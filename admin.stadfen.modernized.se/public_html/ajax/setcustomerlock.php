<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

if(!empty($_GET['id']) && isset($_GET['locked']))
{
	$customerid = $_GET['id'];
	$locked = ($_GET['locked']==1);
	$lockStatus = ($locked) ? 1 : 0;
	$loginST = $DB->prepare("UPDATE Customers SET Locked=? WHERE ID=?;");
	$loginST->execute(array($lockStatus,$customerid));
	
	if($locked)
	{
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
		$ST->execute(array(date("Y-m-d H:i:s"),LoginSession::CLOSETYPE_CUSTOMERLOCKED,$customerid));		
	}
}