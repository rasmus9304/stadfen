<?php
set_time_limit(0);

require_once("../../stadfensystem/cellsyntsystem.php");
require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/messages.php");
require_once("../../stadfensystem/conversations.php");
require_once("../../stadfensystem/system.php");

require_once("../../stadfensystem/notificationservice.php");

$allowed_ips = System::SystemVariable("CRON_ALLOWED_IPS");
if(trim($allowed_ips) != "*" && !in_array($_SERVER['REMOTE_ADDR'], explode(";",$allowed_ips)))
	die("e1");

for($i = 0; $i < 18; $i++)
{
	$ST = $DB->prepare("SELECT `ID`,`DeviceToken`,`Body` FROM APN WHERE `Status`=1;");
	$objects = $ST->fetchAll(PDO::FETCH_ASSOC);
	$ST->closeCursor();
	
	$DB->exec("UPDATE APN SET `Status`=1 WHERE `Status`=0;");
	$ST->execute();
	
	$sent_ids = array();
	$sent_ids[] = 0;
	
	$ServerHandle = IOSNotification::BeginSend();
	
	while($a = $ST->fetch(PDO::FETCH_ASSOC))
	{
		IOSNotification::Send($ServerHandle, $a['DeviceToken'], $a['Body']);
		$sent_ids[] = $a['ID'];
	}
	$ST->closeCursor();
	IOSNotification::EndSend($ServerHandle);
	
	//Set as sent
	$ST = $DB->prepare("UPDATE APN SET  `Status`=2
				WHERE ID IN (". implode(',',$sent_ids) .");");
	$ST->execute();
	$ST->closeCursor();
	
	sleep(3); //Sleep some seconds, then do it again
}