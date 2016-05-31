<?php

require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/statistics.php");

require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->InfoArray = array();


if(isset($_POST['customerid']) && isset($_POST['startdate']) && isset($_POST['enddate']))
{
	$ajax->Data->InfoArray = Statistics::GetDailyForCustomer_Error($_POST['customerid'],$_POST['startdate'],$_POST['enddate']);
}