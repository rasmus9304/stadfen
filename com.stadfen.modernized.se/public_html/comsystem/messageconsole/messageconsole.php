<?php

class MessageConsole
{
	/*const PORT = 4335;
	public static function Log($msg)
	{
		if(empty($_SESSION['___messageconsole_ip']))
			$_SESSION['___messageconsole_ip'] = gethostbyname("lukashem.modernized.se");
		$ip = $_SESSION['___messageconsole_ip'];
		
		 $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

		$len = strlen($msg);
	
		socket_sendto($sock, $msg, $len, 0, $ip, self::PORT);
		socket_close($sock);
	}*/
	
	private $msgs = array();
	
	private $ended = false;
	
	public function _log($msg)
	{
		$this->msgs[] = $msg;
	}
	
	public static function Log($msg)
	{
		global $__messageconsole;
		$__messageconsole->_log($msg);
	}
	
	public static function End()
	{
		global $__messageconsole;
		$__messageconsole->_end();
	}
	
	function __descruct()
	{
		$this->_end();
	}
	
	public function _end()
	{
		if($this->ended)
			return;
		$this->ended = true;
		if(count($this->msgs) > 0)
		{
			//set POST variables
			$url = 'http://213.113.158.135/consolesystem/index.php';
			$fields = array();
			
			for($i = 0; $i < count($this->msgs); $i++)
			{
				$fields["f" . $i] = $this->msgs[$i];
			}
			
			//url-ify the data for the POST
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');
			
			//open connection
			$ch = curl_init();
			
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			
			//execute post
			$result = curl_exec($ch);
			
			die($result);
			
			//close connection
			curl_close($ch);
		}
	}
}

$__messageconsole = new MessageConsole();