<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/messages.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = FALSE;

if(isset($_POST['id']) && isset($_POST['virtualnumber']) && is_numeric($_POST['id']))
{
	$number = Phonenumber::ParseToStandard($_POST['virtualnumber']);
	
	$ST = $DB->prepare("UPDATE Customers SET VirtualNumber=? WHERE ID=?;");
	$ST->execute(array($number,$_POST['id']));
	
	$ajax->Data->VirtualNumber = Phonenumber::GetDisplayStyle($number);
	$ajax->Data->Success = TRUE;
}