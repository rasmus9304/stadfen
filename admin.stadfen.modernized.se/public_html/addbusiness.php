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
			window.location.assign("/viewbusiness.php?id=" + data.CustomerID);
	});
}

</script>

<div style="width: 500px; margin: 0 auto;">

    <form style="margin-top: 15vh; padding: 30px; border-radius: 4px;" id="addbusinessform" action="/ajax/addbusiness.php">
        
        <div style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Lägg till kund</h1></div>
        
        <div class="form-group" style="margin-top: 30px;">
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
            <input type="text" placeholder="" class="form-control" id="maxaccounts" style="width:100px;" value="1">
        </div>
        
        <div class="checkbox">
            <label>
            	<input type="checkbox" id="domailcustomer">Bekräftelse till kund
            </label>
        </div>
        <div class="form-group">
            <button class="btn btn-default" style="width: 100%; padding: 22px inherit;" onClick="submitForm();return false;">Lägg till</button>
        </div>
    </form>

</div>

<?php include("footer.php"); ?>