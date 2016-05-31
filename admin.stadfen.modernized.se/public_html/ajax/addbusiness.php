<?php
session_start();
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");

$ajax = new AjaxEngine();

$ajax->Data->Success = false;

print_r($_POST);

if(isset($_POST['orgnr']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['domailcustomer']) && isset($_POST['maxaccounts']))
{
	if(empty($_POST['orgnr']))
		$ajax->SetInputStatus("orgnr",INPUTSTATUS_ERROR);
	else
		$ajax->SetInputStatus("orgnr",INPUTSTATUS_GOOD);
		
	if(empty($_POST['name']))
		$ajax->SetInputStatus("name",INPUTSTATUS_ERROR);
	else
		$ajax->SetInputStatus("name",INPUTSTATUS_GOOD);
		
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		$ajax->SetInputStatus("email",INPUTSTATUS_ERROR);
	else if(Account::GetAccountObjByEmail(trim($_POST['email'])) !== NULL)
		$ajax->SetInputStatus("email",INPUTSTATUS_ERROR, "Denna E-postadress finns redan i databasen");
	else
		$ajax->SetInputStatus("email",INPUTSTATUS_GOOD);
	
	$maxaccounts = trim($_POST['maxaccounts']);
	if(!is_numeric($maxaccounts) || $maxaccounts < 1)
		$ajax->SetInputStatus("maxaccounts",INPUTSTATUS_ERROR);
	else
		$ajax->SetInputStatus("maxaccounts",INPUTSTATUS_GOOD);
		
	if($ajax->FormularSuccess())
	{
		//Create Customer
		$STCustomer = $DB->prepare("INSERT INTO Customers (CorporateNumber,Name,MaxAccounts,CreateTime,IncomingKey)VALUES(?,?,?,?,?);)");
		
		$STCustomer->execute(array($_POST['orgnr'],$_POST['name'],(int)$maxaccounts, date("Y-m-d H:i:s"), md5(rand())));
		$CustomerID = $DB->lastInsertID();
		$STCustomer->closeCursor();
		
		//Create Main Account
		$STAccount = $DB->prepare("INSERT INTO Accounts (EmailAddress, CustomerID, CreateTime) VALUES (?,?,?);");
		$STAccount->execute(array($_POST['email'],$CustomerID, date("Y-m-d H:i:s")));
		
		//Build Account Object with necessary properties
		$AccountObj = new stdClass();
		$AccountObj->ID = $DB->lastInsertID();
		$AccountObj->EmailAddress = $_POST['email'];
		
		$STAccount->closeCursor();
		
		//Set this account as the main account
		$STUpdateMainAccount = $DB->prepare("UPDATE Customers SET MainAccountID=? WHERE ID=?;");
		$STUpdateMainAccount->execute(array($AccountObj->ID,$CustomerID));
		$STUpdateMainAccount->closeCursor();
		
		Account::GenerateNewPassword($AccountObj, $_POST['domailcustomer']);
		
		$ajax->Data->CustomerID =$CustomerID;
	}
}