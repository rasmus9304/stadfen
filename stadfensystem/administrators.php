<?php
require_once("database.php");
require_once("password.php");
require_once("email.php");
require_once("customers.php");

class Administrator
{
	public static function Create($EmailAddress, $Username, $doMailAdmin=FALSE)
	{
		global $DB;
		$ST = $DB->prepare("INSERT INTO `Administrators`(`Username`, `EmailAddress`,  `CreateTime`) VALUES (?,?,?);");
		$ST->execute(array($Username,$EmailAddress, date("Y-m-d H:i:s")));
		$AdminID = $DB->lastInsertId();
		$AdminhObj = self::GetAdminObj($AdminID);
		$ST->closeCursor();
		
		self::GenerateNewPassword($AdminhObj, $doMailAdmin);
		
		return $AdminhObj;
	}
	
	public static function SetNewPassword($AdminID,$Password)
	{
		global $DB;
		$pwData = Password::CreatePassword($Password);
		$st = $DB->prepare("UPDATE Administrators SET Password=?,Salt=?,NewPasswordTime=? WHERE ID=?;");
		$st->execute(array($pwData->PasswordData,$pwData->SaltData,date("Y-m-d H:i:s"),$AdminID));
	}
	
	public static function CheckPasswordCorrect($AdminObj, $SpecyfiedPassword)
	{
		return (Password::HashSaltPassword($SpecyfiedPassword,$AdminObj->Salt) == $AdminObj->Password);
	}
	
	public static function GetAdminObj($AdminID)
	{
		global $DB;
		$st = $DB->prepare("SELECT * FROM Administrators WHERE ID=?;");
		$st->execute(array($AdminID));
		
		if($st->rowCount() > 0)
			return $st->fetchObject();
		else
			return NULL;
	}
	
	
	public static function GenerateNewPassword($AdminObj,$boolMailAdmin)
	{
		$pw = Password::RandomizePassword();
		self::SetNewPassword($AdminObj->ID,$pw);
		
		if($boolMailAdmin)
		{
			return Email::SendEmailToAdminByTemplate($AdminObj, Email::SUBJECT_NEWPASSWORD_ADMINPANEL, "newpassword_admin", array("<EMAIL>"=>($AdminObj->EmailAddress),"<NEWPASSWORD>"=>$pw, "<USERNAME>"=>$AdminObj->Username));
		}
		return true;
	}
	
	public static function GetAdminByEmail($EmailAddress)
	{
		global $DB;
		$st = $DB->prepare("SELECT * FROM Administrators WHERE EmailAddress=?;");
		$st->execute(array($EmailAddress));
		
		if($st->rowCount() > 0)
			return $st->fetchObject();
		else
			return NULL;
	}
	
	public static function GetAdminByUsername($Username)
	{
		global $DB;
		$st = $DB->prepare("SELECT * FROM Administrators WHERE Username=?;");
		$st->execute(array($Username));
		
		if($st->rowCount() > 0)
			return $st->fetchObject();
		else
			return NULL;
	}
	
	public static function Delete($AdminID)
	{
		global $DB;
		$st = $DB->prepare("DELETE FROM Administrators WHERE ID=?;");
		$st->execute(array($AdminID));
	}
}
