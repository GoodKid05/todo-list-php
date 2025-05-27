<?php
namespace App\Controllers;

use App\Errors\AppException;
use App\Http\JsonResponse;
use App\Services\TaskService;
use App\Services\UserService;
use Exception;

class TaskController 
{
	public function __construct(
		private TaskService $taskService,
		private UserService $userService
	) {}

	public function index() 
	{
		try {
			header('Content-Type: text/html');
			readfile(__DIR__ . '/../../public/html/index.html');
		} catch (Exception $e) {
			$response = new JsonResponse([
				'error' => 'Ошибка чтения файла: ' . $e->getMessage()]);
			$response->send();
		}
	}

	public function list($request) 
	{
		try {
			$filters = $request['query'];
			$filters['user_id'] = $request['auth_user_id'];
			$tasks = $this->taskService->getTasks($filters);

			$response = new JsonResponse(['tasks' => $tasks]);
			$response->send();
		} catch (AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();
		} catch (Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 500);
			$response->send();
		}
	}

	public function getTableHeaders() 
	{
		try {
			$tableHeaders = $this->taskService->getTableHeaders();
			$response = new JsonResponse(['tableHeaders' => $tableHeaders]);
			$response->send();
		} catch (AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();
		} catch (Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 500);
			$response->send();
		} 
	}

	public function store($request) 
	{
		try {
			$taskData = $request['body'];

			$userId = $request['auth_user_id'] ?? null;
			if (!$userId || !$this->userService->getUserById($userId)) {
				throw new AppException('Пользователь не найден', 404);
			}
			$taskData['user_id'] = $userId;
			$newTask =  $this->taskService->createTask($taskData);

			$response = new JsonResponse(['task' => $newTask], 201);
			$response->send();

		} catch (AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();
		} catch (Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 500);
			$response->send();
		} 
	}

	public function update($request) 
	{
		try {
			$taskId = $request['params']['id'];
			$taskData = $request['body'];
			$userId = $request['auth_user_id'] ?? null;
			
			$task = $this->taskService->getTaskById($taskId);

			if ($task['user_id'] !== $userId) {
				throw new AppException('Доступ запрещён', 403);
			}

			$updatedTask = $this->taskService->updateTask($taskId, $taskData);

			$response = new JsonResponse(['task' => $updatedTask]);
			$response->send();

		} catch (AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();
		} catch (Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 500);
			$response->send();
		}
	}

	public function destroy($request) 
	{
		try {
			$taskId = $request['params']['id'];
			$userId = $request['auth_user_id'] ?? null;
			
			$task = $this->taskService->getTaskById($taskId);

			if ($task['user_id'] !== $userId) {
				throw new AppException('Доступ запрещён', 403);
			}

			$result = $this->taskService->deleteTask($taskId);

			$response = new JsonResponse([
				'message' => 'Задача удалена', 
				'taskId' => $taskId
			]);
			$response->send();
			
		} catch(AppException $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
			$response->send();
			
		} catch(Exception $e) {
			$response = new JsonResponse(['error' => $e->getMessage()], 500);
			$response->send();
		}
	}
}

?>