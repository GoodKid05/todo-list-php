<?php

namespace App\Services;

use App\Errors\AppException;
use App\Models\RefreshTokenModel;
use App\Models\UserModel;
use DateInterval;
use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class AuthService 
{
	private string $jwtSecret;

	public function __construct(
		private LoggerInterface $logger,
		private UserModel $userModel, 
		private RefreshTokenModel $refreshTokenModel
	)
	{	
		$this->jwtSecret = $_ENV['JWT_SECRET'] ?? null;

		if(empty($this->jwtSecret)) {
			throw new InvalidArgumentException('JWT secret не задан в переменных окружения');
		}
	}

	private function saveRefreshToken(
		int $userId, 
		string $token, 
		DateTime $expires_at
	): bool
	{
		$result = $this->refreshTokenModel->insert([
			'user_id' => $userId,
			'token' => $token,
			'expires_at' => $expires_at->format('Y-m-d H:i:s'),
			'revoked' => 'false',
			'created_at' => (new DateTime()->format('Y-m-d H:i:s'))
		]);
		if (!$result) {
			error_log('Ошибка при сохранении refresh токена: ' . json_encode([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expires_at->format('Y-m-d H:i:s'),
        ]));
		}
		return $result;
	}

	public function generateAccessToken(array $user): string 
	{
		$payload = [
			'sub' => $user['id'],
			'email' => $user['email'],
			'iat' => time(),
			'exp' => time() + 900,
			'jti' => bin2hex(random_bytes(8))
		];
		return JWT::encode($payload, $this->jwtSecret, 'HS256');
	}

	public function generateRefreshToken(array $user): string 
	{
		$payload = [
			'sub' => $user['id'],
			'iat' => time(),
			'exp' => time() + 604800,
			'type' => 'refresh',
			'jti' => bin2hex(random_bytes(8))
		];
		return JWT::encode($payload, $this->jwtSecret, 'HS256');
	}

	public function generateNewTokens($user): array
	{
		$accessToken = $this->generateAccessToken($user);
		$refreshToken = $this->generateRefreshToken($user);

		if(empty($accessToken) || empty($refreshToken)) {
			throw new AppException('Не получилось сгенерировать новые токены', 500);
		}

		$expires_at = (new DateTime())->add(new DateInterval('P7D'));

		if (!$this->saveRefreshToken($user['id'], $refreshToken, $expires_at)) {
			throw new AppException('Ошибка при сохранении refresh токена', 500);
		}

		return [$accessToken, $refreshToken];
	}

	public function refreshToken($refreshToken): array
	{
		try {
			$tokenData = $this->refreshTokenModel->findByToken($refreshToken);
	
			if (empty($tokenData)) {
				throw new AppException('Токен не найден', 404);
			}
			if (filter_var($tokenData['revoked'], FILTER_VALIDATE_BOOLEAN)) {
				throw new AppException('Токен был отозван', 401);
			}
	
			if (date('Y-m-d H:i:s') > $tokenData['expires_at']) {
				throw new AppException('Токен истёк', 401);
			}
	
			$user = $this->userModel->getById($tokenData['user_id']);
	
			if(!$this->refreshTokenModel->revoke($refreshToken)) {
				throw new AppException('Не получилось отозвать токен', 500);
			};
	
			return $this->generateNewTokens($user);
		} catch (AppException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new Exception('Ошибка базы данных ' . $e->getMessage());
		}
	}

	public function findRefreshToken(string $token): ?array 
	{
		try {
			$tokenData = $this->refreshTokenModel->findByToken($token);
			if(!$tokenData) {
				throw new AppException('Не удалось найти refresh_token', 404);
			}
			return $tokenData;
		} catch (AppException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new Exception('Ошибка базы данных ' . $e->getMessage());
		}
	}

	public function validateAccessToken(string $token): int 
	{
		try {
			$decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
			if (!isset($decoded->sub)) {
				$this->logger->warning("Ошибка авторизации: недействительный токен");
				throw new AppException("Недействительный токен", 401);
			}
			return (int) $decoded->sub;
		} catch (AppException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new Exception('Не удалось провести валидацию токена' . $e->getMessage());
		}
	}

	public function login(array $credentials): array 
	{
		try {
			$login = $credentials['login'];
			$password = $credentials['password'];
			$user = $this->userModel->getByLogin($login);

			if(empty($user) || !password_verify($password, $user['password_hash'])) {
				throw new AppException('Неверный логин или пароль', 401);
			}

			return $this->generateNewTokens($user);

		} catch (AppException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new Exception('Ошибка базы данных: ' . $e->getMessage());
		}
	}

	public function logout(string $token): bool 
	{
		try {

			$tokenData = $this->refreshTokenModel->findByToken($token);
			if(empty($tokenData)) {
				throw new AppException('Токен не найден в базе данных', 404 );
			}

			$result = $this->refreshTokenModel->revoke($token);
			if(!$result) {
				throw new AppException('Не получилось отозвать токен', 500);
			}
			return $result;
		} catch (AppException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new Exception('Ошибка базы данных: ' . $e->getMessage());
		}
	}
}
?>
