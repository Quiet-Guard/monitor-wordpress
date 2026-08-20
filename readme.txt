=== Quiet Guard ===
Contributors: laboiteacode
Tags: monitoring, errors, exceptions, error-tracking
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.2
Stable tag: 0.1.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Report PHP errors, exceptions and fatal shutdowns from WordPress to a Quiet Guard server.

== Description ==

Quiet Guard centralises errors from your sites. This plugin captures
PHP exceptions, errors and fatal shutdowns and forwards them to your Quiet Guard
server over HTTPS, using the per-project API key.

Built on the framework-agnostic core `laboiteacode/monitor-php`, the same engine
that powers the Laravel and Symfony clients.

== Installation ==

1. Build the plugin with its dependencies: `composer install` inside the plugin
   directory (vendors the monitor core), then zip the folder.
2. Upload it under wp-content/plugins/ and activate it.
3. Go to Settings → Quiet Guard and fill in the server URL and project key.

An unbuilt folder (without its `vendor/` directory) activates safely: the plugin
captures nothing and shows an admin notice asking for the missing build step.

== Notes ==

* Capture is additive: WordPress' own error handling still runs.
* Application log forwarding is not part of this plugin yet; it captures errors,
  exceptions and fatal shutdowns only.
* The admin screen strings are currently English only. The plugin declares the
  `laravel-monitor` text domain; full translation support is planned.
* Encrypted storage of the captured content is enforced server-side per team.
* Dependency/vulnerability scanning for WordPress core, plugins and themes is on
  the roadmap (a WordPress-specific advisory source on the server, distinct from
  the Packagist source used for Composer-based apps).

== Changelog ==

= 0.1.0 =
* Initial release: global exception, error and fatal-shutdown capture forwarded
  to a Quiet Guard server, with a wp-admin settings screen (server URL,
  project key, release, enable switch) and key-based context scrubbing.
