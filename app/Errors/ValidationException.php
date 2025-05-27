<?php
namespace App\Errors;

use Exception;

class ValidationException extends Exception {
	private $errorCode;

	public function __construct($message, $code = 500){
		parent::__construct($message, $code);
		$this->errorCode = $code;
	}

	public function getErrorCode() {
		return $this->errorCode;
	}
}

?>