<?php

require_once("admin.php");
require_once("../../stadfensystem/database.php");
$page_id = "page-errors";

?>

<?php include("header.php"); ?>

<div style="position: relative;">
    
    <script>

function checkError(button,ID)
{
	if(!$(button).modernizedButtonActive())
	{
		$(button).modernizedButtonGET("/ajax/checkerror.php?id="+ ID);
		
		$("#checkedicon_"+ID).show();
		$("#checkbutton_"+ID).hide();
	}
}

</script>
    
    <div style="padding: 40px 40px;">
    <h1 style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Problem</h1>
    <table class="list business-customers-list">
        <thead>
            <tr>
                <th>Klarmarkerad</th>
                <th>Meddelande</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        
<?php

$STGetCustomers = $DB->prepare("SELECT * FROM ErrorScanResult;");

$STGetCustomers->execute();


while($a=$STGetCustomers->fetch(PDO::FETCH_ASSOC))
{
	
	$isLocked = $a['AccountLocked'] ? true : false;
	
	
	echo('
			<tr>
                <td><img src="img/icon-correct.png" id="checkedicon_'. $a['ID'] .'" style="'.($a['CheckedDone'] ? '' : 'display:none;').'"></td>
                <td>'.$a['Content'].'</td>
				<td>'.($a['CheckedDone']?'':'<a id="checkbutton_'. $a['ID'] .'" class="btn btn-default btn-medium" href="javascript:checkError(this,'. $a['ID'] .');" role="button">Klarmarkera</a>').'</td>
            </tr>
	
	');
}

?>

        </tbody>
    </table>
    </div>
    
</div>
<?php include("footer.php"); ?>