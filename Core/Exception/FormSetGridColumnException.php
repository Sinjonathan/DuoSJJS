<?php

namespace Core\Exception;

class FormSetGridColumnException extends \Exception {
	
	public function __construct($incorrectSize, $message = null, $code = 0, Exception $previous = null) {
		if (is_null ( $incorrectSize )) {
			$message = "Given size [" . $incorrectSize . "] type not supported, please use Integer";
		}
		
		parent::__construct ( $message, $code, $previous );
		echo __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>