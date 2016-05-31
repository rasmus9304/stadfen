<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();



echo("tja");

$ajax->Javascript = "alert('lol')";

sleep(2);