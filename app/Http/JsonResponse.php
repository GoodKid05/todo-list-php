<?php
namespace App\Http;


class JsonResponse 
{
	private $data;
	private $statusCode;
	private $headers;

	public function __construct(array $data = [], int $statusCode = 200, array $headers = []) 
	{
		$this->data = $data;
		$this->statusCode = $statusCode;
		$this->headers = $headers;
	}

	public function send(): void 
	{
		http_response_code($this->statusCode);
		header('Content-Type: application/json; charset=utf-8');

		foreach($this->headers as $name => $value) {
			header("$name: $value");
		}

		echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		exit;
	}

}
?>