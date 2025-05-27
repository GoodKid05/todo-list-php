<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Controllers\UserController;
use App\Core\Router;
use App\Core\Container;
use App\Database\Database;
use App\Errors\HttpException;
use App\Middlewares\ValidateUpdateTask;
use App\Models\RefreshTokenModel;
use App\Models\TaskModel;
use App\Models\UserModel;
use App\Services\AuthService;
use App\Services\TaskService;
use App\Services\UserService;
use Dotenv\Dotenv;
use Psr\Log\LoggerInterface;

$config = require __DIR__ . '/../config/config.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();

try {
	$uri = $_SERVER['REQUEST_URI'];
	$method = $_SERVER['REQUEST_METHOD'];
	
	$container->set(LoggerInterface::class, fn () => require __DIR__ . '/../config/logger.php');
	$container->set(ValidateUpdateTask::class, fn($c) => new ValidateUpdateTask($c->get(LoggerInterface::class)));


	$container->set(Database::class, fn() => new Database($config));
	$container->set(PDO::class, fn($c) => $c->get(Database::class)->getConnection());
	$container->set(UserModel::class, fn($c) => new UserModel($c->get(PDO::class)));
	$container->set(TaskModel::class, fn($c) => new TaskModel($c->get(PDO::class)));
	$container->set(RefreshTokenModel::class, fn($c) => new RefreshTokenModel($c->get(PDO::class)));

	$container->set(TaskService::class, fn($c) => new TaskService(
		$c->get(TaskModel::class),
		$c->get(LoggerInterface::class)
	));
	$container->set(UserService::class, fn($c) => new UserService($c->get(UserModel::class)));
	$container->set(AuthService::class, fn($c) => new AuthService(
		$c->get(LoggerInterface::class),
		$c->get(UserModel::class), 
		$c->get(RefreshTokenModel::class)
	));

	$container->set(UserController::class, fn($c) => new UserController($c->get(UserService::class)));
	$container->set(AuthController::class, fn($c) => new AuthController(
		$c->get(UserService::class), 
		$c->get(AuthService::class)
	));

	$container->set(TaskController::class, fn($c) => new TaskController(
		$c->get(TaskService::class),
		$c->get(UserService::class)
	));

	$router = new Router($container);

	require_once __DIR__ . '/../app/Routes/taskRouter.php';
	require_once __DIR__ . '/../app/Routes/userRouter.php';
	
	$response = $router->handleRequest($method, $uri);
	if($response !== null) {
		$response->send();
	}
} catch (HttpException $e) {
	http_response_code($e->getStatusCode());
	echo json_encode(['error' => $e->getMessage()]);
} catch(Exception $e){
	http_response_code(500);
	echo json_encode([
		"message" => "Server error",
		"error" => $e->getMessage()
	]);
	exit;
}

?>