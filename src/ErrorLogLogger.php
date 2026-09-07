<?php

namespace QuietGuard\Monitor\WordPress;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * The plugin's only voice.
 *
 * Reporter takes a PSR-3 logger as its fifth argument and it is the ONLY thing
 * in this client that ever emits a diagnostic. It was not passed, so a wrong
 * key, a wrong address or a refused payload produced nothing at all: no
 * exception, no admin notice, no log line. A WordPress administrator had no way
 * to learn that monitoring had never worked.
 *
 * error_log() rather than a WordPress helper on purpose: this runs from
 * ErrorHandler::register(), which is wired before WordPress has finished
 * loading, and it must not itself depend on anything that might not be there.
 * Where WP_DEBUG_LOG is on, error_log() lands in wp-content/debug.log, which is
 * where an administrator already looks.
 */
final class ErrorLogLogger extends AbstractLogger
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $suffix = $context === [] ? '' : ' '.json_encode($context, JSON_UNESCAPED_SLASHES);

        error_log(sprintf('[Quiet Guard] %s: %s%s', strtoupper((string) $level), $message, $suffix));
    }
}
