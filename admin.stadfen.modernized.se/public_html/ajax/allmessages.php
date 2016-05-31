<?php

require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/statistics.php");

require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->InfoArray = array();

print_r($_POST);

if(isset($_POST['startdate']) && isset($_POST['enddate']))
{
	$ajax->Data->InfoArray = Statistics::GetDailyForAll($_POST['startdate'],$_POST['enddate']);
}