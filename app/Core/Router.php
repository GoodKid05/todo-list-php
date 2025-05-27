<?php
namespace App\Core;

use app\Core\Container;
use App\Errors\AppException;
use App\Errors\HttpException;
use Exception;

class Router {
	private $routes = [];
	private $prefix = '';

	private $groupMiddlewares = [];
	private $pendingGroupMiddlewares = [];

	public function __construct(private Container $container) {}

	public function middleware($middleware) 
	{
		if(is_array($middleware)) {
			$this->pendingGroupMiddlewares = array_merge($this->pendingGroupMiddlewares, $middleware);
		} else {
			$this->pendingGroupMiddlewares[] = $middleware;
		}
		return $this;
	}

	public function group($prefix, callable $callback) 
	{
		$previousPrefix = $this->prefix;
		$previousGroupMiddlewares = $this->groupMiddlewares;

		$this->prefix = $previousPrefix . $prefix;

		$this->groupMiddlewares = array_merge(
			$previousGroupMiddlewares, 
			$this->pendingGroupMiddlewares
		);

		$this->pendingGroupMiddlewares = [];
		$callback($this);

		$this->prefix = $previousPrefix;
		$this->groupMiddlewares = $previousGroupMiddlewares;
	}

	public function addRoute(
		$method, 
		$path, 
		$controller, 
		$action, 
		$middlewares = []
	): void
	{
		$fullPath = $this->prefix . $path;
		$allMiddlewares = array_merge($this->groupMiddlewares, $middlewares);
		$this->routes[] = [
			'method' => $method,
			'path' => $fullPath,
			'controller' => $controller,
			'action' => $action,
			'middlewares' => $allMiddlewares
		];
	}

	private function buildMiddlewareChain($middlewares, $finalHandler) 
	{
		return array_reduce(
			array_reverse($middlewares),
			fn($next, $middleware) => fn($request) => $middleware($request, $next),
			$finalHandler
		);
	}

	private function matchRoute($method, $uri): ?array 
	{
		foreach($this->routes as $route) {
			$pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $route['path']);
			$pattern = '#^' . $pattern . '$#';
			if($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
				$route['params'] = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
				return $route;
			}
		}
		return null;
	}

	public function handleRequest($method, $uri) 
	{
		try {
			$parts = explode('?', $uri, 2);
			$path = $parts[0];
			$queryString = $parts[1] ?? '';
			parse_str($queryString, $queryParams);
			$route = $this->matchRoute($method, $path);
	
			if(!$route) throw new HttpException("Route not found", 404);
	
			$controllerName = "App\\Controllers\\" . $route['controller'];
			$controller = $this->container->get($controllerName);
			$action = $route['action'];
	
			$request = [
				'params' => $route['params'],
				'query' => $queryParams,
				'body' => json_decode(file_get_contents('php://input'), true),
				'method' => $method,
				'uri' => $uri
			];
	
			$finalHandler = fn($req) => call_user_func([$controller, $action], $req);
			$middlewareChain = $this->buildMiddlewareChain($route['middlewares'], $finalHandler);
	
			return $middlewareChain($request);

		} catch(AppException $e) {
			throw $e;
		} catch (Exception $e) {
			throw $e;
		}		
	}
}
?>