<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/administrators.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = FALSE;

if(isset($_GET['id'])) //TODO_ man får itne ta bort sig själv
{
	if($_GET['id'] != Admin::GetAdminID())
	{
		Administrator::Delete($_GET['id']);
		$ajax->Data->Success = TRUE;
	}
}