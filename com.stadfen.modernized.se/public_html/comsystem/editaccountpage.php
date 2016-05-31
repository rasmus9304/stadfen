<?php
/*
This script laod all necessary information the the edit-account page

The edit account page is used both when creating a new account and when editing an existing account
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/loginsession.php");
$com = new ComSystem();
//$com->SetEchoManual();

const F_ACCOUNTID = "accountid";
const F_NEWACCOUNT = "newaccount";


$com->RequireLogin();
$com->RequireData(F_ACCOUNTID,F_NEWACCOUNT);

$com->RequireDataNumeric(F_ACCOUNTID,F_NEWACCOUNT);


$newAccount = $_POST[F_NEWACCOUNT] ? TRUE : FALSE;

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$Privileges = Account::GetPrivileges($AccountObj);


if(!$Privileges[Privileges::EDITACCOUNTS])
	$com->InvalidData();
	
$editingMainAccount = FALSE;
$com->Data->CanEditPrivileges = TRUE;
$com->Data->CanEditConvPrivileges = TRUE;
$com->Data->CanCancelAccount = FALSE;

$EditAccountObj = NULL;
if(!$newAccount)
{
	//An existing account is being edited
	$EditAccountObj = Account::GetAccountObj($_POST[F_ACCOUNTID]);
	
	if(!$EditAccountObj ||$EditAccountObj->CustomerID != $AccountObj->CustomerID)
		$com->InvalidData(); //Target account does not belong to this customer
		
	
	$editingMainAccount = ($EditAccountObj->ID == $CustomerObj->MainAccountID);
	$com->Data->CanEditPrivileges = !$editingMainAccount;
	$com->Data->CanEditConvPrivileges = !$editingMainAccount;
	$com->Data->CanCancelAccount = !$editingMainAccount;
}

$com->Data->AccountData = new stdClass();
$com->Data->IsNewAccount = $newAccount;


//Load conversations for this customer
$ST = $DB->prepare("SELECT Conversations.ID AS ID, Conversations.Number AS Number, Conversations.ConversationName AS ConversationName, (SELECT Blocked FROM ConversationAccounts WHERE ConversationAccounts.ConversationID = Conversations.ID AND ConversationAccounts.AccountID = ?) AS Blocked FROM Conversations WHERE Conversations.CustomerID = ? AND Conversations.Deleted =0");

$ST->execute(array(($newAccount ? 0 : $EditAccountObj->ID),$CustomerObj->ID));
$com->Data->Conversations = array();
while($o = $ST->fetchObject())
{
	$o->Blocked = $o->Blocked ? TRUE : FALSE;
	$com->Data->Conversations[] = $o;
}

//Some more fields which should be sent to client
if($newAccount)
{
	$com->Data->AccountData->Privileges = Account::GetDefaultPrivileges();
	$com->Data->AccountData->EmailAddress = "";
	$com->Data->AccountData->DisplayName = NULL;
	$com->Data->AccountData->ID = 0;
}
else
{
	$com->Data->AccountData->Privileges = Account::GetPrivileges($EditAccountObj);
	$com->Data->AccountData->EmailAddress = $EditAccountObj->EmailAddress;
	$com->Data->AccountData->DisplayName = $EditAccountObj->DisplayName;
	$com->Data->AccountData->ID = $EditAccountObj->ID;
}
