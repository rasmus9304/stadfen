<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

if(!empty($_GET['accid']) && isset($_GET['custid']))
{
	$accountid = $_GET['accid'];
	$custmerid = $_GET['custid'];
	$ST = $DB->prepare("UPDATE Customers SET MainAccountID=? WHERE ID=?;");
	$ST->execute(array($accountid,$custmerid));
}