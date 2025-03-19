<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\GraphQL;

if (!is_dir('../logs')) {
    mkdir('../logs', 0775, true);
}

setupErrorHandlers('../logs/errors.log');

setupCors($_ENV['FRONT_END_URL'] ?? '');

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('POST', '/graphql/', ['GraphQL', 'handle']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo("Not Found");
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo("now allowed");
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        echo GraphQL::handle();
        break;
}

/**
 * Sets up Cross-Origin Resource Sharing (CORS) headers for the application.
 *
 * @param string $frontEndUrl The URL of the frontend application. Default is 'http://localhost:5173'.
 * @return void
 */
function setupCors(string $frontEndUrl = 'http://localhost:5173'): void
{
    // CORS headers
    header('Access-Control-Allow-Origin: ' . $frontEndUrl);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

/**
 * This function sets up error and exception handlers for the application.
 * It logs errors and exceptions to the specified log file.
 *
 * @param string $logFile The path to the log file.
 * @return void
 */
function setupErrorHandlers(string $logFile): void
{
    set_error_handler(function ($errno, $errStr, $errFile, $errLine) use ($logFile) {
        $message = date('Y-m-d H:i:s') . " [$errno] $errStr in $errFile:$errLine\n";
        error_log($message, 3, $logFile);
        return true;
    });

    set_exception_handler(function (Throwable $e) use ($logFile) {
        $message = date('Y-m-d H:i:s') . " Uncaught Exception:\n";
        $message .= "Message: " . $e->getMessage() . "\n";
        $message .= "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        $message .= "Trace:\n" . $e->getTraceAsString() . "\n";
        error_log($message, 3, $logFile);
    });
}