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
			window.location.assign("/viewaccount.php?id=" + data.AccountID);
	});
}

</script>

<div style="width: 500px; margin: 0 auto;">
	
    <form style="margin-top: 15vh; padding: 30px; border-radius: 4px;" id="addbusinessform" action="/ajax/addaccount.php">
        <h1 style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Lägg till konto</h1>
        <div class="form-group"  style="margin-top: 30px;">
            <label>Företagskund (Kundnr)</label>
            <input type="text" placeholder="" class="form-control" id="customer" value="<?php echo(!empty($_GET['pcid']) ? $_GET['pcid'] : "") ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" placeholder="" class="form-control" id="email">
        </div>
        
        <div class="checkbox">
            <label>
            	<input type="checkbox" id="domailcustomer">Bekräftelse till användare
            </label>
        </div>
        <div class="form-group">
            <button class="btn btn-default" style="width: 100%; padding-top: 15px; padding-bottom: 15px;" onClick="submitForm();return false;">Lägg till</button>
        </div>
    </form>

</div>

<?php include("footer.php"); ?>