<?php
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/administrators.php");
$__admin_checklogin_skip = TRUE;
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = false;

if(!empty($_POST['loginname']) && !empty($_POST['loginpassword']))
{
	
	$AdminObj = Administrator::GetAdminByUsername($_POST['loginname']);
	
	if($AdminObj != NULL && Administrator::CheckPasswordCorrect($AdminObj,$_POST['loginpassword']))
	{
		Admin::SetLoggedIn($AdminObj->ID);
		$ajax->Data->Success = true;
	}
}