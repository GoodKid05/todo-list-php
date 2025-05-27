<?php
namespace App\Middlewares;

use App\Http\JsonResponse;
use Psr\Log\LoggerInterface;

class ValidateRefreshToken 
{
	public function __construct(private LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function __invoke($request, $next) 
	{
		$body = $request['body'] ?? [];
		if(!isset($body['refresh_token'])) {
			$this->logger->warning('Валидация: не передан refresh_token', ['body' => $body]);
			return new JsonResponse(['error' =>'refresh_token обязателен'], 400);
		}

		if(isset($body['refresh_token']) && empty($body['refresh_token'])) {
			$this->logger->warning('Валидация: невалидный refresh_token', ['body' => $body]);
			return new JsonResponse(['error' =>'Невалидный refresh_token'], 422);
		}

		return $next($request);
	}
}
?>