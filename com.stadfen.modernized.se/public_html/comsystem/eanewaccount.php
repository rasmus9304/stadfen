<?php
/*
This script is used when saving a new account from the edit-account page
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/misc.php");
$com = new ComSystem();

const F_EMAIL = "eac_email";
const F_PRIV = "priv";
const F_CONVPRIV = "convpriv";

$com->RequireLogin();
$com->RequireData(F_EMAIL,F_PRIV,F_CONVPRIV);

$com->Data->Success = FALSE;

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$Privileges = Account::GetPrivileges($AccountObj);

$com->Data->Privileges = $Privileges;

if(!$Privileges[Privileges::EDITACCOUNTS])
	$com->InvalidData();

$EmailAddress = trim($_POST[F_EMAIL]);

if(!is_json($_POST[F_PRIV]) ||!is_json($_POST[F_CONVPRIV]))
	$com->InvalidData();
	
$PrivData = json_decode($_POST[F_PRIV]);
if(!is_array($PrivData))
	$com->InvalidData();
	
$ConvPrivData = json_decode($_POST[F_CONVPRIV]);
	if(!is_array($ConvPrivData))
	$com->InvalidData();
	
//Get number of accounts in customer
$ST = $DB->prepare("SELECT COUNT(ID) FROM Accounts WHERE CustomerID=? AND `Deleted`=0;");
$ST->execute(array($CustomerObj->ID));
$countinfo = $ST->fetch(PDO::FETCH_NUM);
$AccountCount = $countinfo[0];
$ST->closeCursor();

if((int)$AccountCount >= $CustomerObj->MaxAccounts)
	$com->InvalidData("Du kan inte skapa fler konton");

if (filter_var($EmailAddress, FILTER_VALIDATE_EMAIL))
{
	//Check if the email is taken
	if(Account::GetAccountObjByEmail($EmailAddress,FALSE) === NULL)
	{
		//Create account object
		$ST = $DB->prepare("INSERT INTO `Accounts`(`EmailAddress`, `CustomerID`, `CreateTime`) VALUES (?,?,?);");
		$ST->execute(array($EmailAddress,$CustomerObj->ID, date("Y-m-d H:i:s")));
		
		$NewAccountObj = Account::GetAccountObj($DB->lastInsertId());
		$ST->closeCursor();
		
		if($NewAccountObj == NULL)
			$com->InvalidData("Failure when creating account");
		
		//Create password
		Account::GenerateNewPassword($NewAccountObj,true);
		
		//Privileges
		//Make sure no doubles
		$set_privs = array();
		foreach($PrivData as $priv)
		{
			if(is_object($priv) && isset($priv->Priv) && isset($priv->Val) && isset(Privileges::$Privilege_Array[$priv->Priv]))
			{
				//No need to set priv to false because of the account being new
				if($priv->Val && !in_array($priv->Priv,$set_privs))
				{
					Account::SetPrivilege($NewAccountObj->ID,$priv->Priv,$priv->Val);
					$set_privs[] = $priv->Priv;
				}
			}
		}
		
		//ConversationPrivileges
		//Make sure no doubles
		$set_convs = array();
		foreach($ConvPrivData as $convpriv)
		{
			if(is_object($convpriv) && isset($convpriv->ConversationID) && isset($convpriv->Blocked))
			{
				//Only insert if blocked, otherwise there is no need yet
				if($convpriv->Blocked && !in_array($convpriv->ConversationID,$set_convs))
				{
					$ST = $DB->prepare("INSERT INTO `ConversationAccounts`(`AccountID`, `ConversationID`, `Blocked`) VALUES (?,?,?)");
					$ST->execute(array($NewAccountObj->ID,$convpriv->ConversationID,($convpriv->Blocked ? 1 : 0)));
					$set_convs[] = $convpriv->ConversationID;
				}
			}
		}
		
		$com->Data->NewAccountID = $NewAccountObj->ID;
		$com->Data->Success = TRUE;
	}
	else
	{
		$com->SetInputStatus(F_EMAIL, INPUTSTATUS_ERROR, "Denna E-postadress är upptagen");
	}
}
else
{
	$com->SetInputStatus(F_EMAIL, INPUTSTATUS_ERROR, "Ogiltig E-postadress");
}