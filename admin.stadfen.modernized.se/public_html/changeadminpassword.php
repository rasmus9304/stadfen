<?php 
require_once("admin.php");
require_once("../../stadfensystem/database.php");
include("header.php"); ?>


<script>

function submitForm()
{
	$("form").modernizedSubmitForm(null, function(data,data2,success)
	{
		if(success)
		{
			alert("Lösenordet ändrades");
			window.location.assign("/index.php");
		}
			
	});
}

</script>

<div style="width: 500px; margin: 0 auto;">

    <form style="margin-top: 15vh; padding: 30px; border-radius: 4px;" id="addbusinessform" action="/ajax/changeadminpassword.php">
        
        <div style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Ändra lösenord</h1></div>
        
        <div class="form-group" style="margin-top: 30px;">
            <label for="old">Gammalt lösenord</label>
            <input type="password" placeholder="" class="form-control" id="old">
        </div>
        <div class="form-group" style="margin-top: 30px;">
            <label for="new">Lösenord</label>
            <input type="password" placeholder="" class="form-control" id="new">
        </div>
        <div class="form-group" style="margin-top: 30px;">
            <label for="repeat">Upprepa lösenord</label>
            <input type="password" placeholder="" class="form-control" id="repeat">
        </div>
        <div class="form-group">
            <button class="btn btn-default" style="width: 100%; padding: 22px inherit;" onClick="submitForm(); return false;">Ändra</button>
        </div>
    </form>

</div>

<?php include("footer.php"); ?>