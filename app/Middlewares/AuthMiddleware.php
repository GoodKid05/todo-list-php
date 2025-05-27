<?php
namespace App\Middlewares;

use App\Errors\HttpException;
use App\Http\JsonResponse;
use App\Services\AuthService;
use Exception;


class AuthMiddleware 
{

	public function __construct(private AuthService $authService) {}

	public function __invoke($request, $next) 
	{
		$headers = getallheaders();
		if(!isset($headers['Authorization'])) {
			throw new HttpException('Отсутствует заголовок авторизации', 401);
		}

		$token = trim(str_replace('Bearer', '', $headers['Authorization']));

		try {
			$userId = $this->authService->validateAccessToken($token);
			$request['auth_user_id'] = $userId;

			return $next($request);
		} catch (Exception $e) {
			return new JsonResponse(['error' => 'Недействительный токен'], 401);
		}
	}
}
?>