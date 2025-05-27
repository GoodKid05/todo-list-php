<?php
namespace App\Middlewares;

use App\Http\JsonResponse;
use Psr\Log\LoggerInterface;

class ValidateUpdateTask {
	public function __construct(private LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function __invoke($request, $next)
	{
		$taskData = $request['body'];
		if (isset($taskData['title']) && empty($taskData['title'])) {
			$this->logger->warning('Валидация: пустой заголовок задачи', ['taskData' => $taskData]);
			return new JsonResponse(['error' =>'Невалидный заголовок задачи'], 422);
		}

		if (isset($taskData['description']) && empty($taskData['description'])) {
			$this->logger->warning('Валидация: пустое описание задачи', ['taskData' => $taskData]);
			return new JsonResponse(['error' =>'Невалидное описание задачи'], 422);
		}

		return $next($request);
	}
}

?>