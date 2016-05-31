<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

if(!empty($_GET['id']))
{
	$ST = $DB->prepare("UPDATE LoginSession SET `Closed`=?,`CloseType`=?  WHERE LoginSession.ID=? AND `Closed` IS NULL;");
	$ST->execute(array(date("Y-m-d H:i:s"),LoginSession::CLOSETYPE_KICK,$_GET['id']));		
}