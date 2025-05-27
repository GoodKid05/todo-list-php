<?php

use App\Middlewares\ValidateLoginUser;
use App\Middlewares\ValidateRefreshToken;
use App\Middlewares\ValidateRegisterUser;
use Psr\Log\LoggerInterface;

$router->addRoute('GET', '/register', 'UserController', 'index');

$router->group('/api/users', function($router) 
{
	$router->addRoute('GET', '/', 'UserController', 'list');
	$router->addRoute('GET', '/{id}', 'UserController', 'show');
	$router->addRoute('PATCH', '/{id}', 'UserController', 'update');
	$router->addRoute('DELETE', '/{id}', 'UserController', 'destroy');
});

$router->group('/api/auth', function($router) use ($container) 
{
	$router->addRoute('POST', '/register', 'AuthController', 'register', 
		[new ValidateRegisterUser($container->get(LoggerInterface::class))]
	);
	$router->addRoute('POST', '/login', 'AuthController', 'login', 
		[new ValidateLoginUser($container->get(LoggerInterface::class))]
	);
	$router->addRoute('POST', '/logout', 'AuthController', 'logout', [
		new ValidateRefreshToken($container->get(LoggerInterface::class))
	]);
	$router->addRoute('POST', '/refresh', 'AuthController', 'refreshToken', [
		new ValidateRefreshToken($container->get(LoggerInterface::class))
	]);
})
?>