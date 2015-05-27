<?php

namespace Core\Exception;

class LoadValuesInEmptyFieldListException extends \Exception {
	
	public function __construct($message = null, $code = 0, Exception $previous = null) {
		if (is_null ( $message )) {
			$message = "No fields founds, try to add fields before load values";
		}
		
		parent::__construct ( $message, $code, $previous );
		echo __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>