<?php


class Admin
{
	const ADMIN_LOGIN_SESSION = "skdjhy5uvkhaAfs";
	
	public static function IsLoggedIn()
	{
		return (isset($_SESSION[self::ADMIN_LOGIN_SESSION]) && is_numeric($_SESSION[self::ADMIN_LOGIN_SESSION]) && $_SESSION[self::ADMIN_LOGIN_SESSION] > 0);
	}
	
	public static function SetLoggedIn($AdminID)
	{
		$_SESSION[self::ADMIN_LOGIN_SESSION] = $AdminID;
	}
	public static function SetLoggedOut()
	{
		self::SetLoggedIn(0);
	}
	
	public static function GetAdminID()
	{
		if(isset($_SESSION[self::ADMIN_LOGIN_SESSION]))
			return $_SESSION[self::ADMIN_LOGIN_SESSION];
		else
			return 0;
	}
}

require_once("admin.php");