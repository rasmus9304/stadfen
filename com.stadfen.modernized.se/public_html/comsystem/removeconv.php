<?php

/*
The script will remove a conversation

A conversation can be removed if any of the following conditions are met

1. if the conversation only contains failed messages (error-messages)
2. if the conversation is Archived and the current account has the ALLCONVERSATIONS-privilee (Conversation-Administrator)

This modell allow any user to remove conversations which accidently are created by sending a message to an invalid number,
bur additional privileges are required to remove conversations to a valid number

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



const F_CONVID = "convid";

$com->RequireLogin();
$com->RequireData(F_CONVID);
$com->RequireDataNumeric(F_CONVID);

$com->Data->Success = FALSE;



$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);

$Privileges = Account::GetPrivileges($AccountObj);

//Load Conversation data
$ST = $DB->prepare("SELECT ID,`CustomerID`,`Number`, 'Archived', (SELECT Blocked FROM ConversationAccounts WHERE ConversationAccounts.ConversationID = Conversations.ID AND ConversationAccounts.AccountID = ?) AS Blocked FROM Conversations WHERE Conversations.`ID`=?;");
$ST->execute(array($AccountObj->ID,$_POST[F_CONVID]));
if($ST->rowCount() == 0)
	$com->InvalidData();
$ConversationObj = $ST->fetchObject();
if($ConversationObj->CustomerID != $CustomerObj->ID)
	$com->InvalidData(); //Not valid conv for this customer
if($ConversationObj->Blocked)
	$com->InvalidData(); //User blocked from conv

$ST->closeCursor();
//Check if conversation only contains error
$ST = $DB->prepare("SELECT COUNT(ID) FROM Messages WHERE ConversationID=? AND NOT (`Status` = ".MESSAGESTATUS::SENDFAIL." OR `Status` = ".MESSAGESTATUS::DELIVERYFAILED.");");
$ST->execute(array($ConversationObj->ID));
$a = $ST->fetch(PDO::FETCH_NUM);
$ST->closeCursor();

//And check if conv is archived and user have privileges
$privilege_removable = $Privileges[Privileges::ALLCONVERSATIONS] && $ConversationObj->Archived;


//$a[0] == 0 implies that the conversation did not contain any non-error messages
if($a[0] == 0 || $privilege_removable)
{
	Conversation::Remove($ConversationObj->ID);
	
	$com->Data->Success = TRUE;
}
else
{
	$com->InvalidData("Non-removable conversation");
}