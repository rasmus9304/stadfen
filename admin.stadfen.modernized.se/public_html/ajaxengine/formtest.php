<!DOCTYPE html>



<head>
<meta charset="utf-8"/>

<link rel="stylesheet" href="ajaxengine.css"/>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="ajaxengine.js"></script>

<script>
//[{name: "namn", value: "Lukas"},{name: "age", value: "21"}]
function test2()
{
	$('#testform').modernizedSubmitForm({namn: "Lukas", age: 21},function(data, data2, success)
	{
		alert(data2);
	},
	
	function(data)
	{
		var x = 2;
		return true;
	});
}

function test3()
{
	$("#email").modernizedSetErrorLabel("Fel");
}

function test4()
{
	$("#email").modernizedRemoveErrorLabel();
}

function test5()
{
	$("#email").modernizedSetErrorLabel("Fel 2");
}
</script>
</head>



<body>

<fieldset>
<p>
<form id="testform" action="formajax.php">
Email:
<input type="text" id="email"/><br><br>

<p>
<textarea id="text_area"></textarea>
</p>

<p>
<input type="radio" name="rad" value="rad1"> Radio 1<br>
<input type="radio" name="rad" value="rad2"> Radio 2<br>
</p>

<p>
<select id="dropdown">
	<option value="1">Value 1</option>
    <option value="2">Value 2</option>
</select>
</p>

<p>
<input type="radio" name="rad2" value="rad1"> Radio 2-1<br>
<input type="radio" name="rad2" value="rad2"> Radio 2-2<br>
</p>

<p>
	<label>
    	<input type="checkbox" id="acceptradio"/>
        Jag accepterar
    </label>
</p>


<input type="button" onClick="test2();" value="Submit"/>
</form>
</p>
</fieldset>

<p><a href="javascript:test3();">test</a></p>

<p><a href="javascript:test4();">test remove</a></p>

<p><a href="javascript:test5();">test 5</a></p>

</body>


</html>
