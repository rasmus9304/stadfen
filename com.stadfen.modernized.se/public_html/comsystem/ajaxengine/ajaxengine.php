<?php

const INPUTSTATUS_GOOD = 0;
const INPUTSTATUS_ERROR = 1;

class AjaxEngine
{
	public $Data;
	public $Javascript;
	private $FormData;
	private $ManualData;
	private $echoManual;
	private $debugMessage;
	
	private $finished;
	
	public $AutoFinish;
	
	function __construct()
	{
		$this->Data = new stdClass();
		$this->Javascript = NULL;
		$this->FormData = array();
		$this->ManualData = "";
		$this->finished = FALSE;
		$this->AutoFinish = TRUE;
		$this->echoManual = 0;
		$this->debugMessage = "";
		ob_start();
	}
	
	function __destruct() 
	{
    	if(!$this->finished && $this->AutoFinish)
			$this->Finish();
   	}
	
	
	public function Finish()
	{
		$this->finished = true;
		
		$_data = new stdClass();
		
		$_data->Data = $this->Data;
		$_data->Javascript = $this->Javascript;
		$_data->FormData = $this->FormData;
		$_data->ManualData = ob_get_clean();
		$_data->EchoManual = $this->echoManual;
		$_data->Time = time();
		$_data->DebugMessage = $this->debugMessage;
		
		echo json_encode($_data); die;
	}
	
	public function SetInputStatus($ElementID, $Status=INPUTSTATUS_GOOD, $Message=NULL, $Value=NULL)
	{
		//Remove if exists
		$count = count($this->FormData);
		for($i = 0; $i < $count; $i++)
		{
			if($this->FormData[$i]->ElementID == $ElementID)
			{
				$this->FormData = array_splice($this->FormData, $i--, 0);
				$count--;
			}
		}
		
		$temp = new StdClass();
		$temp->ElementID = $ElementID;
		$temp->Status = $Status;
		$temp->Message = $Message;
		$temp->Value = $Value;
		
		
		
		$this->FormData[] = $temp;
		
		$temp->Errors = self::$_ERRORS;
	}
	
	public function FormularSuccess()
	{
		foreach($this->FormData as $s)
		{
			if($s->Status == INPUTSTATUS_ERROR)
				return FALSE;
		}
		return true;
	}
	
	public function SetEchoManual()
	{
		$this->echoManual = 1;
	}
	
	public function SetEchoManualIfAny()
	{
		$this->echoManual = 2;
	}
	
	public function EchoDebug($data)
	{
		$this->debugMessage .= $data;
	}
	
	
	public static $_ERRORS = array();
}
