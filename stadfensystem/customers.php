<?php
require_once("database.php");
require_once("password.php");
require_once("email.php");

class Customer
{
	public static function GetCustomerObj($CustomerID)
	{
		global $DB;
		$st = $DB->prepare("SELECT * FROM Customers WHERE ID=?;");
		$st->execute(array($CustomerID));
		
		if($st->rowCount() > 0)
			return $st->fetchObject();
		else
			return NULL;
	}
	
}