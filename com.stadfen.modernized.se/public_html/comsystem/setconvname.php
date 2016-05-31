<?php
/*
The script will change the name of a conversation
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
const F_NAME = "name";

$com->RequireLogin();
$com->RequireData(F_CONVID,F_NAME);
$com->RequireDataNumeric(F_CONVID);

$com->Data->Success = FALSE;

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);

$Privileges = Account::GetPrivileges($AccountObj);
if(!$Privileges[Privileges::ALLCONVERSATIONS]) //Unauthorized
	$com->InvalidData();

//Load Conversation data
$ConversationObj = Conversation::GetConversationObj($_POST[F_CONVID]);
if($ConversationObj == NULL)
	$com->InvalidData(); //Conversation was not found
if($ConversationObj->CustomerID != $CustomerObj->ID)
	$com->InvalidData(); //Not valid conv for this customer

Conversation::SetName($ConversationObj->ID,$_POST[F_NAME]);

$com->Data->Success = TRUE;