<?php 

namespace App\Middlewares;

use App\Http\JsonResponse;
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator as v;

class ValidateRegisterUser 
{
	public function __construct(private LoggerInterface $logger) {}

	public function __invoke($request, $next)
	{
		$requestBody = $request['body'];

		if(!isset($requestBody['name']) 
			|| !isset($requestBody['email']) 
			|| !isset($requestBody['password'])
		) {
			$this->logger->warning('Ошибка валидации: отсутствуют обязательные поля');
			return new JsonResponse([
				'error' => 'Заполните все обязательные поля',
				'request_body' => $request['body']
			], 400);
		}

		if (!v::alpha(' ')->isValid($requestBody['name'])) {
			$this->logger->warning('Ошибка валидации имени пользователя', ['name' => $requestBody['name']]);
			return new JsonResponse([
				'error' => 'Невалидное имя. Только буквы!',
			], 422);
		}

		if (!v::email()->isValid($requestBody['email'])) {
			$this->logger->warning('Ошибка валидации email', ['emai' => $requestBody['email']]);
			return new JsonResponse([
				'error' => 'Некорректный email'
			], 422);
		}

		if (strlen($requestBody['password']) < 8) {
			$this->logger->warning('Ошибка валидации пароля: слишком короткий');
			return new JsonResponse([
				'error' => 'Пароль должен содержать 8 символов',
			], 422);
		}

		return $next($request);
	}
}
?>