<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

if(!empty($_GET['custid']))
{
	$customerid = $_GET['custid'];
	
	//Delete accounts
	$ST = $DB->prepare("UPDATE Accounts SET Deleted=1 WHERE CustomerID=?;");
	$ST->execute(array($customerid));
	$ST->closeCursor();
	
	//Delete customer
	$ST = $DB->prepare("UPDATE Customers SET Deleted=1 WHERE ID=?;");
	$ST->execute(array($customerid));
	$ST->closeCursor();
	
	//Logout all sessions of this customer
	$ST = $DB->prepare("
	
	
	UPDATE

	LoginSession
	INNER
	
	JOIN Accounts
	ON
	
	LoginSession.AccountID= Accounts.ID
	SET
	
	`Closed`=?,`CloseType`=?  WHERE Accounts.CustomerID=? AND `Closed` IS NULL;");
	$ST->execute(array(date("Y-m-d H:i:s"),LoginSession::CLOSETYPE_CUSTOMERDELETED,$customerid));	
}