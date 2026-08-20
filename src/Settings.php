<?php

namespace LaBoiteACode\Monitor\WordPress;

// Direct access guard (loaded inside WordPress only).
if (! defined('ABSPATH') && php_sapi_name() !== 'cli') {
    exit;
}

/**
 * The wp-admin settings screen (Settings → Quiet Guard). All methods run only
 * inside WordPress; they are never exercised outside it.
 */
class Settings
{
    public function register(): void
    {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_init', [$this, 'fields']);
    }

    public function menu(): void
    {
        add_options_page(
            'Quiet Guard',
            'Quiet Guard',
            'manage_options',
            'laravel-monitor',
            [$this, 'render'],
        );
    }

    public function fields(): void
    {
        register_setting('laravel_monitor', Plugin::OPTION, [$this, 'sanitize']);

        add_settings_section('laravel_monitor_main', 'Connection', '__return_null', 'laravel-monitor');

        foreach ([
            'enabled' => 'Enabled (1/0)',
            'url' => 'Server URL',
            'key' => 'Project key',
            'environments' => 'Environments (comma-separated, empty = all)',
            'release' => 'Release (optional)',
        ] as $key => $label) {
            add_settings_field($key, $label, fn () => $this->input($key), 'laravel-monitor', 'laravel_monitor_main');
        }
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function sanitize($input): array
    {
        $input = is_array($input) ? $input : [];

        // register_setting() routes EVERY update_option() through this method,
        // including programmatic writes: the advanced keys the settings screen
        // does not expose (timeout, trace_limit) must survive a save.
        $stored = get_option(Plugin::OPTION, []);
        $stored = is_array($stored) ? $stored : [];

        return [
            'enabled' => ! empty($input['enabled']) ? 1 : 0,
            'url' => esc_url_raw(trim((string) ($input['url'] ?? ''))),
            'key' => sanitize_text_field((string) ($input['key'] ?? '')),
            'environments' => sanitize_text_field((string) ($input['environments'] ?? '')),
            'release' => sanitize_text_field((string) ($input['release'] ?? '')),
            'timeout' => max(1, (int) ($input['timeout'] ?? $stored['timeout'] ?? 3)),
            'trace_limit' => max(0, (int) ($input['trace_limit'] ?? $stored['trace_limit'] ?? 0)),
        ];
    }

    public function input(string $key): void
    {
        $options = get_option(Plugin::OPTION, []);
        $value = esc_attr((string) ($options[$key] ?? ''));
        printf('<input type="text" name="%s[%s]" value="%s" class="regular-text" />', esc_attr(Plugin::OPTION), esc_attr($key), $value);
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        echo '<div class="wrap"><h1>Quiet Guard</h1><form method="post" action="options.php">';
        settings_fields('laravel_monitor');
        do_settings_sections('laravel-monitor');
        submit_button();
        echo '</form></div>';
    }
}
