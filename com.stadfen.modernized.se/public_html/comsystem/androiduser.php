<?php

/*
This script allows for changes in an accounts properties in the "Google Cloud Messaging"-service
*/

require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/definitions.php");
require_once("../../../stadfensystem/system.php");
require_once("../../../stadfensystem/notificationservice.php");

const F_ACTION = "action";

const F_ACCOUNTID = "accountid";
const F_REGISTRATIONID = "registrationid";
const F_ANDROIDUSERID = "androiduserid";

$com = new ComSystem();

$com->RequireData(F_ACTION);

$Action = $_POST[F_ACTION];

$com->Data->Success = FALSE;

const ACTION_REGISTER = "register";
const ACTION_UNREGISTER = "unregister";
const ACTION_UPDATE = "update";

ob_start();

echo ("===============================================================================\n\r");
echo (date("Y-m-d H:i:s") ."\n\r");

if(isset($_POST))
	print_r($_POST);

$data = ob_get_clean();

$f = fopen("androiduser.txt","a");

fwrite($f,$data);

fclose($f);

switch($Action)
{
	case ACTION_REGISTER:
		$f = fopen("androiduser.txt","a");
		
		fwrite($f,"REGISTER\n\r");
		
		fclose($f);
		$com->Data->AndroidUserID = NULL;
		$com->RequireData(F_REGISTRATIONID);
		$com->RequireLogin();
		$com->Data->AndroidUserID = AndroidNotification::Join(LoginSession::GetAccountID(), $_POST[F_REGISTRATIONID]);
		$com->Data->Success = TRUE;
		break;
	case ACTION_UNREGISTER:
		$com->RequireData(F_ACCOUNTID,F_ANDROIDUSERID);
		$com->RequireDataNumeric(F_ACCOUNTID,F_ANDROIDUSERID);
		AndroidNotification::Leave($_POST[F_ANDROIDUSERID],$_POST[F_ACCOUNTID]);
		$com->Data->Success = TRUE;
		break;
	case ACTION_UPDATE:
		$com->RequireData(F_ACCOUNTID,F_ANDROIDUSERID,F_DEVICETOKEN);
		$com->RequireDataNumeric(F_ACCOUNTID,F_ANDROIDUSERID,F_REGISTRATIONID);
		if(!AndroidNotification::isValidDeviceToken($_POST[F_DEVICETOKEN]));
			$com->InvalidData("Invalid Devicetoken");
		AndroidNotification::Update($_POST[F_ANDROIDUSERID],$_POST[F_ACCOUNTID],$_POST[F_REGISTRATIONID]);
		$com->Data->Success = TRUE;
		break;
	default:
		$com->InvalidData();
}