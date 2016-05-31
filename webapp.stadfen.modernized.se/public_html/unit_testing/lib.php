<?php

class Page
{
	
	static $time;
	static $start;
	
	public static function setPageSpeedWatching()
	{
		
		self::$time = microtime();
		self::$time = explode(' ', self::$time);
		self::$time = self::$time[1] + self::$time[0];
		self::$start = self::$time;
	}
	
	public static function echoIncludedFiles()
	{
		$included = get_included_files();
		
		foreach ($included as $file)
		{
			echo "$file\n";	
		}
	}
	
	public static function echoPageSpeed()
	{    
	
		self::$time = microtime();
		self::$time = explode(' ', self::$time);
		self::$time = self::$time[1] + self::$time[0];
		$finish = self::$time;
		$total_time = round(($finish - self::$start), 4);
		echo 'Page generated in '.$total_time.' seconds.';
			
	}
}



