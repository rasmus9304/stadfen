<?php

class System
{
	public static $Variables = NULL;
	
	public static function LoadVariables()
	{
		global $DB;
		$ST = $DB->prepare("SELECT Name, Value FROM SystemVariables;");
		$ST->execute();
		
		
		self::$Variables = array();
		while($obj = $ST->fetchObject())
		{
			self::$Variables[$obj->Name] = $obj->Value;
		}
	}
	
	public static function SystemVariable($Name)
	{
		if(self::$Variables == NULL)
			self::LoadVariables();
			
		if(isset(self::$Variables[$Name]))
			return self::$Variables[$Name];
		else
			return NULL;
	}
	
	const SYSTEMVAR_STRING = 1;
	const SYSTEMVAR_UINT = 2;
}