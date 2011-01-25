<?php

class Error extends Exception {
	protected $type = 'Warning';
	protected $details = NULL;
	
	public function __construct($message, $details = NULL) {
		$this->setMessage($message);
		$this->setDetails($details);
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getDetails() {
		return $this->details;
	}
	
	protected function setMessage($value) {
		return $this->message = $value;
	}
	
	protected function setDetails($value) {
		return $this->details = $value;
	}
}