<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = FALSE;

if(isset($_GET['id']) && isset($_GET['domail']) && is_numeric($_GET['id']))
{
	$accountObj = Account::GetAccountObj($_GET['id']);
	
	if($accountObj !== NULL)
	{
		Account::GenerateNewPassword($accountObj, ($_GET['domail'] != 0));
		$ajax->Data->Success = TRUE;
	}
}