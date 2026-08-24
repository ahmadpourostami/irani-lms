<?php

declare(strict_types=1);

namespace IraniLMS;

defined( 'ABSPATH' ) || exit;

final class Plugin {
    private static ?self $instance = null;

    public static function boot(): void {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
    }

    private function __construct() {
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
        add_action( 'init', [ $this, 'register_core_hooks' ] );
    }

    public function load_textdomain(): void {
        load_plugin_textdomain(
            'irani-lms',
            false,
            dirname( plugin_basename( IRANI_LMS_FILE ) ) . '/languages'
        );
    }

    public function register_core_hooks(): void {
        // Domain modules will register their own hooks through the application container.
    }
}
