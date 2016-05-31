<?php
/*
This script loads the silent-mode intervals for the silent-mode calendar

The data is loaded from the database and is sent to client
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/misc.php");
$com = new ComSystem();

$com->RequireLogin();

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$ST = $DB->prepare("SELECT * FROM `SilentModeIntervals` WHERE AccountID=?;");
$ST->execute(array($AccountObj->ID)); 

$com->Data->SilentModeIntervals = $ST->fetchAll(PDO::FETCH_OBJ);
$com->Data->Success = TRUE;