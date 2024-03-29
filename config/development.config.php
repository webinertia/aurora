<?php

declare(strict_types=1);

return [
    // Additional modules to include when in development mode
    'modules'      => [
        'Webinertia\Installer',
        'Laminas\DeveloperTools',
        'BjyProfiler',
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404-dev',
        'exception_template'       => 'error/index',
        'template_map'             => [],
    ],
    // Configuration overrides during development mode
    'module_listener_options' => [
        'config_glob_paths'        => [realpath(__DIR__) . '/autoload/{,*.}{global,local}-development.php'],
        'config_cache_enabled'     => false,
        'module_map_cache_enabled' => false,
    ],
];
