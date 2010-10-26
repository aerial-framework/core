<?php 
    class Aerial_Exception extends Exception
    {
        const CONNECTION = "Error connecting to database";
	    const UNKNOWN = "Unknown exception";

		public $aerialLog;

	    public function __construct($message, Exception $ex=null)
	    {
			$this->message = $message ? $message : self::UNKNOWN;
			$this->code = ($ex ? $ex->getCode() : 0);

			if(!PRODUCTION_MODE)
				$this->aerialLog = "Hello from AMFPHP error handling!";
	    }
    }
?>