<?php

require_once("../../stadfensystem/cellsyntsystem.php");
require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/messages.php");
require_once("../../stadfensystem/conversations.php");
require_once("../../stadfensystem/system.php");

$ST = $DB->prepare("SELECT * FROM APN;");
$ST->execute();

while($a = $ST->fetch())
{
	print_r($a);
}