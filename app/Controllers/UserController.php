<?php 
namespace App\Controllers;

use App\Errors\ValidationException;
use App\Http\JsonResponse;
use App\Services\UserService;
use Exception;

class UserController 
{
	public function __construct(private UserService $userService) {}

	public function index() 
	{
		try {
			header('Content-Type: text/html');
			readfile(__DIR__ . '/../../public/html/registration.html'); 
		} catch (Exception $e) {
			$response = new JsonResponse(['error' => 'Ошибка чтения файла: ' . $e->getMessage()], 500);
			$response->send();
		}
	}

	public function list() 
	{
		try {
			$filters = $_GET;
			$users = $this->userService->getUsers($filters);

			$response = new JsonResponse(['users' => $users]);
			$response->send();

		} catch(Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 500);
			$response->send();
		}
	}

	public function show($request) 
	{
		try {
			$id = $request['params']['id'];
			$user = $this->userService->getUserById($id);

			$response = new JsonResponse(['user' => $user]);
			$response->send();

		} catch(Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 404);
			$response->send();
		}
	}

	public function update($request) 
	{
		try {
			$id = $request['params']['id'];
			$data = $request['body'];
			$updatedUser = $this->userService->updateUser($id, $data);

			$response = new JsonResponse(['user' => $updatedUser]);
			$response->send();

		} catch (Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 500);
			$response->send();
		}
	}

	public function store($request) 
	{
		try {
			$newUser = $this->userService->createUser($request['body']);

			$response = new JsonResponse(['user' => $newUser], 201);
			$response->send();

		} catch (ValidationException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getErrorCode());
			$response->send();

		} catch (Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 500);
			$response->send();
		}
	}

	public function destroy($request)
	{
		try {
			$id = $request['params']['id'];
			$message = $this->userService->deleteUserById($id);

			$response = new JsonResponse(['message' => $message]);
			$response->send();
		} catch (Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 404);
			$response->send();
		}
	}
}
?>