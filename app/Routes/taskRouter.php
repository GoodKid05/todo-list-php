<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\ValidateTaskData;
use App\Middlewares\ValidateUpdateTask;
use App\Services\AuthService;
use Psr\Log\LoggerInterface;

$router
	->middleware(new AuthMiddleware($container->get(AuthService::class)))
	->group('/api/tasks', function($router) use ($container){
	$router->addRoute('GET', '/', 'TaskController', 'index');
	$router->addRoute('GET', '/headers', 'TaskController', 'getTableHeaders');
	$router->addRoute('GET', '/list', 'TaskController', 'list');
	$router->addRoute('POST', '/', 'TaskController', 'store', [
		new ValidateTaskData($container->get(LoggerInterface::class))
	]);
	$router->addRoute('PATCH', '/{id}', 'TaskController', 'update', [
		new ValidateUpdateTask($container->get(LoggerInterface::class))
	]);
	$router->addRoute('DELETE', '/{id}', 'TaskController', 'destroy');
});
?>
