<?php

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase 
{
	private $client;
    private $baseUrl = 'http://localhost:8000/'; // Измени на актуальный адрес

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
        ]);
    }

    public function testAuthFlow()
    {	
		$name = 'testuser' . 'One';
        $email = 'testuser' . time() . '@example.com';
        $password = 'securePassword123';

        // 1. Регистрация
        $response = $this->client->post('api/auth/register', [
            'json' => [
				"name" => $name,
                "email" => $email,
                "password" => $password,
            ],
        ]);
		// print_r(json_decode($response->getBody(), true));
        $this->assertEquals(201, $response->getStatusCode());

        // 2. Логин
        $response = $this->client->post('api/auth/login', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('access_token', $data);
        $this->assertArrayHasKey('refresh_token', $data);
        $accessToken = $data['access_token'];
        $refreshToken = $data['refresh_token'];

        // 3. Доступ к защищённому маршруту
        $response = $this->client->get('/api/tasks/list', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);
        $this->assertEquals(200, $response->getStatusCode());
		
        // 4. Обновление токена
        $response = $this->client->post('api/auth/refresh', [
            'json' => [
                'refresh_token' => $refreshToken,
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $newTokens = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('access_token', $newTokens);
        $newAccessToken = $newTokens['access_token'];
		$newRefreshToken = $newTokens['refresh_token'];
	
        // 5. Logout
        $response = $this->client->post('api/auth/logout', [
            'json' => [
                'refresh_token' => $newRefreshToken,
            ],
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        // 6. Проверка отказа в доступе обновления токена после logout
        $response = $this->client->post('/api/auth/refresh', [
            'json' => [
                'refresh_token' => $newRefreshToken,
            ],
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }
}

?>