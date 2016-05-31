<?php
/*
The script will change a conversations active-status
This property is specific for every account and conversation
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
const F_ACTIVE = "active";

$com->RequireLogin();
$com->RequireData(F_CONVID,F_ACTIVE);
$com->RequireDataNumeric(F_CONVID,F_ACTIVE);

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
	$com->InvalidData(); //Conversation was not found
$ConversationObj = $ST->fetchObject();
if($ConversationObj->CustomerID != $CustomerObj->ID)
	$com->InvalidData(); //Not valid conv for this customer
if($ConversationObj->Blocked)
	$com->InvalidData(); //User blocked from conv

$ST->closeCursor();

Conversation::SetActivation($_POST[F_CONVID], $AccountObj->ID, $_POST[F_ACTIVE]);

$com->Data->Success = TRUE;