<?php

/**
 * Plugin Name:       LaravelMonitor
 * Description:       Report PHP errors, exceptions and logs from WordPress to a LaravelMonitor server.
 * Version:           0.1.0
 * Requires PHP:      8.2
 * Author:            la-boite-a-code
 * License:           MIT
 */

// Prevent direct access (WordPress entrypoint).
if (! defined('ABSPATH')) {
    exit;
}

$monitorAutoload = __DIR__.'/vendor/autoload.php';

if (is_file($monitorAutoload)) {
    require_once $monitorAutoload;
}

// Boot once WordPress and plugins are loaded.
add_action('plugins_loaded', ['LaBoiteACode\\Monitor\\WordPress\\Plugin', 'boot']);
