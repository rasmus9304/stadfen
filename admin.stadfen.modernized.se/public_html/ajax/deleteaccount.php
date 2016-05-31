<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

if(!empty($_GET['accid']))
{
	$accountid = $_GET['accid'];
	$ST = $DB->prepare("UPDATE Accounts SET Deleted=1 WHERE ID=?;");
	$ST->execute(array($accountid));
	
	//Logout all sessions of this account
	$ST = $DB->prepare("
	UPDATE

	LoginSession

	SET
	
	`Closed`=?,`CloseType`=?  WHERE AccountID=? AND `Closed` IS NULL;");
	$ST->execute(array(date("Y-m-d H:i:s"),LoginSession::CLOSETYPE_ACCOUNTDELETED,$accountid));	
}