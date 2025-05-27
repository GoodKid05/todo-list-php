<?php
namespace App\Errors;

use Exception;


class HttpException extends Exception {

	protected int $statusCode;

	public function __construct(string $message, int $statusCode) {
		parent::__construct($message);
		$this->statusCode = $statusCode;
	}

	public function getStatusCode(): int {
		return $this->statusCode;
	}

}

?>