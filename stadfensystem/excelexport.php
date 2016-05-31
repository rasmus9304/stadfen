<?php

require_once("PHPExcel/PHPExcel.php");
require_once("PHPExcel/PHPExcel/Writer/Excel2007.php");
require_once("database.php");
require_once("cellsyntsystem.php");
class ExcelExport
{
	const CREATOR = "Städfen System";
	
	const COLUMNNAME_CUSTOMERID = "Kundnummer";
	const COLUMNNAME_CORPORATENUMBER = "Organisationsnummer";
	const COLUMNNAME_NAME = "Namn";
	const COLUMNNAME_MESSAGECOUNT = "Antal meddelanden";
	const COLUMNNAME_TRUESMSCOUNT = "Antal faktiska SMS";
	const COLUMNNAME_TOTALSMSCOST = "Total kostnad för utgående sms";
	
	const CUSTOMERREPORT_SHEETNAME_1 = "Blad 1";
	public static function CustomerReport($Filename, $StartTime = NULL, $EndTime = NULL)
	{
		global $DB;
		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->getProperties()->setCreator(self::CREATOR);
		
		$objPHPExcel->setActiveSheetIndex(0);
		
		$_timespan_where = "";
		
		if($StartTime !== NULL && $EndTime !== NULL)
			$_timespan_where = " AND `SendTime` >= '" . $StartTime ."' AND  `SendTime` <= '".$EndTime."'";
		else if($StartTime !== NULL)
			$_timespan_where = " AND `SendTime` >= '" . $StartTime ."'";
		else if($EndTime !== NULL)
			$_timespan_where = " AND `SendTime` <= '" . $EndTime."'";
			
		//die("SELECT ID as CustomerID, CorporateNumber, Name, (SELECT COUNT(ID) FROM Messages WHERE Messages.CustomerID = Customers.ID AND `SendTime` IS NOT NULL".$_timespan_where.") AS MessageCount, (SELECT SUM(ConcatCount) FROM Messages WHERE Messages.CustomerID = Customers.ID AND `SendTime` IS NOT NULL".$_timespan_where.") AS TrueSMSCount FROM Customers;");
			
			
		//For all not-deleted and all where there are messages
		$st = $DB->prepare("SELECT ID as CustomerID, CorporateNumber, Name, (SELECT COUNT(ID) FROM Messages WHERE Messages.CustomerID = Customers.ID AND `SendTime` IS NOT NULL".$_timespan_where.") AS MessageCount, (SELECT SUM(ConcatCount) FROM Messages WHERE Messages.CustomerID = Customers.ID AND `SendTime` IS NOT NULL".$_timespan_where.") AS TrueSMSCount FROM Customers WHERE Deleted=0 OR (SELECT COUNT(ID) FROM Messages WHERE Messages.CustomerID = Customers.ID AND `SendTime` IS NOT NULL".$_timespan_where.") > 0;");
		
		$st->execute();
		
		
		$row = 1;
		
		
		$col = 0;
		$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), self::COLUMNNAME_CUSTOMERID);
		$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), self::COLUMNNAME_CORPORATENUMBER);
		$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), self::COLUMNNAME_NAME);
		$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), self::COLUMNNAME_MESSAGECOUNT);
		$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), self::COLUMNNAME_TRUESMSCOUNT);
		$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), self::COLUMNNAME_TOTALSMSCOST);
		$row++;
		
		while($a = $st->fetch(PDO::FETCH_ASSOC))
		{
			$col = 0;
			$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), $a['CustomerID']);
			$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), $a['CorporateNumber']);
			$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), $a['Name']);
			$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), $a['MessageCount']);
			$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), $a['TrueSMSCount']);
			$objPHPExcel->getActiveSheet()->SetCellValue(self::getCellName($col++,$row), $a['TrueSMSCount']*CellsyntSystem::$SENDSMSCOST);
			$row++;
		}
		
		$objPHPExcel->getActiveSheet()->setTitle(self::CUSTOMERREPORT_SHEETNAME_1);
		
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($Filename);
	}
	
	public static function MonthlyCustomerReport($Filename, $Year, $Month)
	{
		$timestamp = strtotime($Year.'-'.$Month.'-01');
		$StartTime = date('Y-m-01 00:00:00', $timestamp);
		$EndTime = date('Y-m-t 12:59:59', $timestamp);
		
		self::CustomerReport($Filename, $StartTime,  $EndTime);
	}
	
	private static function getCellName($x,$y)
	{
		return chr($x + 65) . $y;
	}
	
	public static function DownloadFile($file,$filename)
	{
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Length: " . filesize($file));
		header('Content-Disposition: attachment; filename='.$filename);
		
		echo file_get_contents($file);
	}
}