<?php

require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/statistics.php");
require_once("../../stadfensystem/messages.php");
require_once("../../stadfensystem/system.php");

function addEntry($content)
{
	global $DB;
	$ST = $DB->prepare("INSERT INTO ErrorScanResult (Content) VALUES(?);");
	$ST->execute(array($content));
}

//Clear
$DB->query("DELETE FROM ErrorScanResult");

//Check incoming ip-addresses

if(empty(System::SystemVariable("INCOMING_ALLOWED_IPS")))
	addEntry("Inga IP-adresser tillats leverera inkommande meddelanden");
	
if(empty(System::SystemVariable("CRON_ALLOWED_IPS")))
	addEntry("Inga IP-adresser tillats göra CRONJOB-requests");

//Scan for customers with incorrect main account

$ST = $DB->prepare("SELECT Customers.ID  AS CustomerID, Accounts.CustomerID AS AccountCustomerID FROM Customers LEFT JOIN Accounts ON Customers.MainAccountID = Accounts.ID WHERE Customers.Deleted=0 AND (Accounts.ID IS NULL OR NOT Accounts.CustomerID = Customers.ID OR Accounts.Deleted=1);");
$ST->execute();

while($a = $ST->fetchObject())
{
	addEntry("Kundnr. " . $a->CustomerID . " har fel i sin huvudkontokonfiguration");
}