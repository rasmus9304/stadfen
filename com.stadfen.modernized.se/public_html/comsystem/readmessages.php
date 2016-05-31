<?php
/*
This script will change status of messages from unread to read

Multiple message-ids can be supplied
The message-ids array should be a json-encoded array
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/messages.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/misc.php");
$com = new ComSystem();

const F_MESSAGES = "msgs";
const F_CONVID = "convid";

$com->RequireLogin();
$com->RequireData(F_MESSAGES,F_CONVID);
$com->RequireDataNumeric(F_CONVID);

$com->Data->Success = FALSE;

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$Privileges = Account::GetPrivileges($AccountObj);

$com->Data->Privileges = $Privileges;
	

if(!is_json($_POST[F_MESSAGES]))
	$com->InvalidData();
	
$MessageIDs = json_decode($_POST[F_MESSAGES]);
if(!is_array($MessageIDs))
	$com->InvalidData();
	
if(count($MessageIDs) > 0)
{

	//Make sure integers
	foreach($MessageIDs as $ID)
	{
		if(!is_numeric($ID))
			$com->InvalidData();
	}
	
	//Load Conversation data
	$ST = $DB->prepare("SELECT ID,`CustomerID`,`Number`, (SELECT Blocked FROM ConversationAccounts WHERE ConversationAccounts.ConversationID = Conversations.ID AND ConversationAccounts.AccountID = ?) AS Blocked FROM Conversations WHERE Conversations.`ID`=?;");
	$ST->execute(array($AccountObj->ID,$_POST[F_CONVID]));
	
	if($ST->rowCount() == 0) // invalid conv
		$com->InvalidData();
	
	$ConversationObj = $ST->fetchObject();
	$ST->closeCursor();
	
	if($ConversationObj->Blocked && !$Privileges[Privileges::ALLCONVERSATIONS])
		$com->InvalidData(); //Unautherized
	
	$ST = $DB->prepare("UPDATE `Messages` SET `ReadTime`=? WHERE `CustomerID`=? AND `RemoteNumber`=? AND `ReadTime` IS NULL AND ID IN (". implode(',',$MessageIDs) .") AND `Direction`=?;");
	$ST->execute(array(date("Y-m-d H:i:s"),$ConversationObj->CustomerID,$ConversationObj->Number,MessageDirection::IN));
	$rowsChanged = $ST->rowCount();
	$ST->closeCursor();
	
	
	//Decrease conversation unread
	$ST = $DB->prepare("UPDATE Conversations SET NewMessageCount=NewMessageCount-?, `LastUpdateTime`=? WHERE Conversations.ID = ?;");
	$ST->execute(array($rowsChanged,date("Y-m-d H:i:s"),$ConversationObj->ID));
}
$com->Data->Success = TRUE;