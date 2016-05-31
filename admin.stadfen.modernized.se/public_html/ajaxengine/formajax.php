<?php

require("ajaxengine.php");



$ajax = new AjaxEngine();

const EMAIL = "email";

if (!filter_var($_POST[EMAIL], FILTER_VALIDATE_EMAIL)) 
    $ajax->SetInputStatus(EMAIL,INPUTSTATUS_ERROR,"Felaktig E-post");
else
	$ajax->SetInputStatus(EMAIL);


print_r($_POST);