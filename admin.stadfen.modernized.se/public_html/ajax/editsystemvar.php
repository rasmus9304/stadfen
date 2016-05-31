<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/system.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = FALSE;
$ajax->Data->Message = "";

print_r($_POST);

if(isset($_POST['id']) && isset($_POST['val']) && is_numeric($_POST['id']))
{
	$id = $_POST['id'];
	$val = $_POST['val'];
	
	$STGet = $DB->prepare("SELECT `DataType` FROM `SystemVariables` WHERE `ID`=?;");
	$STGet->execute(array($id));
	
	if($STGet->rowCount() > 0)
	{
		$obj = $STGet->fetchObject();
		$datatype = $obj->DataType;
		
		
		if($datatype == System::SYSTEMVAR_UINT && (!is_int($val) && !ctype_digit($val)))
		{
			$ajax->Data->Success = FALSE;
			$ajax->Data->Message = "Måste vara heltal större eller lika med 0";
		}
		else
		{
			$STGet->closeCursor();
			$ST = $DB->prepare("UPDATE `SystemVariables` SET `Value`=? WHERE ID=?");
			$ST->execute(array($val,$id));
			
			$ajax->Data->Success = TRUE;
		}
	}
	else
	{
		$ajax->Data->Success = FALSE;
		$ajax->Data->Message = "Kunde inte utföra ändringen, variabeln hittades ej";
	}
}