<?php

/*

This script will load a template by supplied Template ID, 
and making sure the template belongs to the customer or account

*/

require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/loginsession.php");
$com = new ComSystem();
$com->Data->Success = FALSE;
$com->RequireLogin();

const F_TEMPLATE = "tid";

$com->RequireData(F_TEMPLATE);
$com->RequireDataNumeric(F_TEMPLATE);

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);

$TemplateID = $_POST[F_TEMPLATE];
	
$ST = $DB->prepare("SELECT * FROM MessageTemplates WHERE ID = ? AND (AccountID=? OR CustomerID=?);");
$ST->execute(array($TemplateID,$AccountObj->ID,$AccountObj->CustomerID));

if($ST->rowCount() == 0)
	$com->InvalidData();

$com->Data->Template = $ST->fetchObject();
$com->Data->Success = TRUE;