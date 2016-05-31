<?php require_once("admin.php");

require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/messages.php");
require_once("../../stadfensystem/definitions.php");
include("header.php"); 


if(!isset($_GET['id']) ||!is_numeric($_GET['id']))
{
	header("Location: /");
	die;
}

$MessageID = $_GET['id'];

$STCustomer = $DB->Prepare("SELECT 
Messages.ID AS MessageID, 
Messages.Direction AS MessageDirection, 
Accounts.ID AS AccountID, 
Accounts.EmailAddress AS AccountEmail ,
Customers.ID AS CustomerID,
Customers.Name AS CustomerName,
Messages.CreateTime AS CreateTime,
Messages.SendTime AS SendTime,
Messages.DeliveryTime AS DeliveryTime,
Messages.ReadTime AS ReadTime,
Messages.Status AS MessageStatus,
Messages.ErrorMessage AS ErrorMessage,
Messages.Content AS MessageContent,
Messages.RemoteNumber AS RemoteNumber,
Messages.ConcatCount AS ConcatCount
FROM Messages 
LEFT JOIN Accounts ON Accounts.ID = Messages.AccountID 
LEFT JOIN Customers ON Customers.ID = Messages.CustomerID 
WHERE Messages.ID=?;");
$STCustomer->execute(array($MessageID));

if($STCustomer->rowCount() > 0)
{
	$info = $STCustomer->fetch(PDO::FETCH_ASSOC);
?>

<script>

var MessageID = <?php echo $MessageID; ?>;

function deleteThisMessage()
{
	$.modernizedGET("/ajax/deletemessage.php?id="+MessageID,null, function(data,data2)
	{
		window.location = "messages.php";
	});
}

</script>

<div style="width: 900px; margin: 0 auto;">
    <div style="">
    
    	<h2 style="font-weight: 300; margin: 20px 0px; font-size: 1.8em;">Allmän information</h2>
        <table class="list business-customers-list" style="margin-bottom:20px;">
            <tbody>
                <tr><td class="bold">Meddelande-NR:</td><td><?php echo $info['MessageID']; ?></td></tr>
                <tr><td class="bold">Riktning:</td><td><?php echo $info['MessageDirection']==MESSAGEDIRECTION::OUT ? "Utgående" : "Ingående"; ?></td></tr>
                <tr><td class="bold">Skickat av:</td><td><a href="/viewaccount.php?id=<?php echo $info['AccountID']; ?>"><?php echo $info['AccountEmail']; ?></a></td></tr>
                <tr><td class="bold">Företagskund:</td><td><a href="/viewbusiness.php?id=<?php echo $info['CustomerID']; ?>"><?php echo $info['CustomerID']; ?></a></td></tr>
                <tr><td class="bold">Företagskund (Namn):</td><td><?php echo $info['CustomerName']; ?></td></tr>
                
                
                
                <tr><td class="bold">Meddelandet skapat:</td><td><?php echo $info['CreateTime']; ?></td></tr>
                <tr><td class="bold">Meddelandet skickat:</td><td><?php echo $info['SendTime']; ?></td></tr>
                <tr><td class="bold">Meddelandet leverat:</td><td><?php echo $info['DeliveryTime']; ?></td></tr>
                <tr><td class="bold">Meddelandet läst:</td><td><?php echo $info['ReadTime']; ?></td></tr>
            </tbody>
        </table>
        
        <h2 style="font-weight: 300; margin: 20px 0px; font-size: 1.8em;">Status</h2>
        <table class="list business-customers-list" style="margin-bottom:20px;">
            <tbody>
            	<tr><td class="bold">Status:</td><td><?php echo $messagestatus_labels[$info['MessageStatus']]; ?></td></tr>
                <tr><td class="bold">Meddelandet skapat:</td><td><?php echo $info['CreateTime']; ?></td></tr>
                <tr><td class="bold">Meddelandet skickat:</td><td><?php echo $info['SendTime']; ?></td></tr>
                <tr><td class="bold">Meddelandet leverat:</td><td><?php echo $info['DeliveryTime']; ?></td></tr>
                <tr><td class="bold">Meddelandet läst:</td><td><?php echo $info['ReadTime']; ?></td></tr>
                <tr><td class="bold">Potentiellt felmeddelande:</td><td><?php echo $info['ErrorMessage']; ?></td></tr>
            </tbody>
        </table>
        
        <h2 style="font-weight: 300; margin: 20px 0px; font-size: 1.8em;">Meddelande</h2>
        <table class="list business-customers-list" style="margin-bottom:20px;">
            <tbody>
            	<tr><td class="bold">Längd:</td><td><?php echo strlen($info['MessageContent']); ?></td></tr>
                <tr><td class="bold">Meddelandet skapat:</td><td><?php echo $info['CreateTime']; ?></td></tr>
                <tr><td class="bold">Nummer:</td><td><?php echo $info['RemoteNumber']; ?></td></tr>
                <tr><td class="bold">Antal SMS:</td><td><?php echo $info['ConcatCount']; ?></td></tr>
                
                <?php
				
				if(Definitions::ADMIN_CAN_READ_MESSAGES)
				{
					?>
                    <tr><td class="bold">Meddelande:</td><td><?php echo nl2br($info['MessageContent']); ?></td></tr>
                    <?php
				}
				
				?>
            </tbody>
        </table>
    </div>
    
    <div id="passwordactions">
        <a class="btn btn-default" href="javascript:deleteThisMessage();" role="button">Ta bort meddelande</a>
        <span id="passwordactions_message"></span>
    </div>
    

</div>



<?php 

}
else

{
?>
<div style="width: 900px; margin: 0 auto;">
    <div style="border-bottom: 1px solid #ECECEC;">
        <p style="font-size: 1.5em;font-weight: 300; color:#636363; margin-bottom: 15px;">Kunde inte ladda konto</p>
    </div>
</div>

<?php
}

include("footer.php"); ?>