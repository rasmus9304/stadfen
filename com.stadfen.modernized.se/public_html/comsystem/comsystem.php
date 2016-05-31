<?php
/*
Base class for this communications-system
*/
session_start();

ini_set('display_errors', 1);

// Enable error reporting for NOTICES
error_reporting(E_NOTICE);

require_once("ajaxengine/ajaxengine.php");
require_once("messageconsole/messageconsole.php");
require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/database.php");
class ComSystem
{
	private $ended = FALSE;
	private $ajaxObject;
	public $Data;
	
	function __construct()
	{
		$this->ajaxObject = new AjaxEngine();
		$this->ajaxObject->AutoFinish = FALSE;
		$this->Data = new stdClass();
		$this->ajaxObject->Data->EM = NULL;
	}
	
	function __destruct()
	{
		if(!$this->ended)
			$this->_end(self::COM_OK);
	}
	
	public function RequireLogin()
	{
		if(!LoginSession::IsLoggedIn())
		{
			echo("Inte inloggad");
			$this->_end(self::COM_NOTLOGGEDIN);
		}
	}
	
	public function GetOptionalData($field,$default)
	{
		return (isset($_POST[$field]) ? $_POST[$field] : $default);
	}
	
	public function GetOptionalNumericData($field,$default)
	{
		return ((isset($_POST[$field]) && is_numeric($_POST[$field])) ? $_POST[$field] : $default);
	}
	
	
	public function RequireData(...$fields)
	{
		foreach($fields as $field)
		{
			if(!isset($_POST[$field]))
			{
				$this->_end(self::COM_MISSINGDATA);
			}
		}
	}
	
	public function RequireDataNumeric(...$fields)
	{
		foreach($fields as $field)
		{
			if(!is_numeric($_POST[$field]))
			{
				$this->_end(self::COM_INVALIDDATA);
			}
		}
	}
	
	public function InvalidData($errorMessage=NULL)
	{
		$this->ErrorMessage($errorMessage);
		$this->_end(self::COM_INVALIDDATA);
	}
	
	
	private function _end($Code)
	{
		$this->ended = TRUE;
		
		$this->ajaxObject->Data->SC = $Code; //StatusCode
		
		if($Code == self::COM_OK)
			$this->ajaxObject->Data->CD = $this->Data; //ComData, CD contains the user data
		else
			$this->ajaxObject->Data->CD = NULL;
	
		$this->ajaxObject->Finish();
	}
	
	public function End($Code)
	{
		$this->_end($Code);
	}
	
	public function SetInputStatus($ElementID, $Status=INPUTSTATUS_GOOD, $Message=NULL, $Value=NULL)
	{
		$this->ajaxObject->SetInputStatus($ElementID, $Status, $Message, $Value);
	}
	
	public function SetEchoManual()
	{
		$this->ajaxObject->SetEchoManual();
	}
	
	public function SetEchoManualIfAny()
	{
		$this->ajaxObject->SetEchoManualIfAny();
	}
	
	public function EchoDebug($data)
	{
		$this->ajaxObject->EchoDebug($data);
	}
	
	public function ErrorMessage($message)
	{
		$this->ajaxObject->Data->EM = $message;
	}
	
	public function KickUser()
	{
		LoginSession::SetLoggedOut(LoginSession::CLOSETYPE_KICK);
		$this->_end(self::COM_KICK);
	}
	
	const COM_OK = 0;
	const COM_MISSINGDATA = 1;
	const COM_INVALIDDATA = 2; 
	const COM_NOTLOGGEDIN = 3;
	const COM_SYSTEMOFF = 4;
	const COM_KICK = 5;
}

function CONSOLE($msg)
{
	MessageConsole::Log($msg);
}

function COMLOG($msg)
{
	global $DB;
	$ST = $DB->prepare("INSERT INTO `ComLog`(`Time`, `Message`,`Filename`) VALUES (?,?,?);");
	$filepath = $_SERVER["SCRIPT_FILENAME"];
	$exp = explode("/",$filepath);
	$ST->execute(array(date("Y-m-d H:i:s"),$msg,$exp[count($exp)-1]));
}

LoginSession::LogReponse();
