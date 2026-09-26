<?php
require_once __DIR__ . '/config/config.php';
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/App.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/helpers/helpers.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/models/' . $class . '.php',
        __DIR__ . '/app/controllers/' . $class . '.php',
        __DIR__ . '/app/lib/' . $class . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

if (PHP_SAPI !== 'cli') {
    (new DueNotificationService())->sendDueNotifications();
}

(new App())->run();

// Entry point for the LibAI application. Routes are handled by App.
