<?php 
    class Aerial_Exception extends Exception
    {
        const CONNECTION = "Error connecting to database";
	    const UNKNOWN = "Unknown exception";

		public $debug;

	    public function __construct($message, $debug=null, Exception $ex=null)
	    {
			$this->message = $message ? $message : self::UNKNOWN;
			$this->code = ($ex ? $ex->getCode() : 0);

			if(!PRODUCTION_MODE)
				$this->debug = $debug;
	    }
    }
?>