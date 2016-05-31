<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/administrators.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = FALSE;

if(isset($_GET['id']) && isset($_GET['domail']) && is_numeric($_GET['id']))
{
	$adminObj = Administrator::GetAdminObj($_GET['id']);
	if($adminObj !== NULL)
	{
		Administrator::GenerateNewPassword($adminObj, ($_GET['domail'] != 0));
		$ajax->Data->Success = TRUE;
	}
}