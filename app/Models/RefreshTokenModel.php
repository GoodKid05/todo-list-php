<?php
namespace App\Models;

use PDO;
use PDOException;

class RefreshTokenModel 
{
	public function __construct(private PDO $pdo) {}

	public function insert(array $data): bool 
	{
		$columns = implode(', ', array_keys($data));
		$placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
		$query = "INSERT INTO refresh_tokens ($columns) VALUES ($placeholders)";
		$params = [];
		foreach($data as $key => $value) {
			$params[":$key"] = $value;
		}
		try {
			$stmt = $this->pdo->prepare($query);
			return $stmt->execute($params);
		} catch(PDOException $e) {
			error_log('DB insert error: ' . $e->getMessage());
			return false;
		}
	}

	public function findByToken(string $token): ?array 
	{
		$query = "SELECT * FROM refresh_tokens WHERE token = :token LIMIT 1";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute([':token' => $token]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ?: null;
	}

	public function revoke(string $token): bool
	{
		try {
			$query = "UPDATE refresh_tokens SET revoked = true WHERE token = :token";
			$stmt = $this->pdo->prepare($query);
			$stmt->execute([':token' => $token]);
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			return false;
		}

	}

}
?>