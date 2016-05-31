<?php
require_once("database.php");
class IOSNotification
{
	public static function Join($AccountID, $DeviceToken)
	{
		global $DB;
		$ST = $DB->prepare("DELETE FROM `IOSUsers` WHERE `DeviceToken`=?;");
		$ST->execute(array($DeviceToken));
		$ST->closeCursor();
		$ST = $DB->prepare("INSERT INTO `IOSUsers`(`DeviceToken`, `AccountID`) VALUES (?,?);");
		$ST->execute(array($DeviceToken,$AccountID));
		return $DB->lastInsertId(); //Return IOS-User-ID
	}
	
	
	// Checks whether the format of the device token is correct (64 hexadecimal
	// characters). Note: we have no means to verify whether the device token
	// was really issued by APNS and corresponds to an actual device.
	public static function isValidDeviceToken($deviceToken)
	{
		if (strlen($deviceToken) != 64)
			return false;

		if (preg_match("/^[0-9a-fA-F]{64}$/", $deviceToken) == 0)
			return false;

		return true;
	}
	
	public static function Leave($IOSUserID,$AccountID)
	{
		global $DB;
		//$ST = $DB->prepare("DELETE FROM `IOSUsers` WHERE ID=? AND AccountID=?;"); TODO: Uncomment when deploying
		//$ST->execute(array($IOSUserID,$AccountID));
	}
	
	public static function Update($IOSUserID,$AccountID,$DeviceToken)
	{
		global $DB;
		$ST = $DB->prepare("UPDATE `IOSUsers` SET DeviceToken=? WHERE ID=? AND AccountID=?;");
		$ST->execute(array($DeviceToken,$IOSUserID,$AccountID));
	}
	
	public static function Send($apns,$deviceToken, $payload)
	{
		$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
		fwrite($apns, $apnsMessage);
	}
	
	public static function BeginSend()
	{
		$apnsHost = 'gateway.sandbox.push.apple.com';// //TODO, change to public server when deploying the app
		$apnsPort = 2195;
		$apnsCert = dirname(__FILE__) .'/ioscert/ioscert.pem';
		$error = 123;
		$errorString = "Fel";
		
		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
		
		$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
		
		return $apns;
	}
	
	public static function EndSend($apns)
	{
		fclose($apns);
	}
	
	public static function AddForSend($deviceToken, $payload)
	{
		global $DB;
		
		$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
		$ST = $DB->prepare("INSERT INTO `APN`(`DeviceToken`,`Body`) VALUES (?,?);");
		$ST->execute(array($deviceToken,$payload));
	}
}

class AndroidNotification
{
	public static function Join($AccountID, $RegistrationID)
	{
		global $DB;
		$ST = $DB->prepare("DELETE FROM `AndroidUsers` WHERE `RegistrationID`=?;");
		$ST->execute(array($RegistrationID));
		$ST->closeCursor();
		$ST = $DB->prepare("INSERT INTO `AndroidUsers`(`RegistrationID`, `AccountID`) VALUES (?,?);");
		$ST->execute(array($RegistrationID,$AccountID));
		return $DB->lastInsertId(); //Return Android-User-ID
	}
	
	
	public static function Leave($AndroidUserID,$AccountID)
	{
		$ST = $DB->prepare("DELETE FROM `AndroidUsers` WHERE ID=? AND AccountID=?;");
		$ST->execute(array($AndroidUserID,$AccountID));
	}
	
	public static function Update($AndroidUserID,$AccountID,$RegistrationID)
	{
		$ST = $DB->prepare("UPDATE `AndroidUsers` SET RegistrationID=? WHERE ID=? AND AccountID=?;");
		$ST->execute(array($RegistrationID,$AndroidUserID,$AccountID));
	}
	
	public static function SendGoogleCloudMessage( $data, $ids )
	{
		//------------------------------
		// Replace with real GCM API 
		// key from Google APIs Console
		// 
		// https://code.google.com/apis/console/
		//------------------------------
	
		$apiKey = 'AIzaSyAf_nJMNlBs1qSE7XcLLAjPh55MgnPdvQk';
	
		//------------------------------
		// Define URL to GCM endpoint
		//------------------------------
	
		$url = 'https://android.googleapis.com/gcm/send';
	
		//------------------------------
		// Set GCM post variables
		// (Device IDs and push payload)
		//------------------------------
	
		$post = array(
						'registration_ids'  => $ids,
						'data'              => $data,
						);
	
		//------------------------------
		// Set CURL request headers
		// (Authentication and type)
		//------------------------------
	
		$headers = array( 
							'Authorization: key=' . $apiKey,
							'Content-Type: application/json'
						);
	
		//------------------------------
		// Initialize curl handle
		//------------------------------
	
		$ch = curl_init();
	
		//------------------------------
		// Set URL to GCM endpoint
		//------------------------------
	
		curl_setopt( $ch, CURLOPT_URL, $url );
	
		//------------------------------
		// Set request method to POST
		//------------------------------
	
		curl_setopt( $ch, CURLOPT_POST, true );
	
		//------------------------------
		// Set our custom headers
		//------------------------------
	
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	
		//------------------------------
		// Get the response back as 
		// string instead of printing it
		//------------------------------
	
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	
		//------------------------------
		// Set post data as JSON
		//------------------------------
	
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );
	
		//------------------------------
		// Actually send the push!
		//------------------------------
	
		$result = curl_exec( $ch );
	
		//------------------------------
		// Error? Display it!
		//------------------------------
	
		if ( curl_errno( $ch ) )
		{
			echo 'GCM error: ' . curl_error( $ch );
		}
	
		//------------------------------
		// Close curl handle
		//------------------------------
	
		curl_close( $ch );
	
		//------------------------------
		// Debug GCM response
		//------------------------------
	
		$presult = json_decode($result);
		//print_r($presult);
	}
}