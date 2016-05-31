<?php
/*
This script is used when cancelling an account
This action is performed by someone with the privilege "EDITACCOUNT" (Account-administrator)
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/password.php");
require_once("../../../stadfensystem/messagetemplate.php");
require_once("../../../stadfensystem/misc.php");
$com = new ComSystem();

const F_ACC = "acc";

$com->RequireLogin();
$com->RequireData(F_ACC);
$com->RequireDataNumeric(F_ACC);

$com->Data->Success = FALSE;


$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$Privileges = Account::GetPrivileges($AccountObj);

if(!$Privileges[Privileges::EDITACCOUNTS])
	$com->InvalidData();
$EditAccountObj = Account::GetAccountObj($_POST[F_ACC]);

if(!$EditAccountObj ||$EditAccountObj->CustomerID != $AccountObj->CustomerID)
	$com->InvalidData();
$editingMainAccount = ($EditAccountObj->ID == $CustomerObj->MainAccountID);

if($editingMainAccount)
	$com->InvalidData();

//Cancel
Account::CancelAccount($EditAccountObj->ID);

$com->Data->Success = TRUE;