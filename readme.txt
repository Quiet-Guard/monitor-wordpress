=== Quiet Guard ===
Contributors: laboiteacode
Tags: monitoring, errors, exceptions, error-tracking
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.2
Stable tag: 0.2.1
License: MIT
License URI: https://opensource.org/licenses/MIT

Report PHP errors, exceptions and fatal shutdowns from WordPress to a Quiet Guard server.

== Description ==

Quiet Guard centralises errors from your sites. This plugin captures
uncaught PHP exceptions, fatal shutdowns and userland fatal errors, and forwards
them to your Quiet Guard server over HTTPS, using the per-project API key.

Notices and warnings are not forwarded. Each one used to cost a blocking HTTP
request while the page was rendering, which a visitor reads as a slow site and
which fills a monthly event allowance in a day. This version has no setting to
turn that reach back on.

Built on the framework-agnostic core `quiet-guard/monitor-php`, the same engine
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

= 0.2.1 =
* Fixed: a wrong key, a wrong server address or a refused report now writes a
  `[Quiet Guard]` line to the PHP error log, which is `wp-content/debug.log`
  where `WP_DEBUG_LOG` is on. Until now every one of those failed in complete
  silence, with nothing to see anywhere.
* Changed: only uncaught exceptions, fatal shutdowns and userland fatal errors
  are reported. Notices and warnings are not, and there is no setting to restore
  them. Each one was a blocking HTTP request during page rendering, so one
  warning inside a loop over five hundred rows was five hundred requests in a
  single page load.
* Changed: the same error on the same line is reported once, and a request stops
  at twenty reports.
* Fixed: an exception message, class name or file path too long for the server
  now arrives trimmed instead of being refused and lost.
* Fixed: French social security numbers from Corsica are masked before sending.
  The check digit was computed in a way that could never succeed for them, so
  they travelled in clear.
* Fixed: a server address that already ends in `/api/v1` is accepted, and
  surrounding whitespace is ignored.
* Changed: requires PSR-3 version 2 or 3. The bundled logger is typed and cannot
  run against version 1.

= 0.2.0 =
* Changed: the Composer package and the PHP namespaces moved to Quiet Guard.
  Update the package name in your `composer.json` and any `QuietGuard\` imports;
  the wp-admin screen, the stored options and the plugin slug are unchanged.

= 0.1.0 =
* Initial release: global exception, error and fatal-shutdown capture forwarded
  to a Quiet Guard server, with a wp-admin settings screen (server URL,
  project key, release, enable switch) and key-based context scrubbing.
