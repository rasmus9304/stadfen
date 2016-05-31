<?php

/*
The script will load all necessary information for displaying the Statistics-page for the webb-app

*/

require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/statistics.php");
require_once("../../../stadfensystem/loginsession.php");
$com = new ComSystem();

$com->RequireLogin();

const F_ACCOUNT = "acc";
const F_YEAR = "y";
const F_MONTH = "m";

$com->RequireData(F_ACCOUNT,F_YEAR,F_MONTH);
$com->RequireDataNumeric(F_ACCOUNT,F_YEAR,F_MONTH);

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$Privileges = Account::GetPrivileges($AccountObj);

$com->Data->Privileges = $Privileges;

if(!$Privileges[Privileges::COMPANYTRAFFIC])
	$com->InvalidData();
	
$accountID = $_POST[F_ACCOUNT];
$isAccount = ($accountID != 0);


$ViewAccountObj = NULL;

if($isAccount)
{
	$ViewAccountObj = Account::GetAccountObj($accountID);
	//Check account exists
	if($ViewAccountObj == NULL)
		$com->InvalidData();
	//Check correct customer
	if($ViewAccountObj->CustomerID != $AccountObj->CustomerID)
		$com->InvalidData();
}

$Year = $_POST[F_YEAR];
$Month = $_POST[F_MONTH];

$com->Data->Outgoing = array();
if($isAccount) 
{
	//Populate array with data from account
	$com->Data->Outgoing = Statistics::GetDailyOutgoingForAccount_Month($ViewAccountObj->ID, $Year, $Month);
	$com->Data->CurrentAccountID = $ViewAccountObj->ID;
	$com->Data->CurrentAccountEmailAddress = $ViewAccountObj->EmailAddress;
	$com->Data->CurrentAccountDisplayname = $ViewAccountObj->DisplayName;
}
else
{
	//Data for entire customer
	$com->Data->Outgoing = Statistics::GetDailyOutgoingForCustomer_Month($CustomerObj->ID, $Year, $Month);
	$com->Data->CurrentAccountID = 0;
	$com->Data->CurrentAccountEmailAddress = NULL;
	$com->Data->CurrentAccountDisplayname = NULL;
}

//Load all accounts in customer (for list)
$com->Data->AccountList = array();
$ST = $DB->prepare("SELECT ID,DisplayName,EmailAddress FROM Accounts WHERE CustomerID = ? AND `Deleted` = 0;");
$ST->execute(array($AccountObj->CustomerID));
while($obj = $ST->fetchObject())
{
	$com->Data->AccountList[] = $obj;
}
?>