<?php

include("../../stadfensystem/cellsyntsystem.php");
include("../../stadfensystem/database.php");
include("../../stadfensystem/messages.php");

const F_DEST = 'destination';
const F_TRACKID = 'trackingid';
const F_STATUS = 'status';

if(!isset($_REQUEST[F_DEST]) || !isset($_REQUEST[F_TRACKID]) || !isset($_REQUEST[F_STATUS]))
	die("e1");
	


SetTrackingIDDeliveryStatus(PhoneNumber::ParseToStandard($_REQUEST[F_DEST]),$_REQUEST[F_TRACKID],$_REQUEST[F_STATUS]);

/*

Test:
http://com.stadfen.modernized.se/deliveryreport.php?destination=0046707838026&trackingid=26ee31fb501d9679afb9ea81456451d9&status=buffered

*/