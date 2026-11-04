<?php

namespace QuietGuard\Monitor\WordPress;

use ErrorException;
use QuietGuard\Monitor\Reporter;
use Throwable;

/**
 * The core's reporter, sending one crash once.
 *
 * On WordPress a crash reaches the plugin by more than one door. A fatal is
 * handed over by WordPress's `wp_php_error_args` filter and then read again by
 * the core's shutdown function; a userland fatal is also seen first by the
 * core's error handler; an uncaught exception, handed back to PHP so WordPress
 * can show its error page, comes back as an "Uncaught ..." fatal at the
 * exception's own file and line. Each door is right on its own, so the rule
 * lives here: one report per severity, file and line, the core error handler's
 * own rule, with an exception counted as the E_ERROR it is about to become.
 */
final class OncePerSiteReporter extends Reporter
{
    /** @var array<string, true> */
    private array $sent = [];

    /**
     * @param  array<string, mixed>  $context
     */
    public function reportException(Throwable $e, array $context = []): bool
    {
        $site = ($e instanceof ErrorException ? $e->getSeverity() : E_ERROR).':'.$e->getFile().':'.$e->getLine();

        if (isset($this->sent[$site])) {
            return false;
        }

        $this->sent[$site] = true;

        return parent::reportException($e, $context);
    }
}
