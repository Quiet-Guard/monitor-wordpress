=== LaravelMonitor ===
Contributors: laboiteacode
Requires PHP: 8.2
License: MIT

Report PHP errors, exceptions and logs from WordPress to a LaravelMonitor server.

== Description ==

LaravelMonitor centralises errors and logs from your sites. This plugin captures
PHP exceptions, errors and fatal shutdowns and forwards them to your LaravelMonitor
server over HTTPS, using the per-project API key.

Built on the framework-agnostic core `laboiteacode/monitor-php` — the same engine
that powers the Laravel and Symfony clients.

== Installation ==

1. Build the plugin with its dependencies: `composer install` inside the plugin
   directory (vendors the monitor core), then zip the folder.
2. Upload it under wp-content/plugins/ and activate it.
3. Go to Settings → LaravelMonitor and fill in the server URL and project key.

== Notes ==

* Capture is additive: WordPress' own error handling still runs.
* Encrypted storage of the captured content is enforced server-side per team.
* Dependency/vulnerability scanning for WordPress core, plugins and themes is on
  the roadmap (a WordPress-specific advisory source on the server, distinct from
  the Packagist source used for Composer-based apps).
