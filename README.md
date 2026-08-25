# Quiet Guard: WordPress plugin

Report PHP errors, exceptions and fatal shutdowns from a WordPress site to your
Quiet Guard server.
Built on the framework-agnostic core `laboiteacode/monitor-php`, the same
engine that powers the Laravel SDK and the Symfony bundle.

The WordPress.org distribution file is [readme.txt](readme.txt); this page is
the developer view.

## Requirements

- WordPress 6.0+
- PHP 8.2+ with `ext-curl` and `ext-sodium` (required by the core)

## Build and install

The plugin vendors the monitor core through Composer, so it must be built
before it is uploaded. Clone this repository INTO a folder named
`laravel-monitor`: WordPress takes the plugin's slug from its folder, and the
zip has to carry that name.

```bash
git clone https://github.com/Quiet-Guard/monitor-wordpress laravel-monitor
cd laravel-monitor
composer install --no-dev
cd .. && zip -r laravel-monitor.zip laravel-monitor
```

Upload the zip in wp-admin (Plugins, Add New, Upload Plugin) or drop the folder
under `wp-content/plugins/`, then activate it. An unbuilt folder (no `vendor/`)
activates safely: the plugin captures nothing and shows an admin notice.

## Configuration

Go to **Settings → Quiet Guard** and fill in:

- **Enabled**: master switch (1/0).
- **Server URL**: the base URL of your Quiet Guard server.
- **Project key**: the per-project API key generated in the dashboard (shown
  only once at creation).
- **Environments** and **Release**: optional metadata attached to reports.

Two advanced keys, `timeout` (default 3 seconds) and `trace_limit` (default 0 =
full stack trace, like every client in the family), are read from the same
`laravel_monitor_options` option and can be set programmatically via
`update_option()`; they survive settings-screen saves.

## What it captures

- Uncaught PHP exceptions, PHP errors at the configured `error_reporting`
  level, and fatal shutdowns.
- Capture is additive: WordPress' own error handling still runs, and reporting
  failures never break the site.
- Context values are scrubbed by key (passwords, tokens, cookies...) before
  anything leaves the site, and stack-trace frame arguments are never sent.

Application log forwarding and dependency/vulnerability scanning for WordPress
are on the roadmap and not part of this plugin yet.

## Documentation

Full documentation is served by your Quiet Guard server under `/docs`
(for example `https://monitor.example.com/docs`), including a dedicated
section for this plugin.

## License

MIT. See [LICENSE](LICENSE).
