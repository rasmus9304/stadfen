<?php

require_once("admin-system.php");

session_start();



if(empty($__admin_checklogin_skip) && !Admin::IsLoggedIn())
{
	header("Location: login.php");
	die;
}

