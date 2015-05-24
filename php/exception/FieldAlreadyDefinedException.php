<?php

namespace form\php\exception;

class FieldAlreadyDefinedException extends \Exception { 
	
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}
	
	public function __toString() {
		return $this->message;
	}
	
}

?>