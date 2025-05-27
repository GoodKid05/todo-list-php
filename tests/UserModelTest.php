<?php
use PHPUnit\Framework\TestCase;
use App\Models\UserModel;

class UserModelTest extends TestCase{
	private PDO $pdo;

	private UserModel $model;

	protected function setUp():void {
		$this->pdo = new PDO("sqlite::memory:");
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$this->pdo->exec("
			CREATE TABLE users (
				id INTEGER PRIMARY KEY AUTOINCREMENT,
				name TEXT NOT NULL,
				email TEXT NOT NULL,
				password_hash TEXT NOT NULL
			)		
		");
		$this->model = new UserModel($this->pdo);
	}

	private function createTestUser(): array {
		return $this->model->create([
			'name' => "JohnDoe", 
			'email' => "johndoe@example.com", 
			'password_hash' => '1234']
		);
	}

	public function testCreate(): void {
		$user = $this->createTestUser();

		$this->assertIsArray($user);
		$this->assertArrayHasKey('id', $user);
		$this->assertIsNumeric($user['id']);
	}

	public function testFindUser(): void {
		$user = $this->createTestUser();

		$foundUser = $this->model->getById($user['id']);

		$this->assertNotNull($foundUser);
		$this->assertEquals("JohnDoe", $foundUser['name']);
		$this->assertEquals("johndoe@example.com", $foundUser['email']);
	}

	public function testDeleteUser(): void {
		$user = $this->createTestUser();

		$this->model->delete($user['id']);
		$this->assertNull($this->model->getById($user['id']));
	}

	public function testUserLifecycle(): void {
		$user = $this->createTestUser();

		$foundUser = $this->model->getById($user['id']);

		$this->assertNotNull($foundUser);
		$this->assertEquals("JohnDoe", $foundUser['name']);
		$this->assertEquals("johndoe@example.com", $foundUser['email']);

		$this->model->delete($user['id']);
		$this->assertNull($this->model->getById($user['id']));
	}
}

?>