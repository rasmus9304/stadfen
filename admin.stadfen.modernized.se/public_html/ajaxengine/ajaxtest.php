<?php

require("ajaxengine.php");



$ajax = new AjaxEngine();

$ajax->Data->bjas = "lol";

$ajax->Data->Name = "Lukas";
$ajax->Data->Age = 21;

echo("Test");

$ajax->Javascript = "alert('Testscript'); alert('hej');";


$ajax->SetInputStatus("email",INPUTSTATUS_ERROR, "Finns redan");
$ajax->SetInputStatus("email");

$ajax->Finish();

const EMAILFIELD = "email";