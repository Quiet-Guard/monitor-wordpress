<?php

namespace LaBoiteACode\Monitor\WordPress;

use LaBoiteACode\Monitor\Config;
use LaBoiteACode\Monitor\ErrorHandler;
use LaBoiteACode\Monitor\Http\CurlHttpClient;
use LaBoiteACode\Monitor\Http\HttpClient;
use LaBoiteACode\Monitor\Payload\ExceptionPayloadBuilder;
use LaBoiteACode\Monitor\Reporter;
use LaBoiteACode\Monitor\Support\Scrubber;

/**
 * WordPress adapter for the framework-agnostic monitor core. Registers global
 * PHP error/exception/shutdown handlers and the wp-admin settings page.
 */
class Plugin
{
    public const OPTION = 'laravel_monitor_options';

    public static function boot(): void
    {
        $options = is_array($stored = get_option(self::OPTION, [])) ? $stored : [];

        if (is_admin()) {
            (new Settings)->register();
        }

        if (! empty($options['enabled']) && ! empty($options['url']) && ! empty($options['key'])) {
            ErrorHandler::register(self::makeReporter($options));
        }
    }

    /**
     * Build a reporter from stored options. Pure and transport-injectable so the
     * wiring is testable without WordPress.
     *
     * @param  array<string, mixed>  $options
     */
    public static function makeReporter(array $options, ?HttpClient $http = null): Reporter
    {
        $traceLimit = (int) ($options['trace_limit'] ?? 50);
        $release = $options['release'] ?? null;

        $config = new Config(
            $options['url'] ?? null,
            $options['key'] ?? null,
            (int) ($options['timeout'] ?? 3),
            $release,
            self::environments($options['environments'] ?? ''),
            $traceLimit,
        );

        return new Reporter(
            $config,
            $http ?? new CurlHttpClient,
            new Scrubber(self::scrubKeys()),
            new ExceptionPayloadBuilder($traceLimit, $release),
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
        return ['password', 'pwd', 'token', 'secret', 'authorization', 'cookie', 'auth', 'api_key', 'nonce'];
    }
}
