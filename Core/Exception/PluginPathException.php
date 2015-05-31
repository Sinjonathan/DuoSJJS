<?php

namespace Core\Exception;

class PluginPathException extends \Exception {
	
	public function __construct($location, $message = null, $code = 0, Exception $previous = null) {
		if (is_null ( $message )) {
			$message = "Plugin path not specified in [". $location ."]";
		}
		
		parent::__construct ( $message, $code, $previous );
		echo __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>