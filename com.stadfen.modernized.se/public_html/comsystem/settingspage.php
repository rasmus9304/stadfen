<?php

/*
The script will load all necessary information for displaying the Settings-page
*/

require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/loginsession.php");
$com = new ComSystem();

$com->RequireLogin();

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$Privileges = Account::GetPrivileges($AccountObj);

$com->Data->Privileges = $Privileges;

//Send personal messagetemplates
$com->Data->AccountTemplates = array();
$ST = $DB->prepare("SELECT `ID`,`Title`,`Text` FROM MessageTemplates WHERE `AccountID`=? AND `Deleted` = 0;");
$ST->execute(array($AccountObj->ID));
while($obj = $ST->fetchObject())
{
	$com->Data->AccountTemplates[] = $obj;
}
$ST->closeCursor();

//Send company messagetemplates
$com->Data->CustomerTemplates = array();
if($Privileges[Privileges::COMPANYTEMPLATE])
{
	$ST = $DB->prepare("SELECT `ID`,`Title`,`Text` FROM MessageTemplates WHERE `CustomerID`=? AND `Deleted` = 0;");
	$ST->execute(array($AccountObj->CustomerID));
	while($obj = $ST->fetchObject())
	{
		$com->Data->CustomerTemplates[] = $obj;
	}
	$ST->closeCursor();
}

//Send personal signature
$com->Data->AccountSignature = $AccountObj->Signature;
$com->Data->CompanySignatureEnabled = ($CustomerObj->SignatureActive ? TRUE : FALSE);

//Send company signature
if($Privileges[Privileges::COMPANYSIGNATURE])
	$com->Data->CompanySignature = $CustomerObj->Signature;
else
	$com->Data->CompanySignature = NULL;
//Send Silentmode-data
$com->Data->SilentMode = $AccountObj->SilentMode;
$ST = $DB->prepare("SELECT `ID`, `StartDay`, `StartTime`, `EndDay`, `EndTime` FROM `SilentModeIntervals` WHERE `AccountID`=?");
$ST->execute(array($AccountObj->ID));
$com->Data->SilentModeIntervals = array();
while($obj = $ST->fetchObject())
{
	$com->Data->SilentModeIntervals[] = $obj;
}
$ST->closeCursor();
//Send displayname
$com->Data->DisplayName = $AccountObj->DisplayName;

//Send Silentmode manual status
$com->Data->ManualSilentMode = $AccountObj->SilentMode ? TRUE : FALSE;
//Send Silentmode Calendar info
$ST = $DB->prepare("SELECT * FROM `SilentModeIntervals` WHERE AccountID=?;");
$ST->execute(array($AccountObj->ID)); 
$com->Data->SilentModeIntervals = $ST->fetchAll(PDO::FETCH_OBJ);
$ST->closeCursor();

//Send accountlist
$com->Data->AccountList = array();
$com->Data->AccountCount = 0;
$com->Data->AccountMax = 0;
if($Privileges[Privileges::EDITACCOUNTS])
{
	$ST = $DB->prepare("SELECT ID,DisplayName,EmailAddress FROM Accounts WHERE CustomerID = ? AND `Deleted` = 0;");
	$ST->execute(array($AccountObj->CustomerID));
	$com->Data->AccountCount = $ST->rowCount();
	$com->Data->AccountMax = $CustomerObj->MaxAccounts;
	while($obj = $ST->fetchObject())
	{
		$obj->IsMainAccount = ($obj->ID == $CustomerObj->MainAccountID);
		$com->Data->AccountList[] = $obj;
	}
	
}