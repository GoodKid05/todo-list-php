<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
	private $pdo;
	public function __construct($config)
	{
		try {
			$dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};";
			$this->pdo = new PDO($dsn, $config['user'], $config['password'], [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			]);
		} catch (PDOException $e) {
			die("Ошибка подключение к базе: " . $e->getMessage());
		}
	}

	public function getConnection(): PDO 
	{
		return $this->pdo;
	}

}
?>