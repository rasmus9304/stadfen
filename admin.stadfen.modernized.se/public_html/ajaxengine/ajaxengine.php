<?php

const INPUTSTATUS_GOOD = 0;
const INPUTSTATUS_ERROR = 1;

class AjaxEngine
{
	public $Data;
	public $Javascript;
	private $FormData;
	private $ManualData;
	
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
		
		echo json_encode($_data);
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
	
	
	public static $_ERRORS = array();
}
