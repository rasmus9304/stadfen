<?php

$mysql_host = "stadfensystem-176031.mysql.binero.se";
$mysql_user = "176031_ia15729";
$mysql_pass = "436hASD3o86b54cX";
$mysql_db = "176031-stadfensystem";

$DB = new PDO("mysql:host=".$mysql_host.";dbname=".$mysql_db, $mysql_user, $mysql_pass);
$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$DB->exec("SET CHARACTER SET utf8");