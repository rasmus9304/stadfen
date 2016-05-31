<?php

require_once("admin.php");
require_once("../../stadfensystem/statistics.php");
require_once("../../stadfensystem/messages.php");
require_once("../../stadfensystem/database.php");
$page_id = "page-customers";


require_once("header.php");
?>



<script>

function SetBusinessLock(button,ID,lock)
{
	if(lock && !confirm("Är du säker på att du vill låsa denna kund?"))
		return;
	if(!$(button).modernizedButtonActive())
	{
		$(button).modernizedButtonGET("/ajax/setcustomerlock.php?id="+ ID + "&locked=" + lock);
		
		if(lock == 1)
		{
			$("#customerlock_" + ID).attr('src', 'img/lock.png');
		}
		else
		{
			$("#customerlock_" + ID).attr('src', 'img/unlock.png');
		}
		
		
		$(button).attr("onclick","SetBusinessLock(this, " + ID + ", "+ ((lock==1) ? 0 : 1) +")");
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
	$.modernizedPOST("/ajax/alloutgoingmessages.php",{startdate: startDate, enddate : endDate}, function(data,data2)
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
					fillColor: "#FFF",
					highlightFill: "#FFF",
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

    <div style="display: block; overflow: hidden; background:#343358; color:#FFF;">
    	<div class="clearfix" style="padding: 0px 60px;">
            <h1 style="margin: 35px 0px; font-weight: 300; display: inline; float: left;">Utgående meddelanden - Alla företagskunder</h1>
            <a role="button" class="btn btn-list btn-medium" style="float: right; margin: 35px 0px;">Datum: <strong>Senaste 30 dagarna</strong></a>
        </div>
    	<div style="margin: 60px;">
            <canvas id="bar-chart" width="1100" height="90"></canvas>
        </div>
    </div>
    
    <div class="clearfix" style="background:#F7F7F7; padding: 20px 40px;">
    <a role="button" class="btn btn-default btn-medium" href="addbusiness.php">Lägg till kund</a>
    <!--<div class="form-group" style="float: right; margin: 0px;">
    	<input class="form-control" type="text" placeholder="Sök efter företagskund" style="width: 300px;">
    </div>-->
    </div>
    
    <div style="padding: 40px 40px;">
    	<h1 style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Företagskunder</h1>
        
        
        
        <table class="list business-customers-list">
            <thead>
                <tr>
                    <th style="width: 32px;">&nbsp;</th>
                    <th>Kundnr</th>
                    <th>Kund</th>
                    <th>Utgående SMS</th>
                    <th>Antal konton</th>
                    <th>Virtuellt nummer</th>
                    <th>Misslyckade meddelanen</th>
                </tr>
            </thead>
            <tbody>
            
    <?php
    
    $_30_days_back = date("Y-m-d H:i:s", strtotime("-30 days"));
    $_60_days_back = date("Y-m-d H:i:s", strtotime("-60 days"));
    
    $STGetCustomers = $DB->prepare("SELECT ID,Name, VirtualNumber, CreateTime, MaxAccounts, Locked, (SELECT SUM(ConcatCount) FROM Messages WHERE Messages.CustomerID = Customers.ID AND `SendTime` IS NOT NULL AND `SendTime` >= ?) AS LastSMSCount, (SELECT SUM(ConcatCount) FROM Messages WHERE Messages.CustomerID = Customers.ID AND `SendTime` IS NOT NULL AND `SendTime` < ? AND `SendTime` >= ?) AS PrevSMSCount , (SELECT COUNT(ID) FROM Accounts WHERE Accounts.CustomerID = Customers.ID AND Accounts.`Deleted` = 0) AS AccountCount, (SELECT COUNT(ID) FROM Messages WHERE Messages.CustomerID=Customers.ID AND (Messages.`Status` = ".MESSAGESTATUS::SENDFAIL." OR Messages.`Status` = ".MESSAGESTATUS::DELIVERYFAILED.") AND Messages.`CreateTime` > ?) AS ErrorMessageCount FROM Customers WHERE Deleted=0"); 
    
    $STGetCustomers->execute(array($_30_days_back,$_30_days_back,$_60_days_back,$_30_days_back));
    
    
    while($a=$STGetCustomers->fetch(PDO::FETCH_ASSOC))
    {
        
        
        if(!is_numeric($a['LastSMSCount'])) $a['LastSMSCount'] = 0;
        if(!is_numeric($a['PrevSMSCount'])) $a['PrevSMSCount'] = 0;
        
        $trafficIncrease = ($a['LastSMSCount'] > $a['PrevSMSCount']);
        $trafficDecrease = ($a['LastSMSCount'] < $a['PrevSMSCount']);
        
        echo('
                <tr>
                    <td style="text-align: center;"><img src="img/'.($a['Locked']? 'lock.png':'unlock.png').'" style="margin-right: 15px; cursor: pointer;" id="customerlock_'. $a['ID'] .'" onclick="SetBusinessLock(this,'. $a['ID'] .','.( $a['Locked'] ? "0" : "1" ).');return false;"/></td>
                    <td>'. $a['ID'] .'</td>
                    <td><a href="viewbusiness.php?id='. $a['ID'] .'">'. $a['Name'] .'</a></td>
                    <td>' . ($trafficIncrease?'<img src="img/up-arrow.png"/>':'') . ($trafficDecrease?'<img src="img/down-arrow.png"/>':'') . $a['LastSMSCount'].'</td>
                    <td><span><img src="img/account.png" style="margin-right: 4px;" />'. $a['AccountCount'] .' / '. $a['MaxAccounts'] .'</span></td>
                    <td>'. Phonenumber::GetDisplayStyle($a['VirtualNumber']) .'</td>
					<td><a href="/messages.php?onlyerror=1&customerid='. $a['ID'] .'"><span style="width: 28px; height: 28px; border-radius: 14px; background:#f4b620; color:#e14040; display: inline-block; text-align: center; font-weight: 400;">'. $a['ErrorMessageCount'] .'</span></a></td>
                </tr>
        
        ');
    }
    
    ?>
            
            </tbody>
        </table>
        
        <p>
        	Utgående SMS och misslyckade meddelanden avser perioden efter <?php echo $_30_days_back; ?>
        </p>
        
    </div>


<?php  include("footer.php"); ?>