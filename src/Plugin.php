<?php

namespace QuietGuard\Monitor\WordPress;

// Direct access guard (loaded inside WordPress only).
if (! defined('ABSPATH') && php_sapi_name() !== 'cli') {
    exit;
}

use ErrorException;
use QuietGuard\Monitor\Config;
use QuietGuard\Monitor\ErrorHandler;
use QuietGuard\Monitor\Http\CurlHttpClient;
use QuietGuard\Monitor\Http\HttpClient;
use QuietGuard\Monitor\Payload\ExceptionPayloadBuilder;
use QuietGuard\Monitor\Reporter;
use QuietGuard\Monitor\Support\Scrubber;
use Throwable;

/**
 * WordPress adapter for the framework-agnostic monitor core. Registers global
 * PHP error/exception/shutdown handlers, a hook into WordPress's own fatal
 * error handler, and the wp-admin settings page.
 */
class Plugin
{
    public const OPTION = 'laravel_monitor_options';

    /** What WordPress may hand over as a fatal and the plugin reports: never a warning or a notice. */
    private const FATALS = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;

    public static function boot(): void
    {
        $options = is_array($stored = get_option(self::OPTION, [])) ? $stored : [];

        if (is_admin()) {
            (new Settings)->register();
        }

        // No address in the condition: Config fills it in with the hosted
        // service. The key is the one thing that cannot be guessed.
        if (! empty($options['enabled']) && ! empty($options['key'])) {
            self::register(self::makeReporter($options));
        }
    }

    /**
     * Wire a reporter into PHP and into WordPress's fatal error handler.
     */
    public static function register(Reporter $reporter): void
    {
        // Left to the core, an uncaught exception ends in an error_log line and
        // a blank HTTP 200: no fatal for WordPress to see, so no error page and
        // no recovery mode email. Rethrown, it becomes PHP's own fatal again.
        // Installed only on an empty slate: an existing handler is chained.
        if (set_exception_handler(static fn (Throwable $e) => throw $e) !== null) {
            restore_exception_handler();
        }

        ErrorHandler::register($reporter);

        // WordPress's handler runs before any plugin's shutdown function, and
        // anything it raises on the way, even a suppressed warning, replaces
        // the fatal in error_get_last(). Its filter carries the error it read
        // first. Returning $args untouched keeps its error page as it is.
        add_filter('wp_php_error_args', static function (array $args, array $error) use ($reporter): array {
            if (($error['type'] ?? 0) & self::FATALS) {
                $reporter->reportException(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
            }

            return $args;
        }, 10, 2);
    }

    /**
     * Build a reporter from stored options. Pure and transport-injectable so the
     * wiring is testable without WordPress.
     *
     * @param  array<string, mixed>  $options
     */
    public static function makeReporter(array $options, ?HttpClient $http = null): Reporter
    {
        // 0 = full stack trace, the default across the whole client family.
        $traceLimit = (int) ($options['trace_limit'] ?? 0);
        $release = $options['release'] ?? null;
        // Source snippets around application frames, on unless the option
        // says otherwise; the core reads the same switch.
        $codeSnippets = (bool) ($options['code_snippets'] ?? true);

        $config = new Config(
            $options['url'] ?? null,
            $options['key'] ?? null,
            (int) ($options['timeout'] ?? 3),
            $release,
            self::environments($options['environments'] ?? ''),
            $traceLimit,
            codeSnippets: $codeSnippets,
        );

        return new OncePerSiteReporter(
            $config,
            $http ?? new CurlHttpClient,
            new Scrubber(self::scrubKeys()),
            new ExceptionPayloadBuilder($traceLimit, $release, $codeSnippets),
            new ErrorLogLogger,
        );
    }

    /**
     * @return array<int, string>
     */
    private static function environments(string $csv): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $csv))));
    }

    /**
     * @return array<int, string>
     */
    private static function scrubKeys(): array
    {
        return ['password', 'pwd', 'passphrase', 'token', 'signature', 'secret', 'authorization', 'cookie', 'referer', 'referrer', 'auth', 'api_key', 'nonce', 'salt', 'logged_in', 'db_'];
    }
}
