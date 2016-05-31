<?php

$page_id = "page-login";
	
?>

<!DOCTYPE html>
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
        
        <script src="//use.typekit.net/phm0nlq.js"></script>
		<script>try{Typekit.load();}catch(e){}</script>

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="/ajaxengine/ajaxengine.js"></script>
        
        <script>
		
		function doSubmit()
		{
			$("form").modernizedSubmitForm(null, function(data,data2,success)
			{
				if(success)
					window.location.assign("/index.php");
			});
		}
		
		</script>
    </head>
    <body id="<?php echo $page_id; ?>">
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

	<style>
    
    .Absolute-Center {
      margin: auto;
      position: absolute;
      top: 0; left: 0; bottom: 0; right: 0;
    }
    
    </style>
    
    <div style="width: 100vw; height: 100vh; background-color:#33357C; background-size: cover;">
    	<div class="Absolute-Center" style="width: 270px; height: 400px;">
            <div style="width: 100%; height: 100%;padding: 0px 35px; background:#FFF; border: 1px solid #8B8B8B; border-radius: 4px; box-shadow: 0px 1px 3px rgba(0,0,0,0.35);">
                <div style="display: block; margin-bottom: 20px; margin-top: 30px; text-align: center;"><img src="img/logo-login.png" style="width: 100%;"></div>
                <form action="/ajax/login.php">
                <div class="form-group">
                    <label for="exampleInputEmail1">Användarnamn</label>
                    <input type="text" placeholder="" class="form-control" id="loginname">
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Lösenord</label>
                    <input type="password" placeholder="" class="form-control" id="loginpassword">
                </div>
                <div class="form-group">
                    <button class="btn btn-default" role="button" style="width: 100%; margin-top: 25px; padding-top: 15px; padding-bottom: 15px; font-size: 1em; text-transform: uppercase;" onClick="doSubmit();return false;">Logga in</button>
                </div>
                </form>
                
                <div id="message">&nbsp;</div>
            </div>
            <p style="color:#FFF; text-align: center; font-size: 1.3em; margin: 20px 0px;">Glömt lösenordet?</p>
        </div>
    </div>

	</body>
</html>