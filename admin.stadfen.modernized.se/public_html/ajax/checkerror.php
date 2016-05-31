<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

if(!empty($_GET['id']))
{
	$id = $_GET['id'];
	$ST = $DB->prepare("UPDATE ErrorScanResult SET CheckedDone=1 WHERE ID=?;");
	$ST->execute(array($id));
}