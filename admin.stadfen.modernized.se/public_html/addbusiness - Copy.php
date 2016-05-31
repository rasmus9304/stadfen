<?php 
require_once("admin.php");
require_once("../../stadfensystem/database.php");
include("header.php"); ?>


<script>

function submitForm()
{
	$.modernizedGET("ajax/login.php",null,null);
	$("form").modernizedSubmitForm(null, function(data,data2,success)
	{
		if(success)
			window.location.assign("/viewbusiness.php?id=" + data.CustomerID);
	});
}

</script>

<div style="width: 500px; margin: 0 auto;">

    <form style="margin-top: 15vh; padding: 30px; border-radius: 4px;" id="addbusinessform" action="/ajax/addbusiness.php">
        
        <div class="form-group">
            <label for="exampleInputEmail1">Organisationsnummer</label>
            <input type="text" placeholder="000000-XXXX" class="form-control" id="orgnr">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Namn</label>
            <input type="text" placeholder="" class="form-control" id="name">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Email</label>
            <input type="text" placeholder="" class="form-control" id="email">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Maximalt antal konton</label>
            <input type="text" placeholder="" class="form-control" id="maxaccounts" style="width:50%;" value="1">
        </div>
        
        <div class="checkbox">
            <label>
            	<input type="checkbox" id="domailcustomer">Bekräftelse till kund
            </label>
        </div>
        <div class="form-group">
            <button class="btn btn-default" style="width: 100%; padding-top: 15px; padding-bottom: 15px;" onClick="submitForm();return false;">Lägg till</button>
        </div>
    </form>

</div>

<?php include("footer.php"); ?>