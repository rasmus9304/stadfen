<?php

require_once("admin.php");
require_once("../../stadfensystem/database.php");
$page_id = "page-accounts";

?>

<?php include("header.php"); ?>

<div style="position: relative;">
    <div class="clearfix" style="background:#F7F7F7; padding: 20px 40px;">
        <a class="btn btn-default btn-medium" href="/addaccount.php" role="button">Lägg till konto</a>
        <!--<div class="form-group" style="float: right; margin: 0px;">
    	<input class="form-control" type="text" placeholder="Sök efter konto" style="width: 300px;">
    	</div>-->
    </div>
    
    <script>

function SetAccountLock(button,ID,lock)
{
	if(lock && !confirm("Är du säker på att du vill låsa detta konto?"))
		return;
	if(!$(button).modernizedButtonActive())
	{
		$(button).modernizedButtonGET("/ajax/setaccountlock.php?id="+ ID + "&locked=" + lock);
		
		if(lock == 1)
		{
			$("#accountlock_" + ID).attr('src', 'img/lock.png');
		}
		else
		{
			$("#accountlock_" + ID).attr('src', 'img/unlock.png');
		}
		
		
		$(button).attr("onclick","SetAccountLock(this, " + ID + ", "+ ((lock==1) ? 0 : 1) +")");
	}
}

</script>
    
    <div style="padding: 40px 40px;">
    <h1 style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Konton</h1>
    <table class="list business-customers-list">
        <thead>
            <tr>
            	<th style="width: 32px;">&nbsp;</th>
                <th>E-postadress</th>
                <th>Kundnr</th>
                <th>Kund</th>
                <th>Huvudkonto</th>
            </tr>
        </thead>
        <tbody>
        
<?php

$STGetCustomers = $DB->prepare("SELECT Accounts.ID AS AccountID, Accounts.Locked AS AccountLocked, Accounts.EmailAddress AS EmailAddress, CustomerID, Customers.Name AS CustomerName, Customers.MainAccountID AS MainAccountID FROM Accounts INNER JOIN Customers ON Customers.ID = Accounts.CustomerID WHERE Accounts.`Deleted` = 0;");

$STGetCustomers->execute();


while($a=$STGetCustomers->fetch(PDO::FETCH_ASSOC))
{
	
	$isLocked = $a['AccountLocked'] ? true : false;
	
	
	echo('
			<tr>
				<td><img src="img/'.($isLocked? 'lock.png':'unlock.png').'" style="margin-right: 15px; cursor: pointer;" id="accountlock_'. $a['AccountID'] .'" onclick="SetAccountLock(this,'. $a['AccountID'] .','.( $isLocked ? "0" : "1" ).');return false;"/></td>
                <td><a href="/viewaccount.php?id='. $a['AccountID'] .'">'.$a['EmailAddress'].'</a></td>
                <td>'.$a['CustomerID'].'</td>
                <td><a href="viewbusiness.php?id='. $a['CustomerID'] .'">'.$a['CustomerName'].'</a></td>
				<td>'.(($a['MainAccountID']==$a['AccountID']) ? "Ja" : "Nej").'</td>
            </tr>
	
	');
}

?>

        </tbody>
    </table>
    </div>
    
</div>
<?php include("footer.php"); ?>