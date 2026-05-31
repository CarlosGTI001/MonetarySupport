<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $relative = str_replace(['Controllers', 'Models', 'Core'], ['controllers', 'models', 'core'], $relative);
    $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    if (file_exists($path)) {
        require_once $path;
    }
});

use App\Core\Router;

Router::dispatch();
