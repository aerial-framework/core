<?php 
    class AerialException extends Exception
    {
        const CONNECTION = "Error connecting to database";
	    const UNKNOWN = "Unknown exception";

	    public function __construct($message, Exception $ex=null)
	    {
			$this->message = $message ? $message : self::UNKNOWN;
			$this->code = ($ex ? $ex->getCode() : 0);
	    }
    }
?>