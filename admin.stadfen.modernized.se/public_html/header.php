<?php
	$_ST_getAdminName = $DB->prepare("SELECT Username FROM Administrators WHERE ID=?;");
	$_ST_getAdminName->execute(array(Admin::GetAdminID()));
	
	if($_ST_getAdminName->rowCount() == 0)
	{
		header("Location: /logout.php");
		die;
	}
	
	
	$AdminName = $_ST_getAdminName->fetchObject()->Username;
?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Städfen UI</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/bootstrap-theme.css">
        <link rel="stylesheet" href="css/main.css">
        
        <link rel="stylesheet" href="ajaxengine/ajaxengine.css">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="/ajaxengine/ajaxengine.js"></script>
        
        <script src="//use.typekit.net/phm0nlq.js"></script>
		<script>try{Typekit.load();}catch(e){}</script>

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
       
        <script>
		var update_error_interval = 10000; // milliseconds
		function updateErrorList()
		{
			$.modernizedGET("/ajax/geterrorlist.php?getcount=1",null,function(data)
			{
				var errorCount = data.ErrorCount;
				
				setTimeout(updateErrorList,update_error_interval);
				
				$("#top_error_label").html(errorCount==0 ? "Inga problem har uppstått" : (errorCount + " problem"));
			})
		}
		
		$(document).ready(function(e) {
            updateErrorList();
        });
		
		</script>
        
    </head>
    <body id="<?php echo $page_id; ?>">
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <div class="background-fade" onClick="hideAccountMenu();"></div>
    <div style="width: 230px; height: 100vh; position: fixed; top: 32px; left: 0px; color:#9E9E9E; background:#EDEDED; z-index: 1000;">
        <nav>
            <a href="index.php" id="nav-customers"><img class="svg" src="img/nav-customers.svg" width="62" height="62">Företagskunder</a>
            <a href="accounts.php" id="nav-accounts"><img class="svg" src="img/nav-accounts.svg" width="62" height="62">Konton</a>
            <a href="messages.php" id="nav-messages"><img class="svg" src="img/nav-messages.svg" width="62" height="62">Meddelanden</a>
            <a href="sessions.php" id="nav-sessions"><img class="svg" src="img/nav-accounts.svg" width="62" height="62">Sessioner</a>
            <a href="exporting.php" id="nav-exporting"><img class="svg" src="img/nav-export.svg" width="62" height="62">Exportering</a>
            <a href="settings.php" id="nav-settings"><img class="svg" src="img/nav-settings.svg" width="62" height="62">Inställningar</a>
        </nav>
    </div>
    <div class="top-bar" style="text-transform: uppercase;">
        <img src="img/top-menu.png" onClick="showAccountMenu();" style="cursor:pointer;"><p style="margin-left: 10px;">Inloggad som <?php echo $AdminName; ?>.</p>
        <div style="float: right; top: 0px; margin-right: 10px;">
        	<a href="/errorlist.php"><p class="inactive" style="margin-right: 10px;" id="top_error_label">Laddar...</p><img src="img/top-warning.png" style="height: 18px; width: 18px;" width="18" height="18"></a>
        </div>
    </div>

    <div class="account-menu">
    	<div class="clearfix" style="padding: 15px;">
    		<img src="img/nav-close.svg" style="width: 26px; height: 26px; float: right; cursor:pointer;" onClick="hideAccountMenu();">
        </div>
    	<h1><?php echo $AdminName; ?></h1>
        
        <ul>
        	<li><a href="/admins.php">Visa administratörer</a></li>
            <li><a href="/changeadminpassword.php">Ända lösenord</a></li>
        </ul>
        
        <a role="button" class="btn btn-default" href="logout.php" id="nav-signout" style="display: block; margin: 0px 10px; padding: 20px inherit;">Logga ut</a>
    </div>
    
    <div class="content">
        <div id="main-container">
            <div id="main">