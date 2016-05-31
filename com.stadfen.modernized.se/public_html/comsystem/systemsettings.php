<?php

/*

The script will load settings for the client

*/

require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/definitions.php");
require_once("../../../stadfensystem/system.php");
$com = new ComSystem();

$com->RequireLogin();

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	

$com->Data->SIGNATUREGLUE = Definitions::SIGNATUREGLUE;
$com->Data->WEBAPP_UPDATE_PERIOD = System::SystemVariable("WEBAPP_UPDATE_PERIOD");
$com->Data->MOBILEAPP_UPDATE_PERIOD = System::SystemVariable("MOBILEAPP_UPDATE_PERIOD");
$com->Data->MAX_DESTINATION_COUNT = System::SystemVariable("MAX_DESTINATION_COUNT");
$com->Data->SIGNATURE_MAX_LENGTH = System::SystemVariable("SIGNATURE_MAX_LENGTH");
$com->Data->TEMPLATE_MAX_LENGTH = System::SystemVariable("TEMPLATE_MAX_LENGTH");
$com->Data->LOADMESSAGES_INITCOUNT = System::SystemVariable("LOADMESSAGES_INITCOUNT");
$com->Data->LOADMESSAGES_ADDITIONALCOUNT = System::SystemVariable("LOADMESSAGES_ADDITIONALCOUNT");
$com->Data->MAX_MESSAGE_LENGTH = Definitions::MAX_MESSAGE_LENGTH;

$com->Data->CompanyName =  $CustomerObj->Name;
$com->Data->EmailAddress =  $AccountObj->EmailAddress ? $AccountObj->EmailAddress : "";
$com->Data->DisplayName =  $AccountObj->DisplayName ? $AccountObj->DisplayName : "";
$com->Data->CompanySignature = $CustomerObj->SignatureActive ? $CustomerObj->Signature : NULL;
$com->Data->CompanySignatureActive = $CustomerObj->SignatureActive ? TRUE : FALSE;
$com->Data->MySignature = $AccountObj->Signature;
$com->Data->Privileges = Account::GetPrivileges($AccountObj);

$com->Data->ServerTime = time();