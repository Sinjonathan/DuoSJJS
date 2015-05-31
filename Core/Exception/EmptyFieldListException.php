<?php

namespace Core\Exception;

/**
 * EmptyFieldListException
 * @author Sébastien JOLY
 * @author Jonathan SANTONI
 */
class EmptyFieldListException extends \Exception {
	
	public function __construct($func, $message = null, $code = 0, Exception $previous = null) {
		if (is_null ( $message )) {
			$message = "No fields founds, try to add fields before [" . $func . ']';
		}
		
		parent::__construct ( $message, $code, $previous );
		echo __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>