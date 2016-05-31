<?php

class LoginSession
{
	const LOGIN_SESSION_ACCOUNT = "LOGINSESS1";
	const LOGIN_SESSION_SESSION = "LOGINSESS2";
	const LOGIN_SESSION_LASTRESPONSE = "LOGINSESS3";
	const LOGIN_SESSION_LASTLOAD_CONVLIST = "LOGINSESS4";
	
	const SESSIONTYPE_WEB = 1;
	const SESSIONTYPE_MOBILE = 2;
	
	const STATUS_OPENED = 0;
	const STATUS_CLOSED = 1;
	
	const CLOSETYPE_EXPIRE = 1;
	const CLOSETYPE_LOGOUT = 2;
	const CLOSETYPE_KICK = 3;
	const CLOSETYPE_ACCOUNTCANCELED = 4;
	const CLOSETYPE_CUSTOMERLOCKED = 5;
	const CLOSETYPE_ACCOUNTLOCKED = 6;
	const CLOSETYPE_CUSTOMERDELETED = 7;
	const CLOSETYPE_ACCOUNTDELETED = 8;
	const CLOSETYPE_PASSWORDCHANGED = 9;
	
	public static function IsLoggedIn()
	{
		return (isset($_SESSION[self::LOGIN_SESSION_ACCOUNT]) && is_numeric($_SESSION[self::LOGIN_SESSION_ACCOUNT]) && $_SESSION[self::LOGIN_SESSION_ACCOUNT] > 0);
	}
	
	public static function SetLoggedIn($AccountID, $SessionType)
	{
		global $DB;
		//Create database session
		$ST = $DB->prepare("INSERT INTO `LoginSession`(`Opened`, `OpenIP`, `SessionType`, `Status`, `AccountID`,`LastResponse`) VALUES (?,?,?,?,?,?);");
		$ST->execute(array(date("Y-m-d H:i:s"), ((empty($_SERVER['REMOTE_ADDR'])) ? NULL : $_SERVER['REMOTE_ADDR']), $SessionType, self::STATUS_OPENED, $AccountID,date("Y-m-d H:i:s")));
		$_SESSION[self::LOGIN_SESSION_ACCOUNT] = $AccountID;
		$_SESSION[self::LOGIN_SESSION_SESSION] = $DB->lastInsertId();
		$_SESSION[self::LOGIN_SESSION_LASTRESPONSE] = time();
		$_SESSION[self::LOGIN_SESSION_LASTLOAD_CONVLIST] = 0;
	}
	public static function SetLoggedOut($closetype, $updatedb=TRUE)
	{
		global $DB;
		
		if(!self::IsLoggedIn())
			return;
		
		//Close session in DB
		if($updatedb)
		{
			$ST = $DB->prepare("UPDATE `LoginSession` SET `Closed`=?, `Status`=?,`CloseType`=? WHERE ID = ? AND `Closed` IS NULL;");
			$ST->execute(array(date("Y-m-d H:i:s"), self::STATUS_CLOSED,$closetype, $_SESSION[self::LOGIN_SESSION_SESSION]));
		}
		
		$_SESSION[self::LOGIN_SESSION_ACCOUNT] = 0;
		$_SESSION[self::LOGIN_SESSION_SESSION] = 0;
		$_SESSION[self::LOGIN_SESSION_LASTRESPONSE] = 0;
		$_SESSION[self::LOGIN_SESSION_LASTLOAD_CONVLIST] = 0;
	}
	
	
	public static function GetAccountID()
	{
		if(isset($_SESSION[self::LOGIN_SESSION_ACCOUNT]))
			return $_SESSION[self::LOGIN_SESSION_ACCOUNT];
		else
			return 0;
	}
	
	public static function LogReponse()
	{
		global $DB;
		
		if(isset($_SESSION[self::LOGIN_SESSION_SESSION]))
		{
			$ST = $DB->prepare("UPDATE `LoginSession` SET `LastResponse`=?,`RequestCount`=`RequestCount`+1  WHERE ID=? AND `Closed` IS NULL;");
			$ST->execute(array(date("Y-m-d H:i:s"), $_SESSION[self::LOGIN_SESSION_SESSION]));
			if($ST->rowCount() == 0) //Session has been closed
				self::SetLoggedOut(self::CLOSETYPE_EXPIRE);
			$_SESSION[self::LOGIN_SESSION_LASTRESPONSE] = time();
		}
	}
	
	public static function GetSessionID()
	{
		return $_SESSION[self::LOGIN_SESSION_SESSION];
	}
	
	//Returns the lastloadtime and updates it
	public static function LastLoadConvList()
	{
		$ret = $_SESSION[self::LOGIN_SESSION_LASTLOAD_CONVLIST];
		$_SESSION[self::LOGIN_SESSION_LASTLOAD_CONVLIST] = time();
		return $ret;
	}
	
	public static function ResetLastLoadConvList()
	{
		$_SESSION[self::LOGIN_SESSION_LASTLOAD_CONVLIST] = 0;
	}
}

$loginsessiontype_labels = array
(
	LoginSession::SESSIONTYPE_MOBILE => "Mobil",
	LoginSession::SESSIONTYPE_WEB => "Webb",
);

$loginsessionclosetype_labels = array
(
	LoginSession::CLOSETYPE_EXPIRE => "Utgått",
	LoginSession::CLOSETYPE_LOGOUT => "Utloggning",
	LoginSession::CLOSETYPE_KICK => "Kickad",
	LoginSession::CLOSETYPE_ACCOUNTCANCELED => "Kontot avslutat",
	LoginSession::CLOSETYPE_CUSTOMERLOCKED => "Företagskunden låst",
	LoginSession::CLOSETYPE_ACCOUNTLOCKED => "Kontot låst",
	LoginSession::CLOSETYPE_CUSTOMERDELETED => "Företagskunden togs bort",
	LoginSession::CLOSETYPE_ACCOUNTDELETED => "Kontot togs bort",
	LoginSession::CLOSETYPE_PASSWORDCHANGED => "Lösenordet ändrat",
);