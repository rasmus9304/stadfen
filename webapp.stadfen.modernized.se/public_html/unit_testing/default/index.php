<?php
	require("../lib.php");
	
	Page::setPageSpeedWatching();

	$title = "Default Unit Testing";
	$version = "1.0.0";
	$description = "Beskrivning av testet här.";
	
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
<link type="text/css" rel="stylesheet" href="unit_style.css">
</head>

<body>
	
    <a href="../">Back</a>
    <div class="container">
        <div class="sidebar">
        <div class="logo"><h1>Unit testing: <?php echo $title; ?></h1><p><?php echo $version; ?></p></div>
        <h2>Included files</h2>
        <?php Page::echoIncludedFiles(); ?>
        <p>
        <?php echo $description; ?>
        </p>
        </div>
        <div class="main-content">
        f
        </div>
	</div>

<?php

Page::echoPageSpeed();

?>

</body>
</html>