<?php

/**
 * Plugin Name:       LaravelMonitor
 * Plugin URI:        https://github.com/La-boite-a-code/LaravelMonitor
 * Description:       Report PHP errors, exceptions and logs from WordPress to a LaravelMonitor server.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      8.2
 * Author:            La boite a code
 * Author URI:        https://github.com/La-boite-a-code
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       laravel-monitor
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
