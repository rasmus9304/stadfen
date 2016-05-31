<?php

require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/statistics.php");

require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();
$ajax->Data->ErrorArray = array();

$ST = $DB->prepare("SELECT * FROM ErrorScanResult WHERE CheckedDone=0;");
$ST->execute();

$ajax->Data->ErrorCount = $ST->rowCount();
if(empty($_GET['getcount']) || $_GET['getcount']==1)
{
	$ajax->Data->ErrorArray = $ST->fetchAll();
}