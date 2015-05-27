<?php

namespace Core\Exception;

class FieldUndefinedException extends \Exception {
	
	public function __construct($fieldname, $message = null, $code = 0, Exception $previous = null) {
		if (is_null ( $message )) {
			$message = "Field [" . $fieldname . "] not defined";
		}
		
		parent::__construct ( $message, $code, $previous );
		echo __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>