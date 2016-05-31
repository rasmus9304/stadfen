<?php

require_once("system.php");

class Email
{
	
	const TEMPLATE_DIR = "emailtemplates/";
	const TEMPLATE_EXT = ".txt";
	
	const SUBJECT_NEWPASSWORD = "Nytt lösenord";
	const SUBJECT_NEWPASSWORD_ADMINPANEL = "Nytt lösenord (Administrations-sida)";
	
	public static $DEFAULT_HEADERS = array
	(
		"Content-Type: text/plain; charset=UTF-8"
		
	);
	
	public static function GetHeaders()
	{
		$headers = self::$DEFAULT_HEADERS;
		$headers[] = 'From: '. System::SystemVariable("EMAIL_OUT_SYSTEM_NAME") .' <' . System::SystemVariable("EMAIL_OUT_SYSTEM") .'>';
		
		return $headers;
	}
	
	public static function SendEmailToAccount($AccountObj, $Subject, $Content)
	{
		$headers = self::GetHeaders();
		
		return mail($AccountObj->EmailAddress, $Subject, $Content, implode("\r\n",$headers));
	}
	
	public static function SendEmailToAccounByTemplate($AccountObj, $Subject, $TemplateFileTitle, $TemplateParameters=NULL)
	{
		$content = self::ParseTemplate($TemplateFileTitle,$TemplateParameters);
		return self::SendEmailToAccount($AccountObj,$Subject,$content);
	}
	
	public static function SendEmailToAdmin($AdminObj, $Subject, $Content)
	{
		$headers = self::GetHeaders();
		
		return mail($AdminObj->EmailAddress, $Subject, $Content, implode("\r\n",$headers));
	}
	
	public static function SendEmailToAdminByTemplate($AdminObj, $Subject, $TemplateFileTitle, $TemplateParameters=NULL)
	{
		$content = self::ParseTemplate($TemplateFileTitle,$TemplateParameters);
		return self::SendEmailToAdmin($AdminObj,$Subject,$content);
	}
	
	public static function ParseTemplate($TemplateFileTitle, $TemplateParameters = NULL)
	{
		$txt = file_get_contents(dirname(__FILE__) . "/" .self::TEMPLATE_DIR. $TemplateFileTitle . self::TEMPLATE_EXT);
		
		if(is_array($TemplateParameters))
		{
			$search = array_keys($TemplateParameters);
			$replace = array();
			
			for($i = 0; $i < count($search); $i++)
			{
				$replace[$i] = $TemplateParameters[$search[$i]];
			}
			
			return str_replace($search,$replace,$txt);
		}
		else
			return $txt;
	}
}