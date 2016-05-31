<?php
/*
This script saves calendar-intervals
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
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
const F_DATA = "data";

$com->RequireData(F_DATA);

if(!is_json($_POST[F_DATA]))
	$com->InvalidData();
$Data = json_decode($_POST[F_DATA]);
if(!is_array($Data))
	$com->InvalidData();

//Check validity
foreach($Data as $obj)
{
	if(!is_object($obj))
		$com->InvalidData();
	if(!isset($obj->StartDay) || !isset($obj->StartTime) || !isset($obj->EndDay) || !isset($obj->EndTime))
		$com->InvalidData();
}

//All ok

//Remove all existing intervals
$ST = $DB->prepare("DELETE FROM SilentModeIntervals WHERE AccountID=?");
$ST->execute(array($AccountObj->ID));
$ST->closeCursor();

//Save all intervals
$ST = $DB->prepare("INSERT INTO `SilentModeIntervals`(`AccountID`, `StartDay`, `StartTime`, `EndDay`, `EndTime`) VALUES (?,?,?,?,?)");
foreach($Data as $obj)
{
	$ST->execute(array($AccountObj->ID,$obj->StartDay,$obj->StartTime,$obj->EndDay,$obj->EndTime));
}
$com->Data->Success = TRUE;