<?php

/*
This is the main loading-script for the system

INPUT PARAMETERS:
	getconvs 	- whether the conversation list should be reloaded
	convid		- The current opened conversation too load messages for, 0 for none
	messagecount- The number of messages requested by client
	aboveid		- This specyfies that only messages with ID lower that this should be sent, this parameter allows the "Load more messages" functionality, specifying 0 will load messages from the bottom
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/definitions.php");
require_once("../../../stadfensystem/messages.php");
require_once("../../../stadfensystem/system.php");

$com = new ComSystem();

$com->RequireLogin();
$com->RequireData("getconvs","convid","messagecount","aboveid","firstlistload");
$com->RequireDataNumeric("getconvs","convid","messagecount","aboveid","firstlistload");

$AccountID = LoginSession::GetAccountID();

$isConvListFirstLoad = $_POST['firstlistload'] ? TRUE : FALSE;

const CONVCAT_ACTIVE = 1;
const CONVCAT_INACTIVE = 2;
const CONVCAT_ARCHIVED = 3;

//Loading newmessage-count from conversationcategories
$STGET = $DB->prepare('SELECT SUM( Conversations.NewMessageCount ) AS NewConvCount, (

CASE 
WHEN Conversations.Archived =1
THEN  3
WHEN ConversationAccounts.Active =1
THEN  1
ELSE 2
END
) AS ConvCategory
FROM Conversations
INNER JOIN Accounts ON Accounts.CustomerID = Conversations.CustomerID AND Accounts.ID = ?
LEFT JOIN ConversationAccounts ON ConversationAccounts.AccountID = Accounts.ID AND ConversationAccounts.ConversationID = Conversations.ID
WHERE Conversations.`Deleted` =0 AND (ConversationAccounts.Blocked = 0 OR ConversationAccounts.Blocked IS NULL) AND Conversations.CustomerID = Accounts.CustomerID
GROUP BY ConvCategory');

$STGET->execute(array($AccountID));

$AccountObj = Account::GetAccountObj($AccountID);

$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

$com->Data->NewCounts = new stdClass();
$com->Data->NewCounts->Active = 0;
$com->Data->NewCounts->Inactive = 0;
$com->Data->NewCounts->Archived = 0;

while($a = $STGET->fetch(PDO::FETCH_ASSOC))
{
	if(!is_numeric($a['NewConvCount']))
		continue;
	if($a['ConvCategory'] == CONVCAT_ACTIVE)
		$com->Data->NewCounts->Active = (int)$a['NewConvCount'];
	else if($a['ConvCategory'] == CONVCAT_INACTIVE)
		$com->Data->NewCounts->Inactive = (int)$a['NewConvCount'];
	else if($a['ConvCategory'] == CONVCAT_ARCHIVED)
		$com->Data->NewCounts->Archived = (int)$a['NewConvCount'];
}

$STGET->closeCursor();


//For loading lists of conversations
$com->Data->ConversationLists = new stdClass();
$com->Data->ConversationLists->Conversations = array();
$com->Data->ConversationLists->DeletedConversations = array();

if($_POST['getconvs'] == 1 )
{
	if($isConvListFirstLoad)
		LoginSession::ResetLastLoadConvList();
	$lastConvListLoad = date("Y-m-d H:i:s", LoginSession::LastLoadConvList()); //Get the last time this operation was made
	
	//The client may also provide its own "lastloadtime"
	if(isset($_POST['servertime']) && is_numeric($_POST['servertime']) && $_POST['servertime'] > 0)
	{
		$specyfiedLastLoadTime = intval($_POST['servertime']);
		$specyfiedLastLoadTime = min($specyfiedLastLoadTime, time()); //Can't be higher than the current time
		$lastConvListLoad = date("Y-m-d H:i:s", $specyfiedLastLoadTime);
	}
	
	$STGET = $DB->prepare('SELECT Conversations.ID AS ConversationID, Conversations.ConversationName AS ConversationName, ConversationAccounts.Nickname AS Nickname, ConversationAccounts.Favorite AS Favorite, Conversations.Number AS Number, Conversations.NewMessageCount AS NewMessageCount, Conversations.Archived AS Archived, ConversationAccounts.Active AS Active, (!ConversationAccounts.Active) AS Inactive,
	(SELECT COUNT(ID) FROM Messages WHERE Messages.ConversationID = Conversations.ID AND (Messages.Status = '. MESSAGESTATUS::SENDFAIL .' OR Messages.Status = '. MESSAGESTATUS::DELIVERYFAILED .')) AS ErrorCount,
	(SELECT COUNT(ID) FROM Messages WHERE Messages.ConversationID = Conversations.ID) AS MessageCount,
	Messages.Content AS LastMessage,
	Conversations.LastMessageTime AS LastMessageTime,
	Messages.Direction AS LastMessageDirection,
	Conversations.LastMessageID AS LastMessageID
	FROM Conversations
	INNER JOIN Accounts ON Accounts.CustomerID = Conversations.CustomerID AND Accounts.ID = ?
	LEFT JOIN ConversationAccounts ON ConversationAccounts.AccountID = Accounts.ID AND ConversationAccounts.ConversationID = Conversations.ID
	LEFT JOIN Messages ON Messages.ID = (SELECT MAX(ID) FROM Messages WHERE Messages.ConversationID = Conversations.ID)
	WHERE Conversations.`Deleted` =0 AND (ConversationAccounts.Blocked = 0 OR ConversationAccounts.Blocked IS NULL) AND Conversations.CustomerID = Accounts.CustomerID AND (Conversations.LastUpdateTime >= ? OR ConversationAccounts.LastUpdateTime >= ?);' );
	
	$STGET->execute(array($AccountID,$lastConvListLoad,$lastConvListLoad));
	$com->Data->ConversationLists->Conversations = $STGET->fetchAll(PDO::FETCH_OBJ);
	$com->Data->ConversationLists->LastUpdateTime = $lastConvListLoad;
	$STGET->closeCursor();
	
	
	//Fetch conversation that have been deleted since last load
	$STGET = $DB->prepare('SELECT DeletedConversations.ConversationID AS ConversationID FROM DeletedConversations WHERE DeletedConversations.CustomerID=? AND DeletedConversations.DeleteTime >= ?;' );
	
	$STGET->execute(array($CustomerObj->ID,$lastConvListLoad));
	$com->Data->ConversationLists->DeletedConversations = $STGET->fetchAll(PDO::FETCH_COLUMN, 0);
	$STGET->closeCursor();
}

//Load messages from conversation
$com->Data->Conversation = new stdClass();
if($_POST['convid'] > 0)
{
	$Conv = Conversation::GetConversationObj($_POST['convid']);
	
	$com->Data->ID = 0;
	
	$com->Data->Conversation->DeniedAccess = FALSE;
	$com->Data->Conversation->Nickname = NULL;
	$com->Data->Conversation->Exists = NULL;
	
	$com->Data->Conversation->Messages = array();
	$com->Data->Conversation->Name = NULL;
	$com->Data->Conversation->Number = NULL;
	
	$com->Data->Conversation->TotalMessageCount = 0;
	
	if($Conv === NULL || $AccountObj->CustomerID != $Conv->CustomerID)
	{
		$com->Data->Conversation->Exists = FALSE;
	}
	else
	{
		$com->Data->Conversation->ID = $Conv->ID;
		$com->Data->Conversation->Exists = TRUE;
			
		//Attempt load of account-specific data for conversation
		$ST = $DB->prepare("SELECT Nickname,Blocked FROM ConversationAccounts WHERE ConversationID = ? AND AccountID = ?;");
		$ST->execute(array($_POST['convid'], $AccountObj->ID));
		
		$com->Data->Conversation->Exists = TRUE;
		if($ST->rowCount() > 0)
		{
			$a = $ST->fetch(PDO::FETCH_ASSOC);
			if(intval($a['Blocked']) > 0)
				$com->Data->Conversation->DeniedAccess = TRUE;
			else
				$com->Data->Conversation->Nickname = $a['Nickname'];
		}
		$ST->closeCursor();
		
			
		//Load Messages if access
		if(!$com->Data->Conversation->DeniedAccess)
		{
			
			$messageCount = max(1,min($_POST['messagecount'],(int)System::SystemVariable("LOADMESSAGES_MAXCOUNT")));
			$aboveID = ($_POST["aboveid"] == 0) ? 9999999999999999 : $_POST["aboveid"];
			//$com->EchoDebug("Hämtar över " . $aboveID);
			$ST = $DB->prepare("SELECT
			
			
				Messages.ID AS ID,
				Messages.Direction AS Direction,
				Messages.Destination AS Destination,
				Messages.Originator AS Originator,
				Messages.Content AS Content,
				Messages.Status AS Status,
				Messages.ErrorMessage AS ErrorMessage,
				Messages.AccountID AS AccountID,
				Messages.CustomerID AS CustomerID,
				Messages.CreateTime AS CreateTime,
				Messages.SendTime AS SendTime,
				Messages.DeliveryTime AS DeliveryTime,
				Messages.ReadTime AS ReadTime,
				Messages.SendAttempts AS SendAttempts,
				Messages.ConcatCount AS ConcatCount,
				Messages.RemoteNumber AS RemoteNumber,
				
				Accounts.EmailAddress AS AccountEmailAddress,
				Accounts.Displayname AS AccountDisplayname,
				Accounts.Deleted AS AccountDeleted
			 
			 FROM Messages 
			 LEFT JOIN Accounts ON Accounts.ID = Messages.AccountID
			 WHERE Messages.ConversationID=? AND Messages.ID <= ? ORDER BY ID DESC LIMIT ". $messageCount .";");
			$ST->execute(array($Conv->ID, $aboveID));
			
			$com->Data->Conversation->Messages = $ST->fetchAll(PDO::FETCH_ASSOC);
			$com->Data->Conversation->Name = $Conv->ConversationName;
			$com->Data->Conversation->Number = $Conv->Number;
			$ST->closeCursor();
			$ST = $DB->prepare("SELECT COUNT(ID) FROM Messages WHERE ConversationID=?;");
			$ST->execute(array($Conv->ID));
			$v = $ST->fetch(PDO::FETCH_NUM);
			$com->Data->Conversation->TotalMessageCount = $v[0];
		}
	}
	
}

//Load Settings
$com->Data->SystemSettings = new stdClass();
$com->Data->SystemSettings->CompanyName =  $CustomerObj->Name;
$com->Data->SystemSettings->DisplayName =  $AccountObj->DisplayName ? $AccountObj->DisplayName : "";
$com->Data->SystemSettings->CompanySignature = $CustomerObj->SignatureActive ? $CustomerObj->Signature : NULL;
$com->Data->SystemSettings->CompanySignatureActive = $CustomerObj->SignatureActive ? TRUE : FALSE;
$com->Data->SystemSettings->MySignature = $AccountObj->Signature;

$com->Data->SystemSettings->Settings = new stdClass();
$com->Data->SystemSettings->Settings->Privileges = Account::GetPrivileges($AccountObj);