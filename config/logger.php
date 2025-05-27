<?php

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

$logger = new Logger('app');

$dateFormat = 'Y-m-d H:i:s';
$output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
$formatter  = new LineFormatter($output, $dateFormat, true, true);

$logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Level::Warning));

return $logger;

?>