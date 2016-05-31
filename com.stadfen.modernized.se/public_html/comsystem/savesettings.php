<?php
/*
The script changes settings that can be accessed through the settings-page
And "Action" field is supplied from client informing which setting should be changed
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/conversations.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/password.php");
require_once("../../../stadfensystem/messagetemplate.php");
require_once("../../../stadfensystem/system.php");
$com = new ComSystem();

const F_ACTION = "x";

$com->RequireLogin();
$com->RequireData(F_ACTION);

const ID_NEWPASSWORD = "newpassword";
const ID_TEMPLATENAME_ACCOUNT = "tname_a";
const ID_TEMPLATECONTENT_ACCOUNT = "tcontent_a";
const ID_TEMPLATENAME_CUST = "tname_c";
const ID_TEMPLATECONTENT_CUST = "tcontent_c";
const ID_TEMPLATENAME_EDIT = "tname_e";
const ID_TEMPLATECONTENT_EDIT = "tcontent_e";

$AccountObj = Account::GetAccountObj(LoginSession::GetAccountID());

if($AccountObj == NULL) // Account data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);

if($CustomerObj == NULL) // Customer data gone ?
	$com->End(ComSystem::COM_NOTLOGGEDIN);
	
$Privileges = Account::GetPrivileges($AccountObj);

$action = $_POST[F_ACTION];

$com->Data->Success = FALSE;

switch($action)
{
	case "newpw":
		$com->RequireData("password");
		$password = $_POST['password'];
		
		if(Password::ValidatePassword($password))
		{
			$com->Data->Success = TRUE;
			$com->SetInputStatus(ID_NEWPASSWORD);
			
			Account::SetNewPassword($AccountObj->ID,$password);
			
			//Logout all sessions of this account except the current session
			$ST = $DB->prepare("
			UPDATE
	
			LoginSession
	
			SET
			
			`Closed`=?,`CloseType`=?  WHERE AccountID=? AND `Closed` IS NULL AND NOT ID = ?;");
			$ST->execute(array(date("Y-m-d H:i:s"),LoginSession::CLOSETYPE_PASSWORDCHANGED,$accountid,LoginSession::GetSessionID()));	
			
			$com->Data->Success = TRUE;
		}
		else
		{
			$com->SetInputStatus(ID_NEWPASSWORD,INPUTSTATUS_ERROR,"Ogiltigt lösenord");
		}
		break;
		
	case "setdisplayname":
		$com->RequireData("name");
		$newname = empty($_POST["name"]) ? NULL : $_POST["name"];
		$ST = $DB->prepare("UPDATE `Accounts` SET `DisplayName`=? WHERE ID=?;");
		$ST->execute(array($newname,$AccountObj->ID));
		$ST->closeCursor();
		$com->Data->Success = TRUE;
		break;
		
	case "addtemplate":
		$com->RequireData("ttype","name","content");
		$com->RequireDataNumeric("ttype");
		
		$templatename = $_POST['name'];
		$templatecontent = $_POST['content'];
		
		//Trim the template is too long, remove text from en of string
		$template_max_len = intval(System::SystemVariable("TEMPLATE_MAX_LENGTH"));
		if(strlen($templatecontent) > $template_max_len)
		{
			$templatecontent = substr($templatecontent,0,$template_max_len); 
		}
		
		$templatetype = $_POST['ttype'];
		$isCompanyTemplate = ($templatetype == MessageTemplate::TYPE_CUSTOMER && $Privileges[Privileges::COMPANYTEMPLATE]);
		
		
		if(empty($templatename))
			$com->SetInputStatus(($isCompanyTemplate ? ID_TEMPLATENAME_CUST : ID_TEMPLATENAME_ACCOUNT),INPUTSTATUS_ERROR,"Ogiltigt värde");
		else if(empty($templatecontent))
			$com->SetInputStatus(($isCompanyTemplate ? ID_TEMPLATECONTENT_CUST : ID_TEMPLATECONTENT_ACCOUNT),INPUTSTATUS_ERROR,"Ogiltigt värde");
		else
		{
			$com->SetInputStatus(($isCompanyTemplate ? ID_TEMPLATENAME_CUST : ID_TEMPLATENAME_ACCOUNT));
			$com->SetInputStatus(($isCompanyTemplate ? ID_TEMPLATECONTENT_CUST : ID_TEMPLATECONTENT_ACCOUNT));
			if($isCompanyTemplate)
			{
				//New company template
				$ST = $DB->prepare("INSERT INTO `MessageTemplates` (`CustomerID`,`Title`,`Text`,`CreateTime`) VALUES (?,?,?,?);");
				$ST->execute(array($CustomerObj->ID,$templatename,$templatecontent,date("Y-m-d H:i:s")));
			}
			else
			{
				//New account template
				$ST = $DB->prepare("INSERT INTO `MessageTemplates` (`AccountID`,`Title`,`Text`,`CreateTime`) VALUES (?,?,?,?);");
				$ST->execute(array($AccountObj->ID,$templatename,$templatecontent,date("Y-m-d H:i:s")));
			}
			$com->Data->TemplateID = $DB->lastInsertId();
			$com->Data->Success = TRUE;
		}
		
		
		break;
		
	case "edittemplate":
		$com->RequireData("id","delete");
		$com->RequireDataNumeric("id");
		$TemplateID = $_POST['id'];
		
		if($_POST['delete'])
		{
			if($Privileges[Privileges::COMPANYTEMPLATE])
			{
				$ST = $DB->prepare("UPDATE `MessageTemplates` SET `Deleted` = 1 WHERE ID=? AND (AccountID=? OR CustomerID=?);");
				$ST->execute(array($TemplateID,$AccountObj->ID,$CustomerObj->ID));
				$ST->closeCursor();
			}
			else
			{
				$ST = $DB->prepare("UPDATE `MessageTemplates` SET `Deleted` = 1 WHERE ID=? AND AccountID=? AND CustomerID IS NULL;");
				$ST->execute(array($TemplateID,$AccountObj->ID));
				$ST->closeCursor();
			}
			$com->Data->Success = TRUE;
		}
		else
		{
			$com->RequireData(ID_TEMPLATENAME_EDIT, ID_TEMPLATECONTENT_EDIT);
			$templatename = $_POST[ID_TEMPLATENAME_EDIT];
			$templatecontent = $_POST[ID_TEMPLATECONTENT_EDIT];
			if(empty($templatename))
				$com->SetInputStatus(ID_TEMPLATENAME_EDIT,INPUTSTATUS_ERROR,"Ogiltigt värde");
			else if(empty($templatecontent))
				$com->SetInputStatus(ID_TEMPLATECONTENT_EDIT,INPUTSTATUS_ERROR,"Ogiltigt värde");
			else
			{
				$com->SetInputStatus(ID_TEMPLATENAME_EDIT);
				$com->SetInputStatus(ID_TEMPLATENAME_EDIT);
				
				if($Privileges[Privileges::COMPANYTEMPLATE])
				{
					$ST = $DB->prepare("UPDATE `MessageTemplates` SET `Title`=?,`Text`=? WHERE ID=? AND (AccountID=? OR CustomerID=?);");
					$ST->execute(array($templatename,$templatecontent,$TemplateID,$AccountObj->ID,$CustomerObj->ID));
					$ST->closeCursor();
				}
				else
				{
					$ST = $DB->prepare("UPDATE `MessageTemplates` SET `Title`=?,`Text`=? WHERE ID=? AND AccountID=? AND CustomerID IS NULL;");
					$ST->execute(array($templatename,$templatecontent,$TemplateID,$AccountObj->ID));
					$ST->closeCursor();
				}
				$com->Data->Success = TRUE;
			}
		}
		break;
		
	case "setmysign":
		$com->RequireData("sign");
		$newSign = empty($_POST["sign"]) ? NULL : $_POST["sign"];
		$ST = $DB->prepare("UPDATE `Accounts` SET `Signature`=? WHERE ID=?;");
		$ST->execute(array($newSign,$AccountObj->ID));
		$ST->closeCursor();
		$com->Data->Success = TRUE;
		break;
		
	case "setcustsign":
		if($Privileges[Privileges::COMPANYSIGNATURE])
		{
			$com->RequireData("sign");
			$newSign = $_POST["sign"];
			$ST = $DB->prepare("UPDATE `Customers` SET `Signature`=? WHERE ID=?;");
			$ST->execute(array($newSign,$CustomerObj->ID));
			$ST->closeCursor();
			$com->Data->Success = TRUE;
		}
		break;
		
	case "setcustsignactivation":
		if($Privileges[Privileges::COMPANYSIGNATURE])
		{
			$com->RequireData("active");
			$newstatus = ($_POST["active"] ? 1 : 0);
			$ST = $DB->prepare("UPDATE `Customers` SET `SignatureActive`=? WHERE ID=?;");
			$ST->execute(array($newstatus,$CustomerObj->ID));
			$ST->closeCursor();
			$com->Data->Success = TRUE;
		}
		break;
	case "setmanualsilentmodestatus":
		$com->RequireData("active");
		$newstatus = ($_POST["active"] ? 1 : 0);
		$ST = $DB->prepare("UPDATE `Accounts` SET `SilentMode`=? WHERE ID=?;");
		$ST->execute(array($newstatus,$AccountObj->ID));
		$ST->closeCursor();
		$com->Data->Success = TRUE;
		break;
		
	default:
		$com->Javascript = 'alert("Unknown action");';
		break;
}