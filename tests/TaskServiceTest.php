<?php

use App\Models\TaskModel;
use App\Services\TaskService;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase {
	private $taskModelMock;
	private TaskService $service;

	public function setUp(): void {
		$this->taskModelMock = $this->createMock(TaskModel::class);
		$this->service = new TaskService($this->taskModelMock);
	}
public function testGetTasksReturnsArray()
    {
        $expected = [['id' => 1, 'title' => 'Test']];
        $this->taskModelMock->method('getAll')->willReturn($expected);

        $result = $this->service->getTasks();

        $this->assertEquals($expected, $result);
    }

    public function testGetTaskByIdReturnsTask()
    {
        $expected = ['id' => 1, 'title' => 'Test'];
        $this->taskModelMock->method('getById')->with(1)->willReturn($expected);

        $result = $this->service->getTaskById(1);

        $this->assertEquals($expected, $result);
    }

    public function testCreateTaskSuccess()
    {
        $data = ['title' => 'New', 'description' => 'Test'];
        $expected = ['id' => 1] + $data;

        $this->taskModelMock->method('create')->with($data)->willReturn($expected);

        $result = $this->service->createTask($data);

        $this->assertEquals($expected, $result);
    }

    public function testCreateTaskFailureThrowsException()
    {
        $data = ['title' => 'Fail', 'description' => 'Test'];

        $this->taskModelMock->method('create')->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Не удалось создать новую задачу");

        $this->service->createTask($data);
    }

    public function testUpdateTaskReturnsUpdated()
    {
        $id = 1;
        $data = ['title' => 'Updated'];
        $expected = ['id' => 1, 'title' => 'Updated'];

        $this->taskModelMock->method('update')->with($id, $data)->willReturn($expected);

        $result = $this->service->updateTask($id, $data);

        $this->assertEquals($expected, $result);
    }

    public function testDeleteTaskSuccess()
    {
        $this->taskModelMock->method('delete')->with(1)->willReturn(true);

        $result = $this->service->deleteTask(1);

        $this->assertTrue($result);
    }

    public function testDeleteTaskFailureThrowsException()
    {
        $this->taskModelMock->method('delete')->with(1)->willReturn(false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Не получилось удалить задачу');

        $this->service->deleteTask(1);
    }

    public function testGetTableHeadersReturnsHeaders()
    {
        $headers = ['id', 'title', 'description'];
        $this->taskModelMock->method('getTableHeaders')->willReturn($headers);

        $result = $this->service->getTableHeaders();

        $this->assertEquals($headers, $result);
    }
}


?>