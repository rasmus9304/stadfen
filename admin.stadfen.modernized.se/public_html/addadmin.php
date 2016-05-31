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
			window.location.assign("/admins.php");
	});
}

</script>

<div style="width: 500px; margin: 0 auto;">

    <form style="margin-top: 15vh; padding: 30px; border-radius: 4px;" id="addbusinessform" action="/ajax/addadmin.php">
        
        <div style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Lägg till Administratör</h1></div>
        
        <div class="form-group" style="margin-top: 30px;">
            <label for="name">Användarnamn</label>
            <input type="text" placeholder="" class="form-control" id="name">
        </div>
        <div class="form-group">
            <label for="email">E-postadress</label>
            <input type="text" placeholder="" class="form-control" id="email">
        </div>
        <div class="checkbox">
            <label>
            	<input type="checkbox" id="domailadmin">Skicka lösenord som e-post till administratören
            </label>
        </div>
        <div class="form-group">
            <button class="btn btn-default" style="width: 100%; padding: 22px inherit;" onClick="submitForm();return false;">Lägg till</button>
        </div>
    </form>

</div>

<?php include("footer.php"); ?>