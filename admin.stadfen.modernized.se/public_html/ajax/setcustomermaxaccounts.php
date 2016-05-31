<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

if(!empty($_GET['custid']) && !empty($_GET['maxaccounts']) && is_numeric($_GET['maxaccounts']) && $_GET['maxaccounts'] > 0)
{
	$customerid = $_GET['custid'];
	$maxaccounts = $_GET['maxaccounts'];
	
	//Update customer
	$ST = $DB->prepare("UPDATE Customers SET MaxAccounts=? WHERE ID=?;");
	$ST->execute(array($maxaccounts,$customerid));
}