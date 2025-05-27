<?php
namespace App\Controllers;

use App\Errors\AppException;
use App\Errors\HttpException;
use App\Http\JsonResponse;
use App\Services\AuthService;
use App\Services\UserService;
use Exception;

class AuthController 
{
	public function __construct(
		private UserService $userService, 
		private AuthService $authService
	) {}

	public function register($request) 
	{
		try {
			$data = $request['body'];
			$newUser = $this->userService->createUser($data);

			$response = new JsonResponse(['user' => $newUser], 201);
			$response->send();

		} catch(HttpException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();
		} catch (AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();
		} catch(Exception $e) {
			throw $e;
		}
	}

	public function login($request) 
	{
		try {
			$data = $request['body'];
			
			[$accessToken, $refreshToken] = $this->authService->login($data);

			$response = new JsonResponse([
				'access_token' => $accessToken,
				'refresh_token' => $refreshToken,
			]);
			$response->send();

		} catch (AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function logout($request)
	{
		try {
			$refreshToken = $request['body']['refresh_token'];

			$this->authService->logout($refreshToken);
			
			$response = new JsonResponse(['message' => 'Токен успешно отозван']);
			$response->send();

		} catch (HttpException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();

		} catch (AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function refreshToken($request)
	{
		try {
			$refreshToken = $request['body']['refresh_token'];
	
			[$accessToken, $refreshToken] = $this->authService->refreshToken($refreshToken);
	
			$response = new JsonResponse([
				'access_token' => $accessToken,
				'refresh_token' => $refreshToken,
			]);
			$response->send();

		} catch (HttpException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();

		} catch (AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();
			
		} catch (Exception $e) {
			throw $e;
		}

	}
}

?>