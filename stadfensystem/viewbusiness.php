<?php require_once("admin.php");

require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/cellsyntsystem.php");
include("header.php"); 


if(!isset($_GET['id']) ||!is_numeric($_GET['id']))
{
	header("Location: /");
	die;
}

$CustomerID = $_GET['id'];

$STCustomer = $DB->Prepare("SELECT Customers.ID AS CustomerID, Customers.Name AS Name, Customers.CorporateNumber AS CorporateNumber, (SELECT COUNT(ID) FROM Accounts WHERE Accounts.CustomerID = Customers.ID AND Accounts.`Deleted` = 0) AS AccountCount, Customers.MaxAccounts AS MaxAccounts, Customers.CreateTime AS CreateTime, Customers.Locked AS Locked, Accounts.EmailAddress AS MainAccountEmail, Customers.VirtualNumber AS VirtualNumber, Customers.IncomingKey AS IncomingKey FROM Customers LEFT JOIN Accounts ON Accounts.ID = Customers.MainAccountID WHERE Customers.ID=?;");
$STCustomer->execute(array($CustomerID));

if($STCustomer->rowCount() > 0)
{
	$info = $STCustomer->fetch(PDO::FETCH_ASSOC);
?>

<style>

h2 {
	border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;
}

</style>

<script>
var CustomerID = <?php echo $CustomerID; ?>;


function setMainAccount(accID,button)
{
	if(confirm("Är du säker på att du vill byte huvudkonto?"))
	{
		if(!$(button).modernizedButtonActive())
		{
			$(button).modernizedButtonGET("/ajax/setmainaccount.php?accid="+ accID + "&custid=" + CustomerID, null, function(data,data2)
			{
				$(".cell_mainacc").html("Nej");
				$(".cell_mainacc_" + accID).html("Ja");
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
	$.modernizedPOST("/ajax/customeroutgoingmessages.php",{customerid: CustomerID,startdate: startDate, enddate : endDate}, function(data,data2)
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

function onClickDelete()
{
	if(confirm("Är du säker på att du vill radera denna kund?") && confirm("Är du ALLDELLES säker?"))
	{
		var button = document.getElementById('btn_delete');
		if(!$(button).modernizedButtonActive())
		{
			$(button).modernizedButtonGET("/ajax/deletecustomer.php?custid="+ CustomerID,null,function(data)
			{
				window.location = "/index.php";
			});
		}
	}
}

var currentMaxAccounts;

function editMaxAccounts()
{
	var $label = $("#max_accounts_disp");
	var $label_inner = $("#max_accounts_disp_inner");
	var $edit = $("#max_accounts_edit");
	currentMaxAccounts = $label_inner.html();
	
	var $txtedit = $("#txteditmaxaccounts");
	$txtedit.val(currentMaxAccounts);
	
	$label.css("display","none");
	$edit.css("display","");
	

}

function saveMaxAccounts()
{
	var $button = $("#button_save_max");
	if(!$button.modernizedButtonActive())
	{
		var $label = $("#max_accounts_disp");
		var $label_inner = $("#max_accounts_disp_inner");
		var $edit = $("#max_accounts_edit");
		var $txtedit = $("#txteditmaxaccounts");
		
		var newVal = $txtedit.val();
		var newVal_int;
		
		//Make sure its 1 or higher
		try
		{
			newVal_int = parseInt(newVal);
			
			if(newVal_int <= 0)
				newVal_int = currentMaxAccounts;
		}
		catch (e)
		{
			newVal_int = currentMaxAccounts;
		}
		
		currentMaxAccounts = newVal_int;
		
		$button.modernizedButtonGET("/ajax/setcustomermaxaccounts.php?custid="+ CustomerID + "&maxaccounts=" + newVal_int,null,function()
		{
			$button.modernizedButton
		
		$label_inner.html(newVal_int);
		$label.css("display","");
		$edit.css("display","none");
		});
	}
	
}


</script>

<div style="width: 900px; margin: 0 auto;">
    <div>
        <h1 style="font-weight: 300; font-size: 3em;"><?php echo $info['Name']; ?></h1>
        <p style="font-size: 1.5em;font-weight: 300; color:#636363; margin-bottom: 15px;"><?php echo $info['CorporateNumber']; ?></p>
    </div>
    
    <div style="display: block; overflow: hidden;">
    	<div>
            <canvas id="bar-chart" width="1100" height="120"></canvas>
        </div>
    </div> 
       
    <br />
    <div>
    	<h2>Information</h2>
    </div>
    <div>
    	<table class="list business-customers-list">
        	<tbody>
            	<tr><td class="bold">Kundnr:</td><td><?php echo $info['CustomerID']; ?></td></tr>
                <tr><td class="bold">Antal konton (max):</td><td style=" <?php if(((int)$info['AccountCount']) > ((int)$info['MaxAccounts'])) echo("color:red;"); ?>"><?php echo $info['AccountCount'] . ' <span id="max_accounts_disp">(<span id="max_accounts_disp_inner">'. $info['MaxAccounts'] .'</span>)<a class="btn btn-default" href="javascript:editMaxAccounts();" role="button" id="button_edit_max">Ändra max</a></span><span id="max_accounts_edit" style="display:none;"><input type="text" id="txteditmaxaccounts"/><a class="btn btn-default" href="javascript:saveMaxAccounts();" role="button" id="button_save_max">Spara max</a></span>'; ?></td></tr>
                <tr><td class="bold">Skapad:</td><td><?php echo $info['CreateTime']; ?></td></tr>
                <tr><td class="bold">Låst:</td><td id="lock_label"><?php echo ($info['Locked'] ? "Ja" : "Nej"); ?></td></tr>
                <tr><td class="bold">E-postadress (För huvudkonto):</td><td><?php echo $info['MainAccountEmail']; ?></td></tr>
            </tbody>
        </table>
    </div>
    <br /><br />
    
    <div>
        <a class="btn btn-default btn-medium" href="/messages.php?customerid=<?php echo $CustomerID; ?>" role="button">Visa meddelandehistorik</a>
        <a class="btn btn-default btn-medium" href="/messages.php?onlyerror=1&customerid=<?php echo $CustomerID; ?>" role="button">Visa misslyckade meddelanden</a>
        <a class="btn btn-default btn-medium" href="javascript: switchLock();" role="button" id="btn_lock"><?php echo $info['Locked'] ? "Lås upp" : "Lås"; ?></a>
        <a class="btn btn-default btn-medium" href="javascript: onClickDelete();" role="button" id="btn_delete">Ta bort kund</a>
    </div>

    
    <div style=" border-bottom: 1px solid #E0E0E0;">
    <h2>Konton inom företagskunden</h2>
    </div>
    
    <script>
	
	var isLocked = <?php echo $info['Locked'] ? "true" : "false"; ?>;

function SetAccountLock(button,ID,lock)
{
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

function switchLock()
{
	var button = document.getElementById('btn_lock');
	var shallLock = !isLocked;
	if(!$(button).modernizedButtonActive())
	{
		$(button).modernizedButtonGET("/ajax/setcustomerlock.php?id="+ CustomerID + "&locked=" + (shallLock ? 1:0));
		
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

</script>
    
    <div style="position: relative;">
    <table class="list business-customers-list">
        <thead>
            <tr>
            	<th></th>
            	<th>Konto-nr</th>
                <th>E-postadress</th>
                <th>Huvudkonto</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        
<?php

$STGetAccounts = $DB->prepare("SELECT Accounts.ID AS AccountID,Accounts.Locked AS AccountLocked, Accounts.EmailAddress AS EmailAddress, CustomerID, Customers.Name AS CustomerName, Customers.MainAccountID AS MainAccountID FROM Accounts INNER JOIN Customers ON Customers.ID = Accounts.CustomerID WHERE Customers.ID = ? AND Accounts.`Deleted` = 0;");

$STGetAccounts->execute(array($CustomerID));


while($a=$STGetAccounts->fetch(PDO::FETCH_ASSOC))
{
	
	
	$isLocked = $a['AccountLocked'] ? true : false;
	
	echo('
			<tr>
				<td><img src="img/'.($isLocked? 'lock.png':'unlock.png').'" style="margin-right: 15px; cursor: pointer;" id="accountlock_'. $a['AccountID'] .'" onclick="SetAccountLock(this,'. $a['AccountID'] .','.( $isLocked ? "0" : "1" ).');return false;"/></td>
				<td>'.$a['AccountID'].'</td>
                <td><a href="/viewaccount.php?id='. $a['AccountID'] .'">'.$a['EmailAddress'].'</a></td>
				<td class="cell_mainacc cell_mainacc_'. $a['AccountID'] .'">'.(($a['MainAccountID']==$a['AccountID']) ? "Ja" : "Nej").'</td>
				<td><a class="btn btn-default btn-medium" href="javascript:setMainAccount('. $a['AccountID'] .',this);" role="button">Gör till huvudkonto</a></td>
            </tr>
	
	');
}

?>
            

        </tbody>
    </table>
    <div style="float: left; width: 50%;">
    </div>
    
    <div>
        <a class="btn btn-default btn-medium" href="/addaccount.php?pcid=<?php echo $CustomerID; ?>" role="button">Lägg till konto</a>
    </div>
    
    <script>
	
	var vn_isEditing = false;
	var vn_isRequesting = false;
	
	function editVirtualNumber()
	{
		if(vn_isRequesting)
			return;
			
		var $button = $("#button_vn");
		var $span = $("#virtualnumber_span");
		if(vn_isEditing)
		{
			var $input = $("#editvirtualnumber");
			var newV = $input.val();
			vn_isRequesting = true;
			$input.prop("disabled",true);
			$.modernizedPOST("/ajax/setvirtualnumber.php",{id:CustomerID, virtualnumber: newV},function(data)
			{
				if(data.Success)
				{
					$button.html("Ändra");
					$span.html(data.VirtualNumber);
					vn_isEditing = false
				}
				else
				{
					$input.prop("disabled",false);
				}
				vn_isRequesting = false;
			});
		}
		else
		{
			vn_isEditing = true;
			var preV = $span.html();
			
			$span.html('<input type="text" id="editvirtualnumber" value="'+ preV +'"/>');
			
			$button.html("Spara");
		}
	}
	
	</script>
    
    <div>
    	<h2>Cellsynt-inställningar</h2>
    </div>
    <div style="">
    	<table class="list business-customers-list">
        	<tbody>
            	<tr><td class="bold">Virtuellt nummer:</td><td><span id="virtualnumber_span"><?php echo $info['VirtualNumber']; ?></span> &nbsp;&nbsp;<a class="btn btn-default" href="javascript:editVirtualNumber();" role="button" id="button_vn">Ändra</a></td></tr>
                <tr><td class="bold">URL för ingående SMS:</td><td><?php echo CellsyntSystem::GenerateIncomingSMSURL($CustomerID, $info['IncomingKey']); ?></td></tr>
            </tbody>
        </table>
    </div>
    
    
</div>

</div>

<?php 

}
else

{
?>
<div style="width: 900px; margin: 0 auto;">
    <div style="border-bottom: 1px solid #ECECEC;">
        <p style="font-size: 1.5em;font-weight: 300; color:#636363; margin-bottom: 15px;">Kunde inte ladda kund</p>
    </div>
</div>

<?php
}

include("footer.php"); ?>