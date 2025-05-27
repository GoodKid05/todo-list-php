<?php 
namespace App\Services;

use App\Errors\AppException;
use App\Models\TaskModel;
use Exception;
use PDOException;
use Psr\Log\LoggerInterface;

class TaskService 
{
	public function __construct
	(
		private TaskModel $taskModel,
		private LoggerInterface $logger
	) {}

	public function getTasks(array $filters = []): array 
	{
		try {
			$tasks = $this->taskModel->getAll($filters);
			if(empty($tasks)) {
				throw new AppException('Задачи не найдены', 404);
			}
			return $tasks;
		} catch (AppException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new Exception("Ошибка базы данных: " . $e->getMessage());
		}
	}

	public function getTableHeaders(): array 
	{
		$headers = $this->taskModel->getTableHeaders();
		return $headers;
	}

	public function getTaskById(int $id): array 
	{
		$task = $this->taskModel->getById($id);
		return $task;
	}

	public function createTask(array $data): array 
	{
		try{
	
			$newTask = $this->taskModel->create($data);
			if(!$newTask) {
				throw new AppException("Не удалось создать новую задачу", 500);
			}
			return $newTask;
		} catch(AppException $e){
			throw $e;
		} catch(Exception $e){
			throw new Exception("Ошибка при создании задачи: " . $e->getMessage());
		}
	}

	public function updateTask(int $id, array $data): array 
	{
		try {
			$updatedTask = $this->taskModel->update($id, $data);
			return $updatedTask;
		} catch(PDOException $e) {
			throw new Exception('Ошибка базы данных: ' . $e->getMessage());
		}
	}

	public function deleteTask(int $id): bool 
	{
		try {
			$task = $this->taskModel->getById($id);
			if (empty($task)) {
				$this->logger->warning('Задача не найдена в базе данных');
				throw new AppException('Задача не найдена', 404);
			}

			$confirm = $this->taskModel->delete($id);
			if (!$confirm) {
				throw new Exception('Не получилось удалить задачу');
			}
			return $confirm;
		} catch (AppException $e) {
			$this->logger->error('Ошибка базы данных при удалении задачи: ' . $e->getMessage(), ['taskId' => $id]);
			throw $e;
		} catch (Exception $e) {
			$this->logger->error('Ошибка базы данных при удалении задачи: ' . $e->getMessage(), ['taskId' => $id]);
			throw new Exception("Ошибка базы данных " . $e->getMessage());
		}
	}
}

?>