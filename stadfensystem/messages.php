<?php

require_once("database.php");
require_once("cellsyntsystem.php");
require_once("conversations.php");

$ST_STOREMESSAGE = $DB->prepare("INSERT INTO `Messages`(`Direction`, `Destination`, `Originator`, `Content`, `Status`, `ErrorMessage`, `AccountID`, `CustomerID`, `CreateTime`, `SendTime`,`DeliveryTime`,`SendAttempts`,`ConcatCount`,`RemoteNumber`,`ConversationID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

class MESSAGESTATUS
{
	const CREATED = 0;
	const SENT = 1;
	const SENDFAIL = 2;
	const DELIVERED = 3;
	const DELIVERYFAILED = 4;
	const DELIVERYBUFFERED = 5;
	const DELIVERY_UNKNOWN= 6;
	const RECEIVED = 7;
}

$messagestatus_labels =
array
(
	MESSAGESTATUS::CREATED => "Skapat",
	MESSAGESTATUS::SENT => "Skickat",
	MESSAGESTATUS::SENDFAIL => "Misslyckades att skicka",
	MESSAGESTATUS::DELIVERED => "Levererat",
	MESSAGESTATUS::DELIVERYFAILED => "Misslyckades att leverera",
	MESSAGESTATUS::DELIVERYBUFFERED => "Buffrat",
	MESSAGESTATUS::DELIVERY_UNKNOWN => "Okänd",
	MESSAGESTATUS::RECEIVED => "Mottaget",
);

class MESSAGEDIRECTION
{
	const OUT = 1;
	const IN = 2;
}

const DEFAULT_CONTRYCODE = "46";

function StoreMessage($Direction, $Destination, $Originator, $Content, $Status, $ErrorMessage, $AccountID, $CustomerID, $SendTime, $DeliveryTime,$SendAttempts,$ConcatCount,$ConversationID = NULL)
{
	global $DB,$ST_STOREMESSAGE;
	$ST_STOREMESSAGE->execute(array($Direction, $Destination, $Originator, $Content, $Status, $ErrorMessage, $AccountID, $CustomerID, date("Y-m-d H:i:s"), $SendTime, $DeliveryTime,$SendAttempts,$ConcatCount,(($Direction == MESSAGEDIRECTION::OUT)?$Destination:$Originator),$ConversationID));
	return $DB->lastInsertId();
}



//Receiver has to be in international standard phone number: 0046etc
function SendOutgoingMessage($Content, $Receiver, $AccountID)
{
	global $DB;
	//Fetch account
	$stAccountInfo = $DB->prepare("SELECT Accounts.ID AS ID, Customers.VirtualNumber AS VirtualNumber, Accounts.CustomerID AS CustomerID FROM Accounts INNER JOIN Customers ON Customers.ID = Accounts.CustomerID WHERE Accounts.ID=?;");
	$stAccountInfo->execute(array($AccountID));
	$accountInfo = $stAccountInfo->fetch(PDO::FETCH_ASSOC);
	
	
	//Cellynt send parameters
	$Parameters = array(
		"username" => CellsyntSystem::$USERNAME,
		"password" => CellsyntSystem::$PASSWORD,
		"destination" => Phonenumber::GetCellsyntDestinationStyle($Receiver),
		"text" => $Content,
		"originator" => $accountInfo['VirtualNumber'],
		"originatortype" => "numeric",
		"allowconcat"=>6,
		"charset"=>"UTF-8",
	);
	
	//Send SMS
	$result = CellsyntSystem::SendSMSRequest($Parameters);
	
	$messageStatus;
	$trackingIDs = NULL;
	$error = NULL;
	$sendTime = NULL;
	
	//Matching "OK:<mellanslag><trackingid>"
	if(preg_match('/^OK:\s[^"]+$/',$result))
	{
		$messageStatus = MESSAGESTATUS::SENT;
		$splitted = explode(' ',trim($result));
		$trackingIDSTR = $splitted[1];
		$trackingIDs = explode(",",$trackingIDSTR);
		$sendTime = date("Y-m-d H:i:s");
	}
	else
	{
		$error = "(1)Cellsynt: " . $result;
		$messageStatus = MESSAGESTATUS::SENDFAIL;
	}
	
	$concatLength = (is_array($trackingIDs) ? count($trackingIDs) : NULL);
	
	//Store localy
	$messageID = StoreMessage(MESSAGEDIRECTION::OUT, $Receiver, $accountInfo['VirtualNumber'], $Content, $messageStatus, $error, $AccountID, $accountInfo['CustomerID'], $sendTime, NULL,1,$concatLength);
	
	//Create conversation-object if it doesn't exists
	$ConvObject = Conversation::GetConversationObj2($accountInfo['CustomerID'], $Receiver);
	if($ConvObject == NULL)
	{
		$__cid = Conversation::Create($accountInfo['CustomerID'], $Receiver, NULL, $messageID, date("Y-m-d H:i:s"));
		$ConvObject = Conversation::GetConversationObj($__cid);
	}	
	else
		Conversation::SetLastMessageID($ConvObject->ID,$messageID, date("Y-m-d H:i:s"));
		
	//Update message in db, set conversation ID
	$STUpdate = $DB->prepare("UPDATE Messages SET ConversationID=? WHERE ID=?;");
	$STUpdate->execute(array($ConvObject->ID,$messageID));
	$STUpdate->closeCursor();
	
	//If conversation is inactive for this user, activate it
	Conversation::SetActivation($ConvObject->ID, $AccountID, TRUE);
	
	if($error === NULL)
	{
		//Store trackingIDs
		$localST_storeTID = $DB->prepare("INSERT INTO `CellsyntTrackingIDs`(`MessageID`,`Destination`, `CellsyntStatus`, `TrackingID`, `DeliveryTime`) VALUES (?,?,?,?,?)");
		
		foreach($trackingIDs as $tid)
		{
			$localST_storeTID->execute(array($messageID,$Receiver,NULL,$tid,NULL));
		}
	}
	
	$ret = new stdClass();
	$ret->ConvID = $ConvObject->ID;
	$ret->Success = ($messageStatus == MESSAGESTATUS::SENT);
	$ret->messageID = $messageID;
	$ret->SendTime = date("Y-m-d H:i:s");
	
	return $ret ;
}

function SetMessageDeliveryStatus($MessageID, $Status)
{
	global $DB;
	$stUpdate = $DB->prepare("UPDATE Messages SET `Status`=?, `DeliveryTime`=? WHERE `ID`=?;");
	
	$DeliveryTime = date("Y-m-d H:i:s");
	
	$stUpdate->execute(array($Status,$DeliveryTime,$MessageID));
}

function SetTrackingIDDeliveryStatus($Destination, $TrackingID, $CellsyntStatus)
{
	global $DB;
	$stGet = $DB->prepare("SELECT `MessageID` FROM CellsyntTrackingIDs WHERE `Destination`=? AND `TrackingID`=?;");
	
	
	$stGet->execute(array($Destination,$TrackingID));
	if($stGet->rowCount() == 0)
		return FALSE;
	$info = $stGet->fetch(PDO::FETCH_ASSOC);
	$MessageID = $info['MessageID'];
	
	$stGet->closeCursor();
	
	$statusConvert = array(
	CellsyntSystem::CS_MESSAGESTATUS_BUFFERED => CellsyntSystem::STADFENCS_MESSAGESTATUS_BUFFERED,
	CellsyntSystem::CS_MESSAGESTATUS_DELIVERED => CellsyntSystem::STADFENCS_MESSAGESTATUS_DELIVERED,
	CellsyntSystem::CS_MESSAGESTATUS_FAILED => CellsyntSystem::STADFENCS_MESSAGESTATUS_FAILED
	);
	
	$nStatus = (isset($statusConvert[$CellsyntStatus]) ? $statusConvert[$CellsyntStatus] : 0);
	
	$stUpdate = $DB->prepare("UPDATE CellsyntTrackingIDs SET `CellsyntStatus`=?, `DeliveryTime`=? WHERE `Destination`=? AND `TrackingID`=?;");
	$stUpdate->execute(array($nStatus,date("Y-m-d H:i:s"),$Destination,$TrackingID));	
	EvaluteMessageDeliveryStatus($MessageID);
	return TRUE;
}

function EvaluteMessageDeliveryStatus($MessageID)
{
	global $DB;
	$stAll = $DB->prepare("SELECT count(ID) FROM CellsyntTrackingIDs WHERE `MessageID`=?;");
	$stStatus = $DB->prepare("SELECT count(ID) FROM CellsyntTrackingIDs WHERE `MessageID`=? AND `CellsyntStatus`=?;");
	
	$stAll->execute(array($MessageID));
	$rows = $stAll->fetch(PDO::FETCH_NUM);
	$countAll = $rows[0];
	
	if($countAll == 0)
	{
		SetMessageDeliveryStatus($MessageID, MESSAGESTATUS::DELIVERY_UNKNOWN);
		return;
	}
	
	$stStatus->execute(array($MessageID,CellsyntSystem::STADFENCS_MESSAGESTATUS_FAILED));
	$rows = $stStatus->fetch(PDO::FETCH_NUM);
	$countFailed = $rows[0];
	
	if($countFailed > 0)
	{
		SetMessageDeliveryStatus($MessageID, MESSAGESTATUS::DELIVERYFAILED);
		return;
	}
	
	$stStatus->execute(array($MessageID,CellsyntSystem::STADFENCS_MESSAGESTATUS_BUFFERED));
	$rows = $stStatus->fetch(PDO::FETCH_NUM);
	$countBuffereed = $rows[0];
	
	if($countBuffereed > 0)
	{
		SetMessageDeliveryStatus($MessageID, MESSAGESTATUS::DELIVERYBUFFERED);
		return;
	}
	
	$stStatus->execute(array($MessageID,CellsyntSystem::STADFENCS_MESSAGESTATUS_DELIVERED));
	$rows = $stStatus->fetch(PDO::FETCH_NUM);
	$countDelivered = $rows[0];
	
	if($countDelivered == $countAll)
	{
		SetMessageDeliveryStatus($MessageID, MESSAGESTATUS::DELIVERED);
	}
}

function InternationalPhonenumber($phonenumber)
{
	$number = trim($phonenumber);
	
	//Remove any - and spaces
	$number = str_replace(array(" ","-"),"",$number);
	
	//if starts with +, replace with 00
	if(substr($number,0,1) === "+")
		$number = preg_replace('/\+/',"",$number,1);
	
		
	/*//Make sure it starts with countrycode
	if(strpos($number,"00") !== 0)
	{
		//Remove any leading zeroes
		if(substr($number,0,1) == "0")
			$number = substr($number,1);
		
		$number = DEFAULT_CONTRYCODE . $number;
	}*/
	
	//Remove any leading zeroes
	if(substr($number,0,1) == "0")
		$number = substr($number,1);
	return $number;
}



class Phonenumber
{
	public static function ParseToStandard($number)
	{
		//Remove -
		$number = str_replace("-","",$number);
		//Check if it starts with countycode
		if(strpos($number,"00") === 0)
		{
			//Remove leading zeroes
			return ltrim($number, "0");
		}
		else if(strpos($number,"+") === 0)
		{
			return ltrim($number, "+");
		}
		else
		{
			//If it didn't start with countrycode, remove leading zeroes and dd default contrycode
			return DEFAULT_CONTRYCODE . ltrim($number, "0");
		}
	}
	
	public static function GetDisplayStyle($standardNumber)
	{
		return "+" . $standardNumber;
	}
	
	public static function GetCellsyntDestinationStyle($standardNumber)
	{
		return "00" . $standardNumber;
	}
}

function ParseMessageCode($Text, $AccountObj, $CustomerObj)
{
	$COMPANYNNAME = $CustomerObj->Name;
	$DISPLAYNAME = $AccountObj->DisplayName ? $AccountObj->DisplayName : "";
	
	$search = array("{COMPANYNAME}","{DISPLAYNAME}");
	$replace = array($COMPANYNNAME,$DISPLAYNAME);
	
	return str_replace($search, $replace, $Text);
}