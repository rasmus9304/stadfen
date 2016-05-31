<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

if(!empty($_GET['id']) && isset($_GET['locked']))
{
	$accountid = $_GET['id'];
	$locked = ($_GET['locked']==1);
	$lockStatus = ($locked) ? 1 : 0;
	$loginST = $DB->prepare("UPDATE Accounts SET Locked=? WHERE ID=?;");
	$loginST->execute(array($lockStatus,$accountid));
	
	if($locked)
	{
		//Logout all sessions of this account
		$ST = $DB->prepare("
		UPDATE

		LoginSession

		SET
		
		`Closed`=?,`CloseType`=?  WHERE AccountID=? AND `Closed` IS NULL;");
		$ST->execute(array(date("Y-m-d H:i:s"),LoginSession::CLOSETYPE_ACCOUNTLOCKED,$accountid));		
	}
}