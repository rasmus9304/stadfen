<?php

require_once("../../../stadfensystem/database.php");
require_once("../../../stadfensystem/system.php");
require_once("../admin-system.php");
require_once("../ajaxengine/ajaxengine.php");




if(isset($_POST['name']))
{
	$ST = $DB->prepare("INSERT INTO `SystemVariables`(`Name`, `Value`, `Description`, `DataType`) VALUES (?,?,?,?)");
	$ST->execute(array($_POST['name'],$_POST['value'],$_POST['description'],$_POST['datatype']));
}


?>

<html>
<head>
<meta charset="utf-8"/>
</head>

<body>
<form action="" method="post">

<fieldset>

<p><label>Variabelnamn (CAPS):<br><input type="text" name="name"/></label></p>

<p><label>Beskrivning:<br><input type="text" name="description"/></label></p>

<p><label>Värde:<br><input type="text" name="value"/></label></p>

<p><label>Datatype:<br>

<select name="datatype">

<option value="<?php echo System::SYSTEMVAR_STRING; ?>" selected="selected">String</option>
<option value="<?php echo System::SYSTEMVAR_UINT; ?>">Uint</option>
</select>

</label></p>


<p><input type="submit"/></p>
</fieldset>

</form>
</body>

</html>