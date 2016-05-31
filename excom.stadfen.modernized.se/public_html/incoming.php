<?php

require_once("../../stadfensystem/cellsyntsystem.php");
require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/messages.php");
require_once("../../stadfensystem/conversations.php");
require_once("../../stadfensystem/system.php");

require_once("../../stadfensystem/notificationservice.php");

$allowed_ips = System::SystemVariable("INCOMING_ALLOWED_IPS");
if(trim($allowed_ips) != "*" && !in_array($_SERVER['REMOTE_ADDR'], explode(";",$allowed_ips)))
	die("e1");

const F_DEST = 'destination';
const F_ORIGIN = 'originator';
const F_TEXT = 'text';
const F_USERDATA = 'udh';
const F_CHARSET = 'charset';

const F_CUSTOMERID = 'cid';
const F_INCOMINGKEY = 'x';
if(!isset($_REQUEST[F_DEST]) || !isset($_REQUEST[F_ORIGIN]) || !isset($_REQUEST[F_TEXT]))
	die("e2");

/*
//Some logging when debugging
$f = fopen("log.txt","a");

fwrite($f,  "?". $_SERVER['QUERY_STRING'] . "\n\r");
fclose($f);
*/	

$UserData;
$UserDataHeader;

if(empty($_REQUEST[F_USERDATA])) //If unspecified, assume one-part-sms
{
	$UserData = new stdClass();
	$UserData->MessageID = NULL;
	$UserData->TotalParts = 1;
	$UserData->PartID = NULL;
	
	$UserDataHeader = NULL;
}
else
{
	$UserData = CellsyntSystem::ParseUDH($_REQUEST[F_USERDATA]);
	
	$UserDataHeader = $_REQUEST[F_USERDATA];
}

//Set to specified charset, if unspecified use default
$Charset = (empty($_REQUEST[F_CHARSET]) ? CellsyntSystem::DEFAULT_INCOMING_CHARSET : $_REQUEST[F_CHARSET]);

$_text =/* htmlspecialchars*/(utf8_encode($_REQUEST[F_TEXT]));


//Attempt to identify customer
$CustomerID = NULL;

if(isset($_REQUEST[F_CUSTOMERID]) && isset($_REQUEST[F_INCOMINGKEY]) && is_numeric($_REQUEST[F_CUSTOMERID]))
{
	$STFindCust = $DB->prepare("SELECT ID FROM Customers WHERE ID=? AND IncomingKey=?;");
	$STFindCust->execute(array($_REQUEST[F_CUSTOMERID],$_REQUEST[F_INCOMINGKEY]));
	
	if($STFindCust->rowCount() > 0)
		$CustomerID = $_REQUEST[F_CUSTOMERID];
	$STFindCust->closeCursor();
}

$STStore = $DB->prepare("INSERT INTO `CellsyntIncoming`(`Destination`, `Originator`, `Text`, `Charset`, `CustomerID`, `UserDataHeader`, `UserDataMessageID`, `TotalParts`, `PartID`, `AllPartsReceived`, `ReceiveTime`) VALUES (?,?,?,?,?,?,?,?,?,?,?);");

$STStore->execute(array($_REQUEST[F_DEST],$_REQUEST[F_ORIGIN],$_text,$Charset,$CustomerID,$UserDataHeader,$UserData->MessageID,$UserData->TotalParts,$UserData->PartID,FALSE,date("Y-m-d H:i:s")));

$ID = $DB->lastInsertId();
$STStore->closeCursor();

//Evaluate if the full message have been received
$FullyReceived = false;
$MessageText = "";

if($UserData->TotalParts == 1)
{
	$FullyReceived = true;
	$MessageText = $_text;
}
else
{
	//Attempt to select received parts
	$STGet = $DB->prepare("SELECT `Text`, `Charset`, `PartID` FROM CellsyntIncoming WHERE `UserDataMessageID`=? AND `Destination`=? AND `Originator`=? AND `CustomerID`=? AND `IsDone`=0 ORDER BY `PartID` ASC;");
	$STGet->execute(array($UserData->MessageID,$_REQUEST[F_DEST],$_REQUEST[F_ORIGIN],$CustomerID));
	
	if($STGet->rowCount() >= $UserData->TotalParts) //If fewer parts have been received than required, no more validation has to be done
	{
		//Here the parts are validated to be the correct parts
		
		$STGet_res = $STGet->fetchAll(PDO::FETCH_ASSOC);
		
		//Create message-part array with NULL elements
		$parts = array();
		for($i = 0; $i < $UserData->TotalParts; $i++)
		{
			$parts[$i] = NULL;
		}
		
		//Populate with messagedata
		foreach($STGet_res as $a)
		{
			$parts[$a['PartID']] = $a['Text'];
		}
		
		//If no more null exists in the array, the full message have been received
		if(!in_array(NULL, $parts, TRUE))
		{
			$FullyReceived = true;
			$MessageText = implode("",$parts);
		}
	}
	
	
	if($STGet->rowCount() >= $UserData->TotalParts)
	{
		//Message fully received
		$FullyReceived = true;
		foreach($STGet_res as $a)
		{
			$MessageText .= $a['Text'];
		}
	}
	$STGet->closeCursor();
}


if($FullyReceived) //All parts have been received
{
	$MessageID = StoreMessage(MESSAGEDIRECTION::IN, $_REQUEST[F_DEST], $_REQUEST[F_ORIGIN], $MessageText, MESSAGESTATUS::RECEIVED, NULL, NULL, $CustomerID, NULL, date("Y-m-d H:i:s"), NULL, $UserData->TotalParts);
	
	
	//Check if Conversation exists
	$conv = Conversation::GetConversationObj2($CustomerID, $_REQUEST[F_ORIGIN]);
	$convID;
	if($conv == NULL)
	{
		$convID = Conversation::Create($CustomerID, $_REQUEST[F_ORIGIN],NULL,$MessageID, date("Y-m-d H:i:s"));
		$conv = Conversation::GetConversationObj($convID);
	}
	else
	{
		$convID = $conv->ID;
		Conversation::SetLastMessageID($convID, $MessageID,date("Y-m-d H:i:s"));
	}
	
	//Update message in db, set conversation ID
	$STUpdate = $DB->prepare("UPDATE Messages SET ConversationID=? WHERE ID=?;");
	$STUpdate->execute(array($conv->ID,$MessageID));
	$STUpdate->closeCursor();
		
	//Increate unread count
	Conversation::IncreaseNewMessageCount($convID, 1);
	
	//Remove archived status if archived
	if(intval($conv->Archived) > 0)
	{
		Conversation::SetArchiveStatus($conv->ID, FALSE);
	}
	
	
	if($UserData->TotalParts == 1)
	{
		//If one-part, update by ID
		$STUpdateStatus = $DB->prepare("UPDATE CellsyntIncoming SET `MessageID`=?, `IsDone`=1 WHERE ID=?;");
		$STUpdateStatus->execute(array($MessageID,$ID));
	}
	else
	{
		//else by other parameters
		$STUpdateStatus = $DB->prepare("UPDATE CellsyntIncoming SET `MessageID`=?, `IsDone`=1 WHERE `UserDataMessageID`=? AND `Destination`=? AND `Originator`=? AND `CustomerID`=? AND `IsDone`=0");
		$STUpdateStatus->execute(array($MessageID,$UserData->MessageID,$_REQUEST[F_DEST],$_REQUEST[F_ORIGIN],$CustomerID));
	}
	
	//Acquire all accounts in this customer that doesn't have silentmode on, have the conversation as active, and is not blocked for the given number
	$currentDay = intval(date("N"));
	$currentTime = date("H:i:s");
	$ST = $DB->prepare("SELECT Accounts.ID AS ID, ConversationAccounts.Nickname AS Nickname FROM Accounts
						LEFT JOIN ConversationAccounts ON ConversationAccounts.ConversationID = ? AND ConversationAccounts.AccountID = Accounts.ID
						WHERE Accounts.CustomerID=? AND Accounts.SilentMode = 0 AND ConversationAccounts.Active = 1 AND (ConversationAccounts.Blocked = 0 OR ConversationAccounts.Blocked IS NULL) AND ConversationAccounts.Active = 1
						
						AND (SELECT COUNT(ID) FROM SilentModeIntervals
							WHERE
								(
									StartDay < ?
									OR 
									(StartDay = ? AND StartTime <= ?)
								)
								
								AND
								(
									EndDay > ?
									OR
									(EndDay = ? AND EndTime >= ?)
								)
							) <= 0;");
	$ST->execute(array($convID, $conv->CustomerID, $currentDay, $currentDay, $currentTime, $currentDay, $currentDay, $currentTime));
	$accountids = array();
	$nicknames = array(); //Create an array containing conversation-nicknames for every account, will be used for APN
	$accountids[] = 0; //Must have at least on element
	while($a = $ST->fetch(PDO::FETCH_ASSOC))
	{
		$accountids[] = $a['ID'];
		$nicknames[$a['ID']] = $a['Nickname'];
	}
	$ST->closeCursor();
	
	$implAccountIDS = implode(',',$accountids);
	
	//Send Notifications to Android
	$ST = $DB->prepare("SELECT AndroidUsers.RegistrationID AS RegistrationID FROM AndroidUsers 
				WHERE AccountID IN (". $implAccountIDS .");");
	$ST->execute();
	$registrationIDS = array();
	
	while($a = $ST->fetch(PDO::FETCH_ASSOC))
	{
		$registrationIDS[] = $a['RegistrationID'];
	}
	
	$gcm_data = array();
	$gcm_data['message'] = $MessageText;
	$gcm_data['convid'] = $convID;
	
	AndroidNotification::SendGoogleCloudMessage($gcm_data,$registrationIDS);
	
	//Send Notifications to ios
	$ST = $DB->prepare("SELECT DeviceToken AS DeviceToken, AccountID AS AccountID FROM IOSUsers 
				WHERE AccountID IN (". $implAccountIDS .");");
	$ST->execute();
	$deviceTokens = array();
	
	$payload = array();
	$payload['aps'] = array('alert' => $MessageText, 'badge' => 1, 'sound' => 'default');
	$payload['server'] = array('serverId' => $serverId, 'name' => $name);
	
	$apn_trim = NULL;
	if(strlen($MessageText) > System::SystemVariable("APN_MESSAGELENGTH"))
		$apn_trim = substr($MessageText, 0, System::SystemVariable("APN_MESSAGELENGTH"));
	else
		$apn_trim = $MessageText; 
	
	while($a = $ST->fetch(PDO::FETCH_ASSOC))
	{
		$payload['aps']['alert'] = Conversation::GetConversationLabel($conv->Number, $conv->ConversationName, $nicknames[$a['AccountID']]) . ": " . $apn_trim;
		$payload_json = json_encode($payload);
		
		IOSNotification::AddForSend($a['DeviceToken'], $payload_json);
	}
}