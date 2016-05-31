<?php
/*
The script will logout the current account, ending the current session
*/
require_once("comsystem.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/accounts.php");
require_once("../../../stadfensystem/customers.php");
require_once("../../../stadfensystem/loginsession.php");
$com = new ComSystem();

LoginSession::SetLoggedOut(LoginSession::CLOSETYPE_LOGOUT);

$com->Data->Success = TRUE;

sleep(1);