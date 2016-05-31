<?php
require_once("admin.php");
require_once("../../stadfensystem/database.php");
$page_id = "page-settings";

?>

<?php include("header.php"); ?>


<script>



</script>

<div style="width: 900px; margin: 0 auto;">

    
    <div style=" border-bottom: 1px solid #E0E0E0;">
    <h2 style="font-weight: 300; margin: 20px 0px; font-size: 1.8em;">Systemvariabler</h2>
    </div>
    <div style="position: relative;">
    <table class="business-customers-list" style="margin-bottom:12px;">
        <thead>
            <tr>
                <th>Variabel</th>
                <th>Värde</th>
                <th>Beskrivning</th>
                <th>Åtgärder</th>
            </tr>
        </thead>
        <tbody>
        
<script>

var sysvar_is_editing = false;
var sysvar_is_requesting = false;

const DATATYPE_STRING = 1;
const DATATYPE_UINT = 2;


function editSysvar(id,datatype)
{
	if(sysvar_is_editing)
		return;
	sysvar_is_editing = true;
	var $valueField =  $('#sysvar_'+id+'_value');
	var currentValue = $valueField.html();
	currentValue = escapeHtml(currentValue);
	
	$valueField.html('<input type="text" id="sysvar_'+id+'_valueedit" value="'+ currentValue +'"/><a class="btn btn-default" href="javascript: finishEditSysvar('+id+','+datatype+');" role="button" style="margin-left: 5px;">Spara</a>');
}

function finishEditSysvar(id,datatype)
{
	if(sysvar_is_editing && !sysvar_is_requesting)
	{
		var $valueField =  $('#sysvar_'+id+'_value');
		var $inputField =  $('#sysvar_'+id+'_valueedit');
		
		var val = $inputField.val();
		
		if(datatype == DATATYPE_UINT && !isUINT(val))
		{
			alert("Måste vara heltal större eller lika med 0");
			return;
		}
		
		$valueField.prop('disabled', true);
		sysvar_is_requesting = true;
		$.modernizedPOST("/ajax/editsystemvar.php",{id: id, val : val}, function(data)
		{
			if(data.Success)
			{
				sysvar_is_requesting = false;
				sysvar_is_editing = false;
				$valueField.prop('disabled', false);
				$valueField.html(val);
			}
			else
			{
				sysvar_is_requesting = false;
				$valueField.prop('disabled', false);
				alert(data.Message);
			}
		});
	}
}

function isUINT(str) {
    var n = ~~Number(str);
    return String(n) === str && n >= 0;
}

 var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;',
    "/": '&#x2F;'
  };

  function escapeHtml(string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) {
      return entityMap[s];
    });
  }
</script>
        
<?php

$STGetCustomers = $DB->prepare("SELECT * FROM SystemVariables ORDER BY Name;");

$STGetCustomers->execute(array($CustomerID));


while($a=$STGetCustomers->fetch(PDO::FETCH_ASSOC))
{
	
	

	
	echo('
			<tr>
				<td>'.$a['Name'].'</td>
                <td id="sysvar_'.$a['ID'].'_value">'.$a['Value'].'</td>
				<td>'.$a['Description'].'</td>
                <td><a class="btn btn-default" href="javascript: editSysvar('.$a['ID'].','.$a['DataType'].');" role="button" style="margin-left: 5px;">Ändra</a></td>
            </tr>
	
	');
}

?>
            

        </tbody>
    </table>
    <div style="float: left; width: 50%;">
    </div>
    
    <div>
        <!--<a class="btn btn-default" href="/addaccount.php?pcid=<?php echo $CustomerID; ?>" role="button">Lägg till konto</a>-->
    </div>
    
    
    
</div>

</div>


<?php include("footer.php"); ?>