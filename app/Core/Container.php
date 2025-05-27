<?php 
namespace app\Core;

use Exception;

class Container {
	private $bindings = [];
	private $instances = [];

	public function set(string $id, callable $factory) 
	{
		$this->bindings[$id] = $factory;
	}
	
	public function get(string $id) 
	{
		if (isset($this->instances[$id])) {
			return $this->instances[$id];
		}
		if (!isset($this->bindings[$id])) {
			throw new Exception("Сервис $id не найден в контейнере");
		}

		$this->instances[$id] = $this->bindings[$id]($this);
		return $this->instances[$id];
	}
}
?>