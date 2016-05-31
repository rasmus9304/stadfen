<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = FALSE;

if(isset($_GET['id']))
{
	$ST = $DB->prepare("DELETE FROM Messages WHERE ID=?;");
	$ST->execute(array($_GET['id']));
}