<?php


class CellsyntSystem
{
	public static $USERNAME = "stadfen";
	public static $PASSWORD = "aLRhcF1T";
	
	public static $SENDSMSCOST = 0.5;
	
	const DEFAULT_INCOMING_CHARSET = "ISO-8859-1";
	
	public static function SendSMSRequest($fields)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://se-1.cellsynt.net/sms.php");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
		
		
		//För simulering
		//return "OK: " . md5(rand());
		
		return curl_exec($curl);
	}
	
	public static function GenerateIncomingSMSURL($CustomerID, $IncomingKey)
	{
		return "http://excom.stadfen.modernized.se/incoming?cid=". $CustomerID . "&x=" . $IncomingKey;
	}
	
	public static function ParseUDH($udh)
	{
		$sdata = str_split($udh, 2);
		
		$ret = new stdClass();
		$ret->UDHLength = $sdata[0];
		$ret->IEIdentifier = $sdata[1];
		$ret->HeaderLength = $sdata[2];
		$ret->MessageID = hexdec($sdata[3]);
		$ret->TotalParts = hexdec($sdata[4]);
		$ret->PartID = hexdec($sdata[5]);
		
		return $ret;
	}
	
	const CS_MESSAGESTATUS_BUFFERED = "buffered";
	const CS_MESSAGESTATUS_DELIVERED = "delivered";
	const CS_MESSAGESTATUS_FAILED = "failed";
	
	const STADFENCS_MESSAGESTATUS_BUFFERED = 1;
	const STADFENCS_MESSAGESTATUS_DELIVERED = 2;
	const STADFENCS_MESSAGESTATUS_FAILED = 3;
}