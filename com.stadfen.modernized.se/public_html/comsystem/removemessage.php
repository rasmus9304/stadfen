<?php
/*
This script removes a message from a conversation
Only messages with error-status can be removed
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

const F_MESSAGEID = "msgid";

$com->RequireLogin();
$com->RequireData(F_MESSAGEID);
$com->RequireDataNumeric(F_MESSAGEID);

$com->Data->Success = FALSE;

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
//Load Message data
$ST = $DB->prepare("SELECT CustomerID,RemoteNumber,`Status` FROM Messages WHERE ID=?;");
$ST->execute(array($_POST[F_MESSAGEID]));
if($ST->rowCount() == 0)
{
	//If fails, just skip, this can be because someone removes the same message twice
	die;
}
$MessageObj = $ST->fetchObject();
$ST->closeCursor();
if($MessageObj->CustomerID != $CustomerObj->ID)
	$com->InvalidData(); //Not valid message for this customer

//Load Conversation data
$ST = $DB->prepare("SELECT ID AS ID, (SELECT Blocked FROM ConversationAccounts WHERE ConversationAccounts.ConversationID = Conversations.ID AND ConversationAccounts.AccountID = ?) AS Blocked FROM Conversations WHERE Conversations.CustomerID=? AND Conversations.Number=?;");
$ST->execute(array($AccountObj->ID,$MessageObj->CustomerID,$MessageObj->RemoteNumber));
if($ST->rowCount() == 0)
	$com->InvalidData();
$ConversationObj = $ST->fetchObject();
if($ConversationObj->Blocked)
	$com->InvalidData(); //User blocked from conv

$ST->closeCursor();
//Check for errorstatus
if($MessageObj->Status == MESSAGESTATUS::DELIVERYFAILED || $MessageObj->Status == MESSAGESTATUS::SENDFAIL)
{
	$ST = $DB->prepare("DELETE FROM Messages WHERE ID = ?;");
	$ST->execute(array($_POST[F_MESSAGEID]));
	$ST = $DB->prepare("UPDATE Conversations SET `LastUpdateTime`=? WHERE ID=?;");
	$ST->execute(array(date("Y-m-d H:i:s"),$ConversationObj->ID));
}
else
{
	$com->InvalidData("Non-removable-message");
}