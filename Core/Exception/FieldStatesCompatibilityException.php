<?php

namespace Core\Exception;

class FieldStatesCompatibilityException extends \Exception {
	
	public function __construct($field, $expectState, $invalidCondition, $message = null, $code = 0, Exception $previous = null) {
		if (is_null ( $message )) {
			$message = "Conflict on [" . $field . "] to set [" . $expectState . "] cause of [" . $invalidCondition . "]";
		}
		
		parent::__construct ( $message, $code, $previous );
		echo __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>