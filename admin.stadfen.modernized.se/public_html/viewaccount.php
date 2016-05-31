<?php require_once("admin.php");

require_once("../../stadfensystem/database.php");
include("header.php"); 


if(!isset($_GET['id']) ||!is_numeric($_GET['id']))
{
	header("Location: /");
	die;
}

$AccountID = $_GET['id'];

$STCustomer = $DB->Prepare("SELECT Accounts.ID AS AccountID, Accounts.Locked AS AccountLocked, Accounts.EmailAddress AS EmailAddress, Accounts.CreateTime, Accounts.CustomerID AS CustomerID, Customers.Name AS CustomerName, Accounts.CreateTime AS CreateTime, Accounts.`Deleted` AS AccountDeleted, Accounts.NewPasswordTime AS NewPasswordTime, Customers.Locked AS CustomerLocked FROM Accounts INNER JOIN Customers ON Customers.ID = Accounts.CustomerID WHERE Accounts.ID=?;");
$STCustomer->execute(array($AccountID));

if($STCustomer->rowCount() > 0)
{
	$info = $STCustomer->fetch(PDO::FETCH_ASSOC);
?>

<script>

var AccountID = <?php echo $AccountID; ?>;

function onclickNewPassword(button)
{
	$(button).modernizedButtonGET("/ajax/generatepassword.php?id=<?php echo $AccountID; ?>&domail=1",null, function(data,data2)
	{
		if(data2 !== "")
			alert(data2);
		if(data.Success)
			$("#passwordactions_message").html("Lösenordet skickades till användaren");
		else
			$("#passwordactions_message").html("Något gick fel");
	});
}

var isLocked = <?php echo $info['AccountLocked'] ? "true" : "false"; ?>;

function switchLock()
{
	var button = document.getElementById('btn_lock');
	var shallLock = !isLocked;
	if(shallLock && !confirm("Är du säker på att du vill låsa detta konto?"))
		return;
	if(!$(button).modernizedButtonActive())
	{
		$(button).modernizedButtonGET("/ajax/setaccountlock.php?id="+ AccountID + "&locked=" + (shallLock ? 1:0));
		
		if(shallLock == 1)
		{
			$("#btn_lock").html("Lås upp");
			$("#lock_label").html("Ja");
		}
		else
		{
			$("#btn_lock").html("Lås");
			$("#lock_label").html("Nej");
		}
		
		isLocked = shallLock;
	}
}

function onClickDelete()
{
	if(confirm("Är du säker på att du vill radera detta konto?"))
	{
		var button = document.getElementById('btn_delete');
		if(!$(button).modernizedButtonActive())
		{
			$(button).modernizedButtonGET("/ajax/deleteaccount.php?accid="+ AccountID,null,function(data)
			{
				window.location = "/accounts.php";
			});
		}
	}
}

function dateCurrent()
{
	var today = new Date();
	return dateToStr(today);
}

function date30DaysAgo()
{
	var today = new Date();
	today = new Date(today.getTime() - 30*24*60*60*1000);
	return dateToStr(today);
}

function dateToStr(dateObj)
{
	var dd = dateObj.getDate();
	var mm = dateObj.getMonth()+1;
	var yyyy = dateObj.getFullYear();
	
	if(dd<10) {
		dd='0'+dd
	} 
	
	if(mm<10) {
		mm='0'+mm
	} 
	return yyyy+"-"+mm+"-"+dd;
}

function updateChart(startDate, endDate)
{
	$.modernizedPOST("/ajax/accountoutgoingmessages.php",{accountid: AccountID,startdate: startDate, enddate : endDate}, function(data,data2)
	{
		var labelsArray = new Array();
		var messageArray = new Array();
		for(var i = 0; i < data.InfoArray.length; i++)
		{
			labelsArray[i] = data.InfoArray[i].SendDate;
			messageArray[i] = data.InfoArray[i].MessageCount;
		}
		var data = {
		labels: labelsArray,
		datasets: [
				{
					label: "My First dataset",
					fillColor: "#343358",
					highlightFill: "#343358",
					data: messageArray
				}
			]
		};
		
		var canvas = document.getElementById('bar-chart')
		var ctx = canvas.getContext("2d");
		var barchart = new Chart(ctx).Bar(data, { scaleShowGridLines : false, showScale : false, barShowStroke : false, barValueSpacing : 4, barDatasetSpacing : 5, scaleFontSize: 12, responsive: true, scaleFontColor: "#7297a1", scaleLineColor: "#7297a1" });
	
		barchart.update();
	});
}


$(document).ready(function(e) {
    updateChart(date30DaysAgo(), dateCurrent());
});

</script>

<div style="width: 900px; margin: 0 auto;">
    <div style="border-bottom: 1px solid #ECECEC;">
        <h1 style="font-weight: 300; font-size: 3em;"><?php echo $info['EmailAddress']; ?></h1>
    </div>
    <div style="padding: 15px 0px; border-bottom: 1px solid #ECECEC;">
    	<canvas id="bar-chart" width="900" height="140"></canvas>
    </div>
    
    <?php
	
	if($info['AccountDeleted'])
		echo('<h1>Detta konto är borttaget</h1>');
	
	?>
    
    <div style="">
    
    	<h2 style="font-weight: 300; margin: 20px 0px; font-size: 1.8em;">Allmän information</h2>
        <table class="list business-customers-list" style="margin-bottom:20px;">
            <tbody>
                <tr><td class="bold">Kontonr:</td><td><?php echo $info['AccountID']; ?></td></tr>
                <tr><td class="bold">Företagskund:</td><td><a href="/viewbusiness.php?id=<?php echo $info['CustomerID']; ?>"><?php echo $info['CustomerID']; ?></a></td></tr>
                <tr><td class="bold">Företagskund (Namn):</td><td><?php echo $info['CustomerName']; ?></td></tr>
                <tr><td class="bold">Skapad:</td><td><?php echo $info['CreateTime']; ?></td></tr>
                <tr><td class="bold">Låst:</td><td id="lock_label"><?php echo $info['AccountLocked'] ? "Ja" : "Nej"; ?></td></tr>
                <tr><td class="bold">Företagskund låst:</td><td><?php echo $info['CustomerLocked'] ? "Ja" : "Nej"; ?></td></tr>
            </tbody>
        </table>
        
        <div>
            <a class="btn btn-default btn-medium" href="javascript: switchLock();" role="button" id="btn_lock"><?php echo $info['AccountLocked'] ? "Lås upp" : "Lås"; ?></a>
            <a class="btn btn-default btn-medium" href="javascript: onClickDelete();" role="button" id="btn_delete">Ta bort konto</a>
        </div>
        
        <h2 style="font-weight: 300; margin: 20px 0px; font-size: 1.8em;">Lösenord</h2>
        <table class="list business-customers-list" style="margin-bottom:20px;">
            <tbody>
                <tr><td class="bold">Lösenordet ändrat:</td><td><?php echo $info['NewPasswordTime']; ?></td></tr>
            </tbody>
        </table>
    </div>
    
    <div id="passwordactions">
        <a class="btn btn-default" href="javascript:onclickNewPassword(this);" role="button">Generera nytt lösenord och skicka</a>
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