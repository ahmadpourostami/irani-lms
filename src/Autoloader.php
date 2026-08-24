<?php

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

spl_autoload_register(
    static function ( string $class ): void {
        $prefix = 'IraniLMS\\';
        if ( ! str_starts_with( $class, $prefix ) ) {
            return;
        }

        $relative = substr( $class, strlen( $prefix ) );
        $file = IRANI_LMS_PATH . 'src/' . str_replace( '\\', '/', $relative ) . '.php';
        if ( is_readable( $file ) ) {
            require_once $file;
        }
    }
);
