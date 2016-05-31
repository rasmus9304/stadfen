<?php

require_once("admin.php");
require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/statistics.php");
require_once("../../stadfensystem/messages.php");
$page_id = "page-admins";

?>

<?php include("header.php"); ?>

	<script>
	function generatePasswordForAdmin(adminID)
	{
		$.modernizedGET("/ajax/generatepasswordforadmin.php",{id:adminID, domail: 1},function(data)
			{
				if(data.Success)
				{
					$("#agpw_button_" + adminID).hide();
				}
				else
				{
					
				}
				vn_isRequesting = false;
			});
	}
	
	var currentAdmin = <?php echo Admin::GetAdminID(); ?>;
	
	function deleteAdmin(adminID)
	{
		if(adminID != currentAdmin)
		{
			if(confirm("Är du säker att du vill ta bort denna administratör?"))
			{
				$.modernizedGET("/ajax/deleteadmin.php",{id:adminID},function(data)
					{
						if(data.Success)
						{
							$("#a_row_" + adminID).hide();
						}
						else
						{
							
						}
						vn_isRequesting = false;
					});
			}
		}
		else
		{
			alert("Du kan inte ta bort dig själv");
		}
	}
	
	</script>

    
    <div class="clearfix" style="background:#F7F7F7; padding: 20px 40px;">
    <a role="button" class="btn btn-default btn-medium" href="/addadmin.php">Lägg till administratör</a>
    <!--<div class="form-group" style="float: right; margin: 0px;">
    	<input class="form-control" type="text" placeholder="Sök efter företagskund" style="width: 300px;">
    </div>-->
    </div>
    
    <div style="padding: 40px 40px;">
    	<h1 style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Administratörer</h1>
        
        
        
        <table class="list business-customers-list">
            <thead>
                <tr>
                    <th>Namn</th>
                    <th>Epostadress</th>
                    <th>Nytt lösenord</th>
                    <th>Ta bort</th>
                </tr>
            </thead>
            <tbody>
            
    <?php
    
	$ST = $DB->prepare("SELECT ID,Username,EmailAddress FROM Administrators;");
   	$ST->execute();
	
	while($a = $ST->fetch(PDO::FETCH_ASSOC))
	{
		echo('
		<tr id="a_row_'. $a['ID'] .'">
				<td>'. $a['Username'] .'</td>
				<td>'. $a['EmailAddress'] .'</td>
				<td><a class="btn btn-default btn-medium" href="javascript:generatePasswordForAdmin('. $a['ID'] .');" role="button" id="agpw_button_'. $a['ID'] .'">Generera och maila</a></td>
				<td><a class="btn btn-default btn-medium" href="javascript:deleteAdmin('. $a['ID'] .');" role="button">Ta bort</a></td>
        </tr>
		');
	}
	
    ?>
            
            </tbody>
        </table>
        
        
    </div>


<?php include("footer.php"); ?>