<?php

/**
 * Plugin Name:       LaravelMonitor
 * Plugin URI:        https://github.com/La-boite-a-code/LaravelMonitor
 * Description:       Report PHP errors, exceptions and fatal shutdowns from WordPress to a LaravelMonitor server.
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

// An unbuilt plugin folder (no vendor/) must degrade to an admin notice, never
// fatal the site: the boot callback would crash on the missing classes.
if (class_exists('LaBoiteACode\\Monitor\\WordPress\\Plugin')) {
    // Boot once WordPress and plugins are loaded.
    add_action('plugins_loaded', ['LaBoiteACode\\Monitor\\WordPress\\Plugin', 'boot']);
} else {
    add_action('admin_notices', static function (): void {
        echo '<div class="notice notice-error"><p>'
            .'LaravelMonitor is missing its dependencies and captures nothing. '
            .'Run <code>composer install</code> inside the plugin directory, then re-upload it (see readme.txt).'
            .'</p></div>';
    });
}
