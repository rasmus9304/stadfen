<?php

/*

This script will load a list of available templates
It will load all customer-templates (company-templates) and account-templates

For each returned template, a field is supplied which informs the client whether the template is a customer-template or not


*/

require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/loginsession.php");
$com = new ComSystem();

$com->RequireLogin();

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$ST = $DB->prepare("SELECT ID,Title,CustomerID FROM MessageTemplates WHERE (AccountID=? OR CustomerID=?) AND Deleted=0;");
$ST->execute(array($AccountObj->ID,$AccountObj->CustomerID)); 

$com->Data->Templates = array();
while($obj = $ST->fetchObject())
{
	$temp = new stdClass();
	$temp->ID = $obj->ID;
	$temp->Title = $obj->Title;
	$temp->IsCustomerTemplate = ($obj->CustomerID != NULL);
	
	$com->Data->Templates[] = $temp;
}
$com->Data->Success = TRUE;