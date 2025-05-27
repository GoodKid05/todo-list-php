<?php
namespace App\Middlewares;

use App\Http\JsonResponse;
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator as v;

class ValidateLoginUser 
{
	public function __construct(private LoggerInterface $logger)
	{
		$this->logger = $logger;		
	}

	public function __invoke($request, $next) 
	{
		if (empty($request['body'])) {
			$this->logger->warning('Валидация: тело запроса некорректно', ['body' => $request['body']]);

			return new JsonResponse([
				'error' => 'Тело запроса некорректно',
				'request_body' => $request['body']
			], 400);
		};

		$requestBody = $request['body'];
		$hasEmail = isset($requestBody['email']);
		$hasName = isset($requestBody['name']);
		$hasPassword = isset($requestBody['password']);

		if (!$hasEmail && !$hasName) {
			$this->logger->warning('Валидация: не указан ни email, ни имя пользователя');

			return new JsonResponse([
				'error' => 'Требуется указать либо email, либо имя пользователя'
			], 400);
		}

		if ($hasEmail && $hasName) {

			$this->logger->warning('Валидация: указан и email, и имя');

			return new JsonResponse([
				'error' => 'Нельзя одновременно указывать и email, и имя пользователя'
			], 400);
		}

		if ($hasEmail && !v::email()->isValid($requestBody['email'])) {
			$this->logger->warning('Валидация: невалидный email', ['email' => $requestBody['email']]);

			return new JsonResponse([
				'error' => 'Некорректный email'
			], 422);
		}

		if ($hasName && !v::alpha(' ')->isValid($requestBody['name'])) {
			$this->logger->warning('Валидация: невалидное имя пользователя', ['name' => $requestBody['name']]);

			return new JsonResponse([
				'error' => 'Некорректное имя пользователя. Только буквы.'
			], 422);
		}

		if (!$hasPassword) {
			$this->logger->warning('Валидация: пароль не передан');
			return new JsonResponse([
				'error' => 'Пароль обязателен'
			], 400);
		}

		if ($hasPassword && strlen($requestBody['password']) < 8) {
			$this->logger->warning('Валидация: пароль слишком короткий');

			return new JsonResponse([
				'error' => 'Некорректный пароль. Пароль должен быть не менее 8 символов'
			], 422);
		}

		$request['body']['login'] = $requestBody['email'] ?? $requestBody['name'];
		unset($request['body']['email'], $request['body']['name']);

		return $next($request);
	}

}
?>