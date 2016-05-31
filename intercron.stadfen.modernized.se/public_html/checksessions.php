<?php

require_once("../../../stadfensystem/loginsession.php");
require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/definitions.php");
require_once("../../../stadfensystem/system.php");

$expire_webapp = (int)System::SystemVariable("WEBAPP_SESSION_EXPIRE");
$expire_mobileapp = (int)System::SystemVariable("MOBILEAPP_SESSION_EXPIRE");

$compareDate = date("Y-m-d H:i:s");

//Go through sessions
//Cancel those that should have expired
$ST = $DB->prepare("UPDATE `LoginSession` SET `Closed`=?,`CloseType`=?  
WHERE `Closed` IS NULL AND 
(
	(`SessionType`=? AND TIME_TO_SEC(TIMEDIFF(?,`LastResponse`)) > ?)
	OR 
	(`SessionType`=? AND TIME_TO_SEC(TIMEDIFF(?,`LastResponse`)) > ?)
);");
$ST->execute(array(date("Y-m-d H:i:s"),LoginSession::CLOSETYPE_EXPIRE,LoginSession::SESSIONTYPE_WEB,$compareDate,$expire_webapp, LoginSession::SESSIONTYPE_MOBILE, $compareDate,$expire_mobileapp));

