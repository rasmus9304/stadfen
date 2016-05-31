<?php
/*
This script is used when an existing account is edited
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/password.php");
require_once("../../../stadfensystem/messagetemplate.php");
require_once("../../../stadfensystem/misc.php");
$com = new ComSystem();

const F_ACTION = "x";
const F_ACC = "acc";

$com->RequireLogin();
$com->RequireData(F_ACTION,F_ACC);
$com->RequireDataNumeric(F_ACC);


$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$Privileges = Account::GetPrivileges($AccountObj);

if(!$Privileges[Privileges::EDITACCOUNTS])
	$com->InvalidData();
$EditAccountObj = Account::GetAccountObj($_POST[F_ACC]);

//Make sure account belongs to customer
if(!$EditAccountObj ||$EditAccountObj->CustomerID != $AccountObj->CustomerID)
	$com->InvalidData();
	
$editingMainAccount = ($EditAccountObj->ID == $CustomerObj->MainAccountID);


$action = $_POST[F_ACTION];

$com->Data->Succes = FALSE;

//Actions vary between editing privileges and editing conversation-privileges, and generating a new password for an account
switch($action)
{
	case "editpriv":
		//Privileges should be sent to server as a json-encoded array of privileges objects. 
		//These objects have the following structure: { $Priv, $Val }, defining whether the account should have the privilege or not
		//The data will be parsed and validated, assuring correct structure of data
		$com->RequireData("priv");
		if(!is_json($_POST["priv"]))
			$com->InvalidData();
		$PrivData = json_decode($_POST["priv"]);
		if(!is_array($PrivData))
			$com->InvalidData();
		if($editingMainAccount)
			$com->InvalidData(); // Privileges of main account may not be edited
			
		$set_privs = array();
		foreach($PrivData as $priv)
		{
			if(is_object($priv) && isset($priv->Priv) && isset($priv->Val) && isset(Privileges::$Privilege_Array[$priv->Priv]))
			{
				if(!in_array($priv->Priv,$set_privs)) //To avoid doubles (check if already set)
				{
					Account::SetPrivilege($EditAccountObj->ID,$priv->Priv,$priv->Val);
					$set_privs[] = $priv->Priv; //To avoid doubles (store in list)
				}
			}
		}
		$com->Data->Success = TRUE;
		break;
		
	case "editconvpriv":
		//Conversation-Privileges should be sent to server as a json-encoded array of coversation-privileges objects. 
		//These objects have the following structure: { $ConversationID, $Blocked }, defining whether the conversation is blocked for the account
		//The data will be parsed and validated, assuring correct structure of data
		$com->RequireData("convpriv");
		if(!is_json($_POST["convpriv"]))
			$com->InvalidData();
		$ConvPrivData = json_decode($_POST["convpriv"]);
		if(!is_array($ConvPrivData))
			$com->InvalidData();
		if($editingMainAccount)
			$com->InvalidData();
			
		$set_convprivs = array();
		$set_convs = array();
		//Load all existing ConversationAccount-objects for this account
		$ST = $DB->prepare("SELECT ConversationID FROM ConversationAccounts WHERE AccountID=?;");
		$ST->execute(array($EditAccountObj->ID));
		$existing_array = array();
		while($a = $ST->fetch(PDO::FETCH_NUM)) //Add to accesible array
			$existing_array[] = $a[0];
		$ST->closeCursor();
		foreach($ConvPrivData as $convpriv)
		{
			if(is_object($convpriv) && isset($convpriv->ConversationID) && isset($convpriv->Blocked) && !in_array($convpriv->ConversationID,$set_convs))
			{
				//Update if exists
				if(in_array($convpriv->ConversationID, $existing_array))
				{
					$ST = $DB->prepare("UPDATE `ConversationAccounts` SET `Blocked`=? WHERE `AccountID`=? AND `ConversationID`=?");
					$ST->execute(array(($convpriv->Blocked ? 1 : 0),$EditAccountObj->ID,$convpriv->ConversationID));
				}
				else if($convpriv->Blocked) //Otherwise, insert only if blocked, because unblocked is default
				{
					$ST = $DB->prepare("INSERT INTO `ConversationAccounts`(`AccountID`, `ConversationID`, `Blocked`) VALUES (?,?,?)");
					$ST->execute(array($EditAccountObj->ID,$convpriv->ConversationID,($convpriv->Blocked ? 1 : 0)));
				}
				$set_convs[] = $convpriv->ConversationID;
			}
		}
		$com->Data->Success = TRUE;
		break;
		
	case "generatepass":
		Account::GenerateNewPassword($EditAccountObj,TRUE);
		$com->Data->Success = TRUE;
		break;
		
	default:
		$com->InvalidData("Unknown action");
		break;
}