<?php
/*
The script will send a message
Recipents can be supplied either by a conversation-ID or by a series of numbers separated by comma
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/messages.php");
require_once("../../../stadfensystem/definitions.php");
require_once("../../../stadfensystem/system.php");
$com = new ComSystem();

const F_ISCONV = "isconv";
const F_MESSAGE = "message";
const F_SIGNATURE = "signature";

const F_CONV = "convid";
const F_NUMBERS = "numbers";

$com->RequireLogin();
$com->RequireData(F_ISCONV,F_MESSAGE,F_SIGNATURE);
$com->RequireDataNumeric(F_ISCONV);

$isConv = intval($_POST[F_ISCONV]);

$com->Data->Success = FALSE;

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);

$Signature;

//Determine signature
//Check if companysignature is enabled
if($CustomerObj->SignatureActive)
	$Signature = ($CustomerObj->Signature ? ParseMessageCode($CustomerObj->Signature,$AccountObj,$CustomerObj) : ""); //Company signature
else
	$Signature = $_POST[F_SIGNATURE];
	
$FullMessage = $_POST[F_MESSAGE] . (empty($Signature) ? "" : (Definitions::SIGNATUREGLUE . $Signature));

if(strlen($FullMessage) > Definitions::MAX_MESSAGE_LENGTH)
	$com->InvalidData("Message too long"); //Error

$messageID = NULL;

if($isConv) //Whether this message is sent to a conversation, otherwise it's sent to a series of numbers
{
	$com->RequireData(F_CONV);
	$com->RequireDataNumeric(F_CONV);
	
	//Load Conversation
	$ST = $DB->prepare("SELECT *,(SELECT Blocked FROM ConversationAccounts WHERE ConversationAccounts.ConversationID = Conversations.ID AND ConversationAccounts.AccountID=?) AS Blocked FROM Conversations WHERE ID = ?");
	$ST->execute(array($AccountObj->ID,$_POST[F_CONV]));
	if($ST->rowCount() == 0)
		$com->InvalidData(); //Conversation was not found
	$ConversationObj = $ST->fetchObject();
	$ST->closeCursor();
	
	if($ConversationObj->CustomerID != $CustomerObj->ID)
		$com->InvalidData(); //Conversation does not belong to this customer
		
	if($ConversationObj->Blocked)
		$com->InvalidData(); //Current account is blocked from this conversation
	
	//Send the message
	$sendRet = SendOutgoingMessage($FullMessage, $ConversationObj->Number,$AccountObj->ID);
	
	$messageID = $sendRet->messageID;
	
	
	//Load some additional data from the message just created
	$ST = $DB->prepare("SELECT `SendTime` FROM Messages WHERE ID = ?;");
	$ST->execute(array($messageID));
	$MessageObj = $ST->fetchObject();
	$ST->closeCursor();
	
	$com->Data->ConversationID = $sendRet->ConvID;
	$com->Data->MessageID = $messageID;
	$com->Data->SendTime = $MessageObj->SendTime;
	$com->Data->SendSuccess = $sendRet->Success;
	$com->Data->Number = $ConversationObj->Number;
}
else //Message is sent to numbers
{
	$com->RequireData(F_NUMBERS);
	$numberSTR = $_POST[F_NUMBERS];
	$numberSTR = str_replace(";",",",$numberSTR);
	$numbers = explode(',',$numberSTR);
	$standardNumbers = array();
	
	//Parse all numbers to standard-form
	foreach($numbers as $n)
		$standardNumbers[] = Phonenumber::ParseToStandard(trim($n));
	//Remove doublettes
	$standardNumbers = array_unique($standardNumbers);
	
	$com->Data->MessageReturns = array();
	
	//Limit amount of numbers for this request
	$sendCount =  min(count($standardNumbers),(int)System::SystemVariable("MAX_DESTINATION_COUNT"));
	
	//Send message to numbers
	for($i = 0; $i < $sendCount; $i++)
	{
		//Check if blocked
		$standard = $standardNumbers[$i];
		$ST = $DB->prepare("SELECT ConversationAccounts.Blocked AS Blocked FROM Conversations LEFT JOIN ConversationAccounts ON ConversationAccounts.ConversationID=Conversations.ID AND ConversationAccounts.AccountID=? WHERE Conversations.Number=? AND Conversations.CustomerID=?");
		$ST->execute(array($AccountObj->ID,$standard,$CustomerObj->ID));
		if($ST->rowCount() > 0)
		{
			$obj = $ST->fetchObject();
			if($obj->Blocked)
			{
				$sendRet = new stdClass();
				$sendRet->Number = trim($standard);
				$sendRet->Blocked = TRUE;
				$sendRet->Success = FALSE;
				$com->Data->MessageReturns[] = $sendRet;
				continue;
			}
		}
		$ST->closeCursor();
		$sendRet = SendOutgoingMessage($FullMessage, $standard,$AccountObj->ID);
		$sendRet->Number = trim($numbers[$i]);
		$sendRet->Blocked = FALSE;
		$com->Data->MessageReturns[] = $sendRet;
	}
}
sleep(1);
$com->Data->FullMessage = $FullMessage;