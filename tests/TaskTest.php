<?php


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase 
{
	private $client;
    private $baseUrl = 'http://localhost:8000/';
    private $accessToken;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
        ]);

        // Создаём тестового пользователя и получаем токен
        $email = 'taskuser' . time() . '@example.com';
        $password = 'securePassword123';

        $this->client->post('api/auth/register', [
            'json' => [
                'name' => 'TaskUser',
                'email' => $email,
                'password' => $password,
            ]
        ]);

        $response = $this->client->post('api/auth/login', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ]
        ]);

        $tokens = json_decode($response->getBody(), true);
        $this->accessToken = $tokens['access_token'];
    }

    public function testTaskCrud()
    {
        // 1. Создание задачи
        $response = $this->client->post('/api/tasks/', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'json' => [
                'title' => 'Test Task',
                'description' => 'Test Description',
            ]
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $task = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $task);
        $taskId = $task['id'];

        // 2. Получение списка задач
        $response = $this->client->get('/api/tasks/list', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $tasks = json_decode($response->getBody(), true);
        $this->assertIsArray($tasks);

        // 3. Обновление задачи
        $response = $this->client->patch("/api/tasks/update/$taskId", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'json' => [
                'description' => 'Updated Description',
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        // 4. Удаление задачи
        $response = $this->client->delete("/api/tasks/delete/$taskId", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
    }
}

?>