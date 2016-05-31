<?php

class Password
{
	public $PasswordData;
	public $SaltData;
	
	const HASH_ALGORITHM = "sha512";
	
	public static $PasswordMinimumLength = 8;
	
	//Skapar ett slumpat SALT och hashar lösenordet kombinerat med detta
	public static function CreatePassword($Password)
	{
		$ret = new Password();
		$ret->SaltData = md5(rand());
		$ret->PasswordData = self::HashSaltPassword($Password,$ret->SaltData);
		return $ret;
	}
	
	public static function HashSaltPassword($Password,$Salt)
	{
		$_temp = $Password.$Salt;
		return hash(self::HASH_ALGORITHM,$_temp);
	}
	
	//Validerar, lösenord måste innehålla MINST: 1 stor bokstav, 1 liten, en siffra, och vara minst ett visst antal tecken långt
	public static function ValidatePassword($Password)
	{
		return (strlen($Password) >= self::$PasswordMinimumLength && preg_match("#[0-9]+#", $Password) && preg_match("#[a-z]+#", $Password) && preg_match("#[A-Z]+#", $Password));
	}
	
	public static function RandomizePassword()
	{
		$NUMS = "0123456789";
		$LOWER = "abcdefghijklmnopqrstuvwxyz";
		$HIGHER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		
		
		$nums = ""; $lower = ""; $higher = "";
		
		//7 of each
		for ($i = 0; $i < 7; $i++) 
			$nums .= $NUMS[rand(0, strlen($NUMS))];
		for ($i = 0; $i < 7; $i++) 
			$lower .= $LOWER[rand(0, strlen($LOWER))];
		for ($i = 0; $i < 7; $i++) 
			$higher .= $HIGHER[rand(0, strlen($HIGHER))];
			
		return str_shuffle($nums . $lower . $higher);
	}
}