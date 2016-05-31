<?php
/*
The script changes a conversations archived-status
Only accounts with the privilege "ALLCONVERSATIONS" (Conversation-admin) may perform this action
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
const F_ARCHIVED = "archived";

$com->RequireLogin();
$com->RequireData(F_CONVID,F_ARCHIVED);
$com->RequireDataNumeric(F_CONVID,F_ARCHIVED);

$com->Data->Success = FALSE;

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);

$Privileges = Account::GetPrivileges($AccountObj);
if(!$Privileges[Privileges::ALLCONVERSATIONS]) //Unautorized 
	$com->InvalidData(); 
$ConversationObj = Conversation::GetConversationObj($_POST[F_CONVID]);
if($ConversationObj === NULL)
	$com->InvalidData();
if($ConversationObj->CustomerID != $CustomerObj->ID)
	$com->InvalidData(); //Not valid conv for this customer

Conversation::SetArchiveStatus($_POST[F_CONVID], $_POST[F_ARCHIVED]);


$com->Data->Success = TRUE;