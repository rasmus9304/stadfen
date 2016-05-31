<?php

/*
The script will change the NICKNAME for a conversation
Every account has its own nickname for every conversation
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
const F_NICKNAME = "nickname";

$com->RequireLogin();
$com->RequireData(F_CONVID,F_NICKNAME);
$com->RequireDataNumeric(F_CONVID);

$com->Data->Success = FALSE;

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);


//Load Conversation data
$ST = $DB->prepare("SELECT ID,`CustomerID`,`Number`, (SELECT Blocked FROM ConversationAccounts WHERE ConversationAccounts.ConversationID = Conversations.ID AND ConversationAccounts.AccountID = ?) AS Blocked FROM Conversations WHERE Conversations.`ID`=?;");
$ST->execute(array($AccountObj->ID,$_POST[F_CONVID]));
if($ST->rowCount() == 0)
	$com->InvalidData();
$ConversationObj = $ST->fetchObject();
if($ConversationObj->CustomerID != $CustomerObj->ID)
	$com->InvalidData(); //Not valid conv for this customer
if($ConversationObj->Blocked)
	$com->InvalidData(); //User blocked from conv

$ST->closeCursor();

Conversation::SetNickname($ConversationObj->ID, $AccountObj->ID, $_POST[F_NICKNAME]);

$com->Data->Success = TRUE;