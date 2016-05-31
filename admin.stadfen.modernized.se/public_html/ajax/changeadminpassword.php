<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/administrators.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = false;


if(isset($_POST['old']) && isset($_POST['new']) && isset($_POST['repeat']))
{
	//Load current admin
	$AdminObj = Administrator::GetAdminObj(Admin::GetAdminID());
	
	if(!Administrator::CheckPasswordCorrect($AdminObj,$_POST['old']))
		$ajax->SetInputStatus("old",INPUTSTATUS_ERROR);
	else
		$ajax->SetInputStatus("old",INPUTSTATUS_GOOD);

	if(empty($_POST['new']))
		$ajax->SetInputStatus("new",INPUTSTATUS_ERROR);
	else
		$ajax->SetInputStatus("new",INPUTSTATUS_GOOD);
		
	if($_POST['new'] != $_POST['repeat'])
		$ajax->SetInputStatus("repeat",INPUTSTATUS_ERROR);
	else
		$ajax->SetInputStatus("repeat",INPUTSTATUS_GOOD);
	
		
	if($ajax->FormularSuccess())
	{
		Administrator::SetNewPassword($AdminObj->ID, $_POST['new']);
		$ajax->Data->Success = TRUE;
	}
}



//5530-2137