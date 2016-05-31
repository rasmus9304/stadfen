<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/administrators.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = false;

print_r($_POST);

if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['domailadmin']))
{
	if(empty($_POST['name']))
		$ajax->SetInputStatus("name",INPUTSTATUS_ERROR);
	else if(Administrator::GetAdminByUsername(trim($_POST['name'])) !== NULL)
		$ajax->SetInputStatus("name",INPUTSTATUS_ERROR, "Detta användarnamn finns redan som administratör");
	else
		$ajax->SetInputStatus("name",INPUTSTATUS_GOOD);
		
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		$ajax->SetInputStatus("email",INPUTSTATUS_ERROR);
	else if(Administrator::GetAdminByEmail(trim($_POST['email'])) !== NULL)
		$ajax->SetInputStatus("email",INPUTSTATUS_ERROR, "Denna E-postadress finns redan som administratör");
	else
		$ajax->SetInputStatus("email",INPUTSTATUS_GOOD);
		
	if($ajax->FormularSuccess())
	{
		$doMailAdmin = ($_POST['domailadmin'] ? TRUE : FALSE);
		//Create admin
		$NewAdminObj = Administrator::Create(trim($_POST['email']), trim($_POST['name']), $doMailAdmin);
		
		$ajax->Data->AdminID = $NewAdminObj->ID;
		$ajax->Data->NewPasswordTime = $NewAdminObj->NewPasswordTime;
	}
}



//5530-2137