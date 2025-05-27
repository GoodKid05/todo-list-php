<?php

use App\Models\TaskModel;
use PHPUnit\Framework\TestCase;

class TaskModelTest extends TestCase {
	private PDO $pdo;
	private TaskModel $model;

	public function setUp(): void {
		$this->pdo = new PDO('pgsql:host=localhost;port=5432;dbname=todo_db_test','postgres', '9396158829');
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$this->model = new TaskModel($this->pdo);
	}

	public function createTestTask(): array{
		return $this->model->create([
			"title" => "What is Lorem Ipsum",
			"description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
		]);
	}

	public function testCreateTask(): void {
		$task = $this->createTestTask();

		$this->assertIsArray($task);
		$this->assertArrayHasKey('id', $task);
		$this->assertIsNumeric($task['id']);
	}

	public function testFindTask(): void {
		$task = $this->createTestTask();

		$found = $this->model->getById($task['id']);
		$this->assertNotNull($found);
		$this->assertEquals($task['title'], $found['title']);
		$this->assertEquals($task['description'], $found['description']);
	}

	public function testDeleteTask(): void {
		$task = $this->createTestTask();

		$this->model->delete($task['id']);
		$this->assertNull($this->model->getById($task['id']));
	
	}

}

?>