<?php
namespace App\Services;

use App\Errors\AppException;
use App\Errors\HttpException;
use App\Errors\ValidationException;
use App\Models\UserModel;
use Exception;

class UserService 
{
	public function __construct(private UserModel $userModel) {}

	public function getUsers(array $filters = []): array 
	{
		try {
			$users =  $this->userModel->getAll($filters);
			if(empty($users)) {
				throw new Exception('Не удалось получить список пользователей');
			}
			return $users;
		} catch (Exception $e) {
			throw new Exception('Ошибка базы данных: ' . $e->getMessage());
		}
	}

	public function getUserById(int $id): ?array 
	{
		try {
			$user = $this->userModel->getById($id);
			if(empty($user)) {
				throw new Exception('Не найден пользователь с таким id');
			}
			return $user;
		} catch(Exception $e) {
			throw new Exception('Ошибка базы данных: ' . $e->getMessage());
		}
	}

	public function createUser(array $data): array 
	{
		try {
			if($this->userModel->emailExists($data['email'])) {
				throw new HttpException('Email уже существует', 409);
			}

			$data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
			unset($data['password']);

			return $this->userModel->create($data);
		} catch(HttpException $e) {
			throw $e;
		} catch(Exception $e) {
			throw new Exception('Ошибка базы данных: ' . $e->getMessage());
		}
	}

	public function updateUser(int $id, array $data): array 
	{
		try {
			if(array_key_exists('password', $data)) {
				$data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
				unset($data['password']);
			}

			$updatedUser = $this->userModel->update($id, $data);
			return $updatedUser;
		} catch (Exception $e) {
			throw new Exception('Ошибка базы данных: ' . $e->getMessage());
		}
	}

	public function deleteUserById(int $id): bool 
	{
		try {
			$result = $this->userModel->delete($id);
			if ($result) {
				$message = 'Пользователь успешно удален';
				return $message;
			} else {
				throw new Exception('Не удалось удалить пользователя');
			}
		} catch(Exception $e){
			throw new Exception('Ошибка базы данных: ' . $e->getMessage());
		}
	}
}
?>