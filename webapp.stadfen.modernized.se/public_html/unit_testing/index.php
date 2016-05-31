<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
<link type="text/css" rel="stylesheet" href="index.css">
</head>

<body>
	
	<div class="dir-container">
    	<h1>Available unit tests</h1>
    	<?php
		
			$dirs = array_filter(glob('*'), 'is_dir');
			for ($i = 0; $i < count($dirs); $i++)
			{
				echo "<div class='dir-item'><a href='".$dirs[$i]."'>".$dirs[$i]."</a></div>";	
			}
		
		?>
    </div>

</body>
</html>