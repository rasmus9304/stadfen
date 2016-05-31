<?php

require_once("admin-system.php");
session_start();
Admin::SetLoggedOut();


header("Location: /login.php");