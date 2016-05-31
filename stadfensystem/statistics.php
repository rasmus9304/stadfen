<?php
require_once("database.php");
require_once(dirname(__FILE__) ."/messages.php");

class Statistics
{
	public static function GetDailyForAll($startDate,$endDate)
	{
		global $DB;
		
		$startDate2 = $startDate . " 00:00:00";
		$endDate2 = $endDate . " 23:59:59";
		
		$ST = $DB->prepare("SELECT COUNT(ID) AS MessageCount, DATE(CreateTime) AS SendDate FROM Messages WHERE NOT (`Status` = ". MESSAGESTATUS::CREATED ." OR `Status`=". MESSAGESTATUS::SENDFAIL .") AND CreateTime >= ? AND CreateTime <= ? GROUP BY SendDate;");
		$ST->execute(array($startDate2, $endDate2));
		
		$ret = array();
		$startime = strtotime($startDate);
		$endtime = strtotime($endDate);
		
		//Create ret array
		$curtime = $startime;
		while($curtime <= $endtime)
		{
			$tmp = new stdClass();
			$tmp->MessageCount = 0;
			$tmp->SendDate = date("Y-m-d",$curtime);
			$ret[] = $tmp;
			$curtime = strtotime('+1 day', $curtime);
			
			//echo("ENDTIME: " . $endtime . " CURTIME: " .$curtime . "\n");
		}
		
		//Add elements that are set
		
		while($a = $ST->fetchObject())
		{
			for($i = 0; $i < count($ret); $i++)
			{
				if($a->SendDate == $ret[$i]->SendDate)
				{
					$ret[$i]->MessageCount = $a->MessageCount;
					break;
				}
			}
		}
		
		
		return $ret;
	}
	
	public static function GetDailyForAll_Error($startDate,$endDate)
	{
		global $DB;
		
		$startDate2 = $startDate . " 00:00:00";
		$endDate2 = $endDate . " 23:59:59";
		
		$ST = $DB->prepare("SELECT COUNT(ID) AS MessageCount, DATE(CreateTime) AS SendDate FROM Messages WHERE (`Status` = ". MESSAGESTATUS::CREATED ." OR `Status`=". MESSAGESTATUS::SENDFAIL .") AND CreateTime >= ? AND CreateTime <= ? GROUP BY SendDate;");
		$ST->execute(array($startDate2, $endDate2));
		
		$ret = array();
		$startime = strtotime($startDate);
		$endtime = strtotime($endDate);
		
		//Create ret array
		$curtime = $startime;
		while($curtime <= $endtime)
		{
			$tmp = new stdClass();
			$tmp->MessageCount = 0;
			$tmp->SendDate = date("Y-m-d",$curtime);
			$ret[] = $tmp;
			$curtime = strtotime('+1 day', $curtime);
			
			//echo("ENDTIME: " . $endtime . " CURTIME: " .$curtime . "\n");
		}
		
		//Add elements that are set
		
		while($a = $ST->fetchObject())
		{
			for($i = 0; $i < count($ret); $i++)
			{
				if($a->SendDate == $ret[$i]->SendDate)
				{
					$ret[$i]->MessageCount = $a->MessageCount;
					break;
				}
			}
		}
		
		
		return $ret;
	}
	
	public static function GetDailyOutgoingForAll($startDate,$endDate)
	{
		global $DB;
		
		$startDate2 = $startDate . " 00:00:00";
		$endDate2 = $endDate . " 23:59:59";
		
		$ST = $DB->prepare("SELECT COUNT(ID) AS MessageCount, DATE(CreateTime) AS SendDate FROM Messages WHERE Direction=". MESSAGEDIRECTION::OUT ." AND NOT (`Status` = ". MESSAGESTATUS::CREATED ." OR `Status`=". MESSAGESTATUS::SENDFAIL .") AND CreateTime >= ? AND CreateTime <= ? GROUP BY SendDate;");
		$ST->execute(array($startDate2, $endDate2));
		
		$ret = array();
		$startime = strtotime($startDate);
		$endtime = strtotime($endDate);
		
		//Create ret array
		$curtime = $startime;
		while($curtime <= $endtime)
		{
			$tmp = new stdClass();
			$tmp->MessageCount = 0;
			$tmp->SendDate = date("Y-m-d",$curtime);
			$ret[] = $tmp;
			$curtime = strtotime('+1 day', $curtime);
			
			//echo("ENDTIME: " . $endtime . " CURTIME: " .$curtime . "\n");
		}
		
		//Add elements that are set
		
		while($a = $ST->fetchObject())
		{
			for($i = 0; $i < count($ret); $i++)
			{
				if($a->SendDate == $ret[$i]->SendDate)
				{
					$ret[$i]->MessageCount = $a->MessageCount;
					break;
				}
			}
		}
		
		
		return $ret;
	}
	
	public static function GetDailyOutgoingForAll_Month($year,$month)
	{
		$time = strtotime($year . "-" . $month . "-05");
		return self::GetDailyOutgoingForAll(date("Y-m-01", $time), date("Y-m-t", $time));
	}
	public static function GetDailyForCustomer($customerID,$startDate,$endDate)
	{
		global $DB;
		
		$startDate2 = $startDate . " 00:00:00";
		$endDate2 = $endDate . " 23:59:59";
		

		$ST = $DB->prepare("SELECT COUNT(ID) AS MessageCount, DATE(CreateTime) AS SendDate FROM Messages WHERE CustomerID=? AND NOT (`Status` = ". MESSAGESTATUS::CREATED ." OR `Status`=". MESSAGESTATUS::SENDFAIL .") AND CreateTime >= ? AND CreateTime <= ? GROUP BY SendDate;");
		$ST->execute(array($customerID,$startDate2, $endDate2));
		
		$ret = array();
		$startime = strtotime($startDate);
		$endtime = strtotime($endDate);
		
		//Create ret array
		$curtime = $startime;
		while($curtime <= $endtime)
		{
			$tmp = new stdClass();
			$tmp->MessageCount = 0;
			$tmp->SendDate = date("Y-m-d",$curtime);
			$ret[] = $tmp;
			$curtime = strtotime('+1 day', $curtime);
			
			//echo("ENDTIME: " . $endtime . " CURTIME: " .$curtime . "\n");
		}
		
		//Add elements that are set
		
		while($a = $ST->fetchObject())
		{
			for($i = 0; $i < count($ret); $i++)
			{
				if($a->SendDate == $ret[$i]->SendDate)
				{
					$ret[$i]->MessageCount = $a->MessageCount;
					break;
				}
			}
		}
		
		
		return $ret;
	}
	
	public static function GetDailyForCustomer_Error($customerID,$startDate,$endDate)
	{
		global $DB;
		
		$startDate2 = $startDate . " 00:00:00";
		$endDate2 = $endDate . " 23:59:59";
		

		$ST = $DB->prepare("SELECT COUNT(ID) AS MessageCount, DATE(CreateTime) AS SendDate FROM Messages WHERE CustomerID=? AND (`Status` = ". MESSAGESTATUS::CREATED ." OR `Status`=". MESSAGESTATUS::SENDFAIL .") AND CreateTime >= ? AND CreateTime <= ? GROUP BY SendDate;");
		$ST->execute(array($customerID,$startDate2, $endDate2));
		
		$ret = array();
		$startime = strtotime($startDate);
		$endtime = strtotime($endDate);
		
		//Create ret array
		$curtime = $startime;
		while($curtime <= $endtime)
		{
			$tmp = new stdClass();
			$tmp->MessageCount = 0;
			$tmp->SendDate = date("Y-m-d",$curtime);
			$ret[] = $tmp;
			$curtime = strtotime('+1 day', $curtime);
			
			//echo("ENDTIME: " . $endtime . " CURTIME: " .$curtime . "\n");
		}
		
		//Add elements that are set
		
		while($a = $ST->fetchObject())
		{
			for($i = 0; $i < count($ret); $i++)
			{
				if($a->SendDate == $ret[$i]->SendDate)
				{
					$ret[$i]->MessageCount = $a->MessageCount;
					break;
				}
			}
		}
		
		
		return $ret;
	}
	
	public static function GetDailyOutgoingForCustomer($customerID,$startDate,$endDate)
	{
		global $DB;
		
		$startDate2 = $startDate . " 00:00:00";
		$endDate2 = $endDate . " 23:59:59";
		

		$ST = $DB->prepare("SELECT COUNT(ID) AS MessageCount, DATE(CreateTime) AS SendDate FROM Messages WHERE CustomerID=? AND Direction=". MESSAGEDIRECTION::OUT ." AND NOT (`Status` = ". MESSAGESTATUS::CREATED ." OR `Status`=". MESSAGESTATUS::SENDFAIL .") AND CreateTime >= ? AND CreateTime <= ? GROUP BY SendDate;");
		$ST->execute(array($customerID,$startDate2, $endDate2));
		
		$ret = array();
		$startime = strtotime($startDate);
		$endtime = strtotime($endDate);
		
		//Create ret array
		$curtime = $startime;
		while($curtime <= $endtime)
		{
			$tmp = new stdClass();
			$tmp->MessageCount = 0;
			$tmp->SendDate = date("Y-m-d",$curtime);
			$ret[] = $tmp;
			$curtime = strtotime('+1 day', $curtime);
			
			//echo("ENDTIME: " . $endtime . " CURTIME: " .$curtime . "\n");
		}
		
		//Add elements that are set
		
		while($a = $ST->fetchObject())
		{
			for($i = 0; $i < count($ret); $i++)
			{
				if($a->SendDate == $ret[$i]->SendDate)
				{
					$ret[$i]->MessageCount = $a->MessageCount;
					break;
				}
			}
		}
		
		
		return $ret;
	}
	
	public static function GetDailyOutgoingForCustomer_Month($customerID,$year,$month)
	{
		$time = strtotime($year . "-" . $month . "-05");
		return self::GetDailyOutgoingForCustomer($customerID, date("Y-m-01", $time), date("Y-m-t", $time));
	}
	
	public static function GetDailyOutgoingForAccount($accountID,$startDate,$endDate)
	{
		global $DB;
		
		$startDate2 = $startDate . " 00:00:00";
		$endDate2 = $endDate . " 23:59:59";
		
		$ST = $DB->prepare("SELECT COUNT(ID) AS MessageCount, DATE(CreateTime) AS SendDate FROM Messages WHERE Direction=". MESSAGEDIRECTION::OUT ." AND AccountID=? AND NOT (`Status` = ". MESSAGESTATUS::CREATED ." OR `Status`=". MESSAGESTATUS::SENDFAIL .") AND CreateTime >= ? AND CreateTime <= ? GROUP BY SendDate;");
		$ST->execute(array($accountID, $startDate2, $endDate2));
		
		
		
		$ret = array();
		$startime = strtotime($startDate);
		$endtime = strtotime($endDate);
		
		//Create ret array
		$curtime = $startime;
		while($curtime <= $endtime)
		{
			$tmp = new stdClass();
			$tmp->MessageCount = 0;
			$tmp->SendDate = date("Y-m-d",$curtime);
			$ret[] = $tmp;
			$curtime = strtotime('+1 day', $curtime);
			
			//echo("ENDTIME: " . $endtime . " CURTIME: " .$curtime . "\n");
		}
		
		//Add elements that are set
		
		while($a = $ST->fetchObject())
		{
			for($i = 0; $i < count($ret); $i++)
			{
				if($a->SendDate == $ret[$i]->SendDate)
				{
					$ret[$i]->MessageCount = $a->MessageCount;
					break;
				}
			}
		}
		
		
		return $ret;
	}
	
	public static function GetDailyOutgoingForAccount_Month($accountID,$year,$month)
	{
		$time = strtotime($year . "-" . $month . "-05");
		return self::GetDailyOutgoingForAccount($accountID, date("Y-m-01", $time), date("Y-m-t", $time));
	}
	
	public static function GetMonthlyOutgoingForCustomer_Year($customerID, $year)
	{
		global $DB;
		
		$startDate = $year . "-01-01 00:00:00";
		$endDate = $year . "-12-31 23:59:59";
		
		$ST = $DB->prepare("SELECT COUNT(ID) AS MessageCount, MONTH(CreateTime) AS SendMonth FROM Messages WHERE Direction=". MESSAGEDIRECTION::OUT ." AND CustomerID=? AND NOT (`Status` = ". MESSAGESTATUS::CREATED ." OR `Status`=". MESSAGESTATUS::SENDFAIL .") AND CreateTime >= ? AND CreateTime <= ? GROUP BY SendMonth;");
		$ST->execute(array($customerID, $startDate, $endDate));
		
		$ret = array();
		$startime = strtotime($startDate);
		$endtime = strtotime($year . "-12-31 00:00:00");
		
		//Create ret array
		$curtime = $startime;
		while($curtime <= $endtime)
		{
			$tmp = new stdClass();
			$tmp->MessageCount = 0;
			$tmp->SendDate = date("Y-m-d",$curtime);
			$ret[] = $tmp;
			$curtime = strtotime('+1 day', $curtime);
			
			//echo("ENDTIME: " . $endtime . " CURTIME: " .$curtime . "\n");
		}
		
		//Add elements that are set
		
		while($a = $ST->fetchObject())
		{
			for($i = 0; $i < count($ret); $i++)
			{
				if($a->SendDate == $ret[$i]->SendDate)
				{
					$ret[$i]->MessageCount = $a->MessageCount;
					break;
				}
			}
		}
		
		
		return $ret;
	}
}