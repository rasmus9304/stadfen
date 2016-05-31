<?php
require_once("admin.php");
require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/messages.php");
$page_id = "page-messages";

include("header.php");

$isCustomer = !empty($_GET['customerid']) && is_numeric($_GET['customerid']);
$CustomerID = $isCustomer ? $_GET['customerid'] : 0;

$onlyError = !empty($_GET['onlyerror']) && $_GET['onlyerror'] == "1";
?>

<script>

var customerID = <?php echo $CustomerID; ?>;
var chartSource = "/ajax/<?php echo $isCustomer ? ($onlyError ? "customermessages_error.php" :"customermessages.php") : ($onlyError ? "allmessages_error.php" : "allmessages.php");  ?>";

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
	$.modernizedPOST(chartSource,{startdate: startDate, enddate : endDate, customerid: customerID}, function(data,data2)
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
					fillColor: "rgba(220,220,220,0.3)",
					strokeColor: "#fff",
					pointColor: "#fff",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "rgba(220,220,220,1)",
					data: messageArray
				}
			]
		};
		
		var canvas = document.getElementById('bar-chart')
		var ctx = canvas.getContext("2d");
		var barchart = new Chart(ctx).Line(data, { scaleShowGridLines : false, showScale : false, barShowStroke : false, barValueSpacing : 4, barDatasetSpacing : 5, scaleFontSize: 12, responsive: true, scaleFontColor: "#7297a1", scaleLineColor: "#7297a1" });
	
		
		barchart.update();
	});
}

$(document).ready(function(e) {
   updateChart(date30DaysAgo(), dateCurrent());
	
});

</script>

<div style="position: relative;">

	<div style="display: block; overflow: hidden; background:#62eead; color:#FFF;">
    	<div class="clearfix" style="padding: 0px 60px;">
            <h1 style="margin: 35px 0px; font-weight: 300; display: inline; float: left;">Tidslinje över meddelanden</h1>
            <a role="button" class="btn btn-list btn-medium" style="float: right; margin: 35px 0px;">Kund: <strong><?php echo ($isCustomer ? $CustomerID : "Alla kunder") ?></strong></a>
           <!-- <a role="button" class="btn btn-list btn-medium" style="float: right; margin: 35px 0px; margin-right: 10px;">Konto: <strong>Alla konton</strong></a> -->
        </div>
    	<div style="margin: 30px 60px;">
            <canvas id="bar-chart" width="1100" height="70"></canvas>
        </div>
    </div>


    <div class="clearfix" style="background:#F7F7F7; color:#767676; padding: 20px 40px;">
    <?php
	
	if($onlyError)
		echo('<a class="btn btn-default btn-medium" href="/messages.php?customerid='. ($isCustomer ? $CustomerID : "0") .'" role="button" id="btn_delete">Visa lyckade meddelanden '. ($isCustomer ? "för denna kund" : "") .'</a>');
	else
		echo('<a class="btn btn-default btn-medium" href="/messages.php?customerid='. ($isCustomer ? $CustomerID : "0") .'&onlyerror=1" role="button" id="btn_delete">Visa misslyckade meddelanden '. ($isCustomer ? "för denna kund" : "") .'</a>');
		
	if($isCustomer)
	{
		echo(' <a class="btn btn-default btn-medium" href="/messages.php?customerid=0&onlyerror='. ($onlyError ? "1" : "0") .'" role="button" id="btn_delete">Visa meddelanden för alla</a>');
	}
	?>
    	
        
        <!--<div class="form-group" style="float: right; margin: 0px;">
            <input class="form-control" type="text" placeholder="Sök efter meddelande" style="width: 300px;">
        </div>-->
    </div>
    


<div class="clearfix" style="padding: 40px 40px;">
	<div style=" width: auto;">
    <h1 style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Meddelandelista</h1>
    <table class="list incoming-messages-list">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kund</th>
                <th>Konto</th>
                <th>Skapat</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        
<?php

$STGetMessages;

if($isCustomer)
{
	$STGetMessages = $DB->prepare("SELECT Messages.ID AS MessageID, Messages.CreateTime AS CreateTime, Customers.ID AS CustomerID, Customers.Name AS CustomerName, Accounts.EmailAddress AS AccountEmail, Messages.Direction AS MessageDirection FROM Messages INNER JOIN Customers ON Customers.ID = Messages.CustomerID LEFT JOIN Accounts ON Accounts.ID = Messages.AccountID WHERE Messages.CustomerID=?". ($onlyError ? (" AND (Messages.`Status`=". MESSAGESTATUS::SENDFAIL ." OR Messages.`Status`=". MESSAGESTATUS::DELIVERYFAILED .")") : "") .";"); 
	
	$STGetMessages->execute(array($CustomerID));
}
else
{
	
	$STGetMessages = $DB->prepare("SELECT Messages.ID AS MessageID, Messages.CreateTime AS CreateTime, Customers.ID AS CustomerID, Customers.Name AS CustomerName, Accounts.EmailAddress AS AccountEmail, Messages.Direction AS MessageDirection FROM Messages INNER JOIN Customers ON Customers.ID = Messages.CustomerID LEFT JOIN Accounts ON Accounts.ID = Messages.AccountID". ($onlyError ? (" WHERE (Messages.`Status`=". MESSAGESTATUS::SENDFAIL ." OR Messages.`Status`=". MESSAGESTATUS::DELIVERYFAILED .")") : "") .""); 
	
	$STGetMessages->execute();
}

while($a=$STGetMessages->fetch(PDO::FETCH_ASSOC))
{
	
	

	
	echo('
			<tr class='. ($a['MessageDirection']==MESSAGEDIRECTION::IN ? "incoming" : "outgoing") .'>
                <td><a class="btn btn-default" href="viewmessage.php?id='. $a['MessageID'] .'" role="button" style="background:#4aa279;">'. $a['MessageID'] .'</a></td>
                <td>'. $a['CustomerName'] .'</td>
				<td>'. $a['AccountEmail'] .'</td>
				<td>'. $a['CreateTime'] .'</td>
				<td><img src="img/icon-correct.png"></td>
            </tr>
	
	');
}

?>
        
        </tbody>
    </table>
    </div>
  	<!--<div style="float: left; width: 48%; margin-left: 4%;">
    <h1 style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Konversationer</h1>
		
    </div>-->
</div>
</div>

<?php include("footer.php"); ?>