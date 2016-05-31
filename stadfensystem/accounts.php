<?php
require_once("database.php");
require_once("password.php");
require_once("email.php");
require_once("customers.php");

class Account
{
	
	
	public static function SetNewPassword($AccountID,$Password)
	{
		global $DB;
		$pwData = Password::CreatePassword($Password);
		$st = $DB->prepare("UPDATE Accounts SET Password=?,Salt=?,NewPasswordTime=? WHERE ID=?;");
		$st->execute(array($pwData->PasswordData,$pwData->SaltData,date("Y-m-d H:i:s"),$AccountID));
	}
	
	public static function CheckPasswordCorrect($AccountObj, $SpecyfiedPassword)
	{
		return (Password::HashSaltPassword($SpecyfiedPassword,$AccountObj->Salt) == $AccountObj->Password);
	}
	
	public static function GetAccountObj($AccountID)
	{
		global $DB;
		$st = $DB->prepare("SELECT * FROM Accounts WHERE ID=? AND `Deleted`=0;");
		$st->execute(array($AccountID));
		
		if($st->rowCount() > 0)
			return $st->fetchObject();
		else
			return NULL;
	}
	
	
	public static function GenerateNewPassword($AccountObj,$boolMailCustomer)
	{
		$pw = Password::RandomizePassword();
		self::SetNewPassword($AccountObj->ID,$pw);
		
		if($boolMailCustomer)
		{
			return Email::SendEmailToAccounByTemplate($AccountObj, Email::SUBJECT_NEWPASSWORD, "newpassword", array("<EMAIL>"=>($AccountObj->EmailAddress),"<NEWPASSWORD>"=>$pw));
		}
		return true;
	}
	
	public static function GetAccountObjByEmail($EmailAddress, $acceptDeleted=FALSE)
	{
		global $DB;
		$st = $DB->prepare("SELECT * FROM Accounts WHERE EmailAddress=?".($acceptDeleted ? "" : " AND `Deleted`=0").";");
		$st->execute(array($EmailAddress));
		
		if($st->rowCount() > 0)
			return $st->fetchObject();
		else
			return NULL;
	}
	
	public static function GetPrivileges($AccountObj)
	{
		global $DB;
		//Check if main account
		$Customer = Customer::GetCustomerObj($AccountObj->CustomerID);
		$isMain = ($Customer->MainAccountID == $AccountObj->ID);
		
		$def = ($isMain) ? 1 : 0;
		
		$ret = array
		(
			0 => 0,
			Privileges::EDITACCOUNTS => $def,
			Privileges::ALLCONVERSATIONS => $def,
			Privileges::COMPANYTEMPLATE => $def,
			Privileges::COMPANYSIGNATURE => $def,
			Privileges::COMPANYTRAFFIC => $def,
		);
		
		if(!$isMain)
		{
			//Load priv
			$ST = $DB->prepare("SELECT Privilege FROM Privileges WHERE AccountID=? AND Value=1");
			$ST->execute(array($AccountObj->ID));
			
			while($a = $ST->fetch(PDO::FETCH_ASSOC))
			{
				$ret[$a['Privilege']] = 1;
			}
		}
		return $ret;
	}
	public static function GetDefaultPrivileges()
	{
		$def =  0;
		
		$ret = array
		(
			0 => 0,
			Privileges::EDITACCOUNTS => $def,
			Privileges::ALLCONVERSATIONS => $def,
			Privileges::COMPANYTEMPLATE => $def,
			Privileges::COMPANYSIGNATURE => $def,
			Privileges::COMPANYTRAFFIC => $def,
		);
		
		return $ret;
	}
	
	public static function SetPrivilege($AccountID, $Privilege, $boolValue)
	{
		global $DB;
		$ST;
		if($boolValue)
		{
			$ST = $DB->prepare("INSERT INTO `Privileges`(`AccountID`, `Privilege`, `Value`) VALUES (?,?,1);");
			$ST->execute(array($AccountID,$Privilege));
		}
		else
		{
			$ST = $DB->prepare("DELETE FROM `Privileges` WHERE `AccountID`=? AND `Privilege`=?;");
			$ST->execute(array($AccountID,$Privilege));
		}
		
		$ST->closeCursor();
	}
	
	public static function CancelAccount($AccountID)
	{
		print_r($AccountID);
		global $DB;
		//Remove account
		$ST = $DB->prepare("UPDATE Accounts SET Deleted=1 WHERE ID=?;");
		$ST->execute(array($AccountID));
		$ST->closeCursor();
		//Logout if online
		$ST = $DB->prepare("UPDATE `LoginSession` SET `Closed`=?,`CloseType`=?  WHERE AccountID=? AND `Closed` IS NULL;");
		$ST->execute(array(date("Y-m-d H:i:s"),LoginSession::CLOSETYPE_ACCOUNTCANCELED,$AccountID));
	}
}


class Privileges
{
	const EDITACCOUNTS = 1;
	const ALLCONVERSATIONS = 2;
	const COMPANYTEMPLATE = 3;
	const COMPANYSIGNATURE = 4;
	const COMPANYTRAFFIC = 5;
	public static $Privilege_Array;
}
Privileges::$Privilege_Array = array(Privileges::EDITACCOUNTS,Privileges::ALLCONVERSATIONS,Privileges::COMPANYTEMPLATE,Privileges::COMPANYSIGNATURE,Privileges::COMPANYTRAFFIC);