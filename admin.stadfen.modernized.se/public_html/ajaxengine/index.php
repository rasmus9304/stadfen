<!DOCTYPE html>



<head>
<meta charset="utf-8"/>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="ajaxengine.js"></script>

<script>
function test()
{
	var ret =$.modernizedGET("ajaxtest.php",null, 
	function(data,otherdata)
	{
		alert("Namn är " + data.Name + ", ålder är " + data.Age + ". Övrig data är '"+ otherdata +"'");
	});
}
</script>
</head>



<body>

<a href="javascript:test();">Test</a>

</body>


</html>
