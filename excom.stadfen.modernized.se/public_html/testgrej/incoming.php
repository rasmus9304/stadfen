<?php

require("notificationservice.php");

$deviceTokens = array();

$payload = array();
$payload['aps'] = array('alert' => 'Hejsan', 'badge' => 1, 'sound' => 'default');
$payload['server'] = array('serverId' => 3, 'name' => "sd");

$payload = json_encode($payload);

IOSNotification::Send("85f454ee 0f9ed5f2 677c98e8 86fc59b4 81fa09b6 94729ed9 07c6efd7 34dedca7", $payload);
