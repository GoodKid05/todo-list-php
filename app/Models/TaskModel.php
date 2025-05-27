<?php 
namespace App\Models;

use Exception;
use InvalidArgumentException;
use PDO;

class TaskModel 
{
	protected array $protectedFields = ['id', 'created_at', 'updated_at'];
	protected array $attributes = [];

	public function __construct(private PDO $pdo) {}

	public function fill(array $data): void 
	{
	foreach ($data as $key => $value) {
		if (in_array($key, $this->protectedFields)) {
			continue;
		}
		$this->attributes[$key] = $value;
	}
	}

	public function getAll(array $filters = []): array
	{
		$query = "SELECT * FROM tasks WHERE 1=1";
		$params = [];
		foreach ($filters as $key => $value) {
			if(is_numeric($value)) {
				$query .= " AND $key = :$key";
				$params[":$key"] = $value;
			}
			elseif (in_array($key,['created_at', 'updated_at', 'deadline'])) {
				$query .= " AND $key::date = :$key";
				$params [":$key"] = $value;
			} else {
				$query .= " AND $key ILIKE :$key";
				$params [":$key"] = '%' . $value . '%';
			}
		}

		$query .= " ORDER BY id DESC";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getTableHeaders(): array 
	{
		$query = "SELECT column_name FROM information_schema.columns WHERE table_name = 'tasks' AND table_schema = 'public'";
		$stmt = $this->pdo->query($query);
		$result = $stmt->fetchAll(PDO::FETCH_COLUMN);
		return $result;
	}

	public function getById(int $id): ?array 
	{
		$query = "SELECT * FROM tasks WHERE id = :id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result !== false ? $result : null;
	}

	public function create(array $data): ?array 
	{	
		$this->fill($data);
		$fields = $this->attributes;

		$columns = implode(', ', array_keys($fields));
		$placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($fields)));

		$query = "INSERT INTO tasks ($columns) VALUES ($placeholders)";
		$stmt = $this->pdo->prepare($query);

		$params = [];
		foreach($fields as $key => $value) {
			$params[":$key"] = $value;
		}

		$stmt->execute($params);
		$id = $this->pdo->lastInsertId();
		return $this->getById($id);
	}

	public function update(int $id, array $data): array
	{	
		$this->fill($data);
		$fields = $this->attributes;

		if(empty($fields)) {
			throw new InvalidArgumentException('Нет данных для обновления');
		}

		$fields["updated_at"] = date('Y-m-d H:i:s.u');

		$sets = implode(', ', array_map(fn($k) => "$k=:$k", array_keys($fields)));
		$query = "UPDATE tasks SET $sets WHERE id=:id";

		$params = [':id' => $id] + array_combine(
			array_map(fn($k) => ":$k", array_keys($fields)),
			array_values($fields)
		);

		$stmt = $this->pdo->prepare($query);
		$stmt->execute($params);
		$rowCount = $stmt->rowCount();
		if ($rowCount === 0) {
			throw new Exception('Не удалось обновить задачу');
		}
		
		return $this->getById($id);
	}

	public function delete(int $id): bool 
	{
		$query = 'DELETE FROM tasks WHERE id = :id';
		$stmt = $this->pdo->prepare($query);
		$stmt->execute([':id' => $id]);
		return $stmt->rowCount() > 0;
	}
}

?>