<?php
//LOGIN - The script requires username and password and will login the user
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/loginsession.php");


/*

Login message-codes:

0 = Successful login
1 = Invalid login
2 = Customer is locked
3 = Account is locked

*/

$com = new ComSystem();

const F_USER = "user";
const F_PASS = "pass";
const F_TYPE = "systype";

$com->RequireData(F_USER,F_PASS,F_TYPE);


$com->RequireDataNumeric(F_TYPE);
if($_POST[F_TYPE] != LoginSession::SESSIONTYPE_MOBILE && $_POST[F_TYPE] != LoginSession::SESSIONTYPE_WEB)
	$com->InvalidData(); //Invalid Sessiontype specyfied

$com->Data->LoginSuccess = FALSE;

$com->Data->MessageCode = 1;
$com->Data->Number = NULL;
$com->Data->AccountID = NULL;

$com->Data->ServerTime = NULL;



$AccountObj = Account::GetAccountObjByEmail($_POST[F_USER]); //Attempt to load account
if($AccountObj !== NULL)
{
	if(Account::CheckPasswordCorrect($AccountObj, $_POST[F_PASS]))
	{
		//Check if locked
		$CustomerObj = Customer::GetCustomerObj($AccountObj->CustomerID);
		if($CustomerObj->Locked)
		{
			$com->Data->MessageCode = 2; 
		}
		else if($AccountObj->Locked) //If account locked
		{
			$com->Data->MessageCode = 3;
		}
		else
		{
			$com->Data->LoginSuccess = TRUE;
			$com->Data->MessageCode = 0;
			//Login
			LoginSession::SetLoggedIn($AccountObj->ID, $_POST[F_TYPE]);
			
			//Fetch phonenumber and send it, will be displayed while application is loading
			$ST = $DB->prepare("SELECT Customers.VirtualNumber FROM Accounts LEFT JOIN Customers ON Customers.ID = Accounts.CustomerID WHERE Accounts.ID = ?;");
			$ST->execute(array($AccountObj->ID));
			
			$info = $ST->fetch(PDO::FETCH_NUM);
			$com->Data->Number = $info[0];
			$com->Data->AccountID = $AccountObj->ID;
		}
	}
}

//sleep(1);