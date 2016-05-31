<?php

require_once("admin.php");
require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/loginsession.php");
require_once("../../stadfensystem/system.php");
$page_id = "page-sessions";

$showHistory = (isset($_GET['history']) && $_GET['history'] == "1");

?>

<?php include("header.php"); ?>

<div style="position: relative;">
    
    
    <script>
	
	var closetype_kick = "<?php echo $loginsessionclosetype_labels[LoginSession::CLOSETYPE_KICK]; ?>";

function kickSession(sessionID)
{
	$.modernizedGET("/ajax/kicksession.php?id=" + sessionID,null,function(data)
	{
		$("tr.sess_" + sessionID + " .col_status").html("Stängd");
		$("tr.sess_" + sessionID + " .col_btns").html("Kickad");
	})
}
function kickAccount(accountID)
{
	$.modernizedGET("/ajax/kickaccount.php?id=" + accountID,null,function(data)
	{
		$("tr.acc_" + accountID + " .col_status").html("Stängd");
		$("tr.acc_" + accountID + " .col_btns").html("Kickad");
	})
}
</script>
    
    <div style="padding: 40px 40px;">
    <h1 style="border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;">Sessioner</h1>
    
    <div style="margin-top: 15px;">
    <?php
	
	if($showHistory)
	{
		echo('<a class="btn btn-default btn-medium" href="/sessions.php?history=0" role="button">Dölj historik</a>');
	}
	else
	{
		echo('<a class="btn btn-default btn-medium" href="/sessions.php?history=1" role="button">Visa historik</a>');
	}
	
	?>
    </div>
    
    <div style="margin-top: 25px; color:#909090;">
        <p>
        <?php
        
            $expire_webapp = (int)System::SystemVariable("WEBAPP_SESSION_EXPIRE");
            $expire_mobileapp = (int)System::SystemVariable("MOBILEAPP_SESSION_EXPIRE");
            
            echo("Sessioner i Webappen utgår efter " . $expire_webapp . " sekunder (" . $expire_webapp/60 . " minuter)<br>");
            echo("Sessioner i Mobilenappen utgår efter " . $expire_mobileapp . " sekunder (" . $expire_mobileapp/60 . " minuter)");
        ?>
        </p>
    </div>
    
    <table class="list business-customers-list">
        <thead>
            <tr>
                <th>Session-ID</th>
                <th>Status</th>
                <th>Konto-ID</th>
                <th>E-postadress</th>
                <th>Typ</th>
                <th>Påbörjad</th>
                <th>Senaste kommunikation</th>
                <?php if($showHistory) {?>
                <th>Avslutad</th>
                <th>Avslutad pga</th>
                
                <?php } ?>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        
<?php

$STGetCustomers = $DB->prepare("SELECT 
LoginSession.ID AS SessionID,
Accounts.ID AS AccountID,
Accounts.EmailAddress AS AccountEmail,
LoginSession.SessionType AS SessionType,
LoginSession.Opened AS Opened,
LoginSession.LastResponse AS LastResponse,
LoginSession.Closed AS Closed,
LoginSession.CloseType AS CloseType
 FROM LoginSession INNER JOIN Accounts ON Accounts.ID = LoginSession.AccountID WHERE ". ($showHistory ? "1" : "LoginSession.Closed IS NULL") ." ORDER BY LoginSession.ID DESC;");

$STGetCustomers->execute();


while($a=$STGetCustomers->fetch(PDO::FETCH_ASSOC))
{
	
	$isLocked = $a['AccountLocked'] ? true : false;
	
	
	echo('
			<tr class="rowclass acc_'.$a['AccountID'].' sess_'.$a['SessionID'].'">
				<td>'. $a['SessionID'] .'</td>
				<td class="col_status">'. (($a['Closed']==NULL) ? "Öppen" : "Stängd") .'</td>
				<td>'. $a['AccountID'] .'</td>
				<td>'. $a['AccountEmail'] .'</td>
				<td>'. $loginsessiontype_labels[$a['SessionType']] .'</td>
				<td>'. $a['Opened'] .'</td>
				<td>'. $a['LastResponse'] .'</td>');
				
				
				if($showHistory)
				{
					echo('
					
					<td>'. $a['Closed'] .'</td>
					<td class="col_closetype">'. $loginsessionclosetype_labels[$a['CloseType']] . '</td>
					');
				}
 	echo('
	
				<td class="col_btns">
				'.(($a['Closed']==NULL) ? '
					<a class="btn btn-default btn-small" href="javascript:kickSession('. $a['SessionID'] .');" role="button">Kicka session</a>
					<a class="btn btn-default btn-small" href="javascript:kickAccount('. $a['AccountID'] .');" role="button">Kicka konto</a>
				' : "").'
				</td>
            </tr>
	
	');
}

?>

        </tbody>
    </table>
    </div>
    
</div>
<?php include("footer.php"); ?>