<?php require_once("admin.php");

require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/cellsyntsystem.php");
require_once("../../stadfensystem/excelexport.php");

if(empty($_GET['year']) || empty($_GET['month']))
	die;
	
$year = $_GET['year'];
$month = $_GET['month'];

$filename = "monthreport_". $year . "-" . $month . ".xlsx";

$filename2 = "/reports/" . $filename;

$filename3 = dirname(__FILENAME__) . $filename; 

ExcelExport::MonthlyCustomerReport($filename3,$year,$month);
ExcelExport::DownloadFile($filename3,$filename);