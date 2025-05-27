<?php 
namespace App\Models;

use PDO;

class UserModel 
{
	public function __construct(private PDO $pdo) {}

	public function getAll(array $filters = []): array
	{
		$query = "SELECT * FROM users WHERE 1=1";
		$params = [];

		foreach($filters as $key => $value) {
			if(in_array($key, ['created_at', 'updated_at'])) {
				$query .= " AND $key::date = :$key";
				$params[":$key"] = $value;
			} else {
				$query .= " AND $key ILIKE :$key";
				$params[":$key"] = '%' . $value . '%';
			}
		}
		$query .= ' ORDER BY id DESC';
		$stmt = $this->pdo->prepare($query);
		$stmt->execute($params);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function	getById(int $id): ?array 
	{
		$query = "SELECT * FROM users WHERE id = :id";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute([':id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result !== false ? $result : null;
	}

	public function	getByLogin(string $login): ?array 
	{
		$query = "SELECT * FROM users WHERE name = :login OR email = :login";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute([':login' => $login]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result !== false ? $result : null;
	}

	public function emailExists(string $email): bool 
	{
		$query = "SELECT 1 FROM users WHERE email = :email";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute([':email' => $email]);
		return $stmt->rowCount() > 0;
	}

	public function create(array $data): ?array 
	{
		$query = "INSERT INTO users(name, email, password_hash) VALUES(:name, :email, :password_hash)";
		$params = [];
		foreach($data as $key => $value) {
			$params[":$key"] = $value;
		}
		$stmt = $this->pdo->prepare($query);
		$stmt->execute($params);
		return $this->getById($this->pdo->lastInsertId());
	}

	public function update(int $id, array $data): ?array 
	{
		$allowFields = ['name', 'email', 'password_hash', 'role', 'updated_at', 'email_verified', 'is_active'];
		$fields = array_filter($data, fn($k) => in_array($k, $allowFields), ARRAY_FILTER_USE_KEY);

		if(empty($fields)) {
			return $this->getById($id);
		}

		$fields['updated_at'] = date("Y-m-d H:i:s.u");
		$sets = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($fields)));
		$query = "UPDATE users SET $sets WHERE id = :id";

		$params = [':id' => $id] + array_combine(
			array_map(fn($k) => ":$k", array_keys($fields)),
			array_values($fields)
		);

		$stmt = $this->pdo->prepare($query);
		$stmt->execute($params);

		return $this->getById($id);
	}

	public function delete(int $id): bool
	{
		$query = "DELETE FROM users WHERE id=:id";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute([':id' => $id]);
		return $stmt->rowCount() > 0;
	}
}
?>