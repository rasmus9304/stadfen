<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = false;

print_r($_POST);

if(isset($_POST['customer']) && isset($_POST['email']) && isset($_POST['domailcustomer']))
{
	if(empty($_POST['customer']))
		$ajax->SetInputStatus("customer",INPUTSTATUS_ERROR);
	else if(!is_numeric($_POST['customer']))
		$ajax->SetInputStatus("customer",INPUTSTATUS_ERROR,"Felaktigt värde");
	else if(Customer::GetCustomerObj($_POST['customer']) == NULL)
		$ajax->SetInputStatus("customer",INPUTSTATUS_ERROR,"Finns ingen kund med detta kundnummer");
	else
		$ajax->SetInputStatus("customer",INPUTSTATUS_GOOD);
		
		
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		$ajax->SetInputStatus("email",INPUTSTATUS_ERROR);
	else if(Account::GetAccountObjByEmail(trim($_POST['email'])) !== NULL)
		$ajax->SetInputStatus("email",INPUTSTATUS_ERROR, "Denna E-postadress finns redan i databasen");
	else
		$ajax->SetInputStatus("email",INPUTSTATUS_GOOD);
	
		
	if($ajax->FormularSuccess())
	{

		//Create  Account
		$STAccount = $DB->prepare("INSERT INTO Accounts (EmailAddress, CustomerID, CreateTime) VALUES (?,?,?);");
		$STAccount->execute(array($_POST['email'],$_POST['customer'], date("Y-m-d H:i:s")));
		
		//Build Account Object with necessary properties
		$AccountObj = new stdClass();
		$AccountObj->ID = $DB->lastInsertID();
		$AccountObj->EmailAddress = $_POST['email'];
		
		$STAccount->closeCursor();
		
		Account::GenerateNewPassword($AccountObj, $_POST['domailcustomer']);
		
		$ajax->Data->AccountID = $AccountObj->ID;
	}
}