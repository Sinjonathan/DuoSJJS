<?php

namespace Core\Exception;

class DriverNotSupportedException extends \Exception {
	
	public function __construct($driver, $message = null, $code = 0, Exception $previous = null) {
		if (is_null ( $message )) {
			$message = "Driver [" . $driver . "] not supported";
		}
		
		parent::__construct ( $message, $code, $previous );
		echo __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>