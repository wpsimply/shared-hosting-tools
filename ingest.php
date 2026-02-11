#!/usr/bin/env php
<?php
// Minimal Monolog ingester.

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

ini_set('error_log', getenv('HOME') . '/logs/ingest-php-error.log');
date_default_timezone_set('UTC');
require __DIR__ . '/vendor/autoload.php';

$logFile = getenv('LOG_FILE') ?: (getenv('HOME') . '/logs/syslog.log');
$user = getenv('USER') ?: getenv('LOGNAME') ?: get_current_user();
$tag = $argv[1] ?? 'stdout';

@mkdir(dirname($logFile), 0700, true);

$logger = new Logger('syslog');
$handler = new RotatingFileHandler($logFile, 14, Logger::INFO, true, null, true);
$handler->setFilenameFormat('{filename}-{date}', RotatingFileHandler::FILE_PER_DAY);
$handler->setFormatter(new LineFormatter("%datetime% %context.user% %context.tag%: %message%\n", 'c', true, true));
$logger->pushHandler($handler);

while (($line = fgets(STDIN)) !== false) {
    $logger->info(rtrim($line, "\r\n"), ['user' => $user, 'tag' => $tag]);
}
