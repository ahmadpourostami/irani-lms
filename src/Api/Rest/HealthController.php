<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

defined( 'ABSPATH' ) || exit;

final class HealthController extends RestController {
    public function register(): void {
        $this->register_route( '/health', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'show' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public function show(): \WP_REST_Response {
        $checks = [
            'php' => [ 'ok' => version_compare( PHP_VERSION, '8.1.0', '>=' ), 'version' => PHP_VERSION ],
            'wordpress' => [ 'ok' => version_compare( get_bloginfo( 'version' ), '6.4', '>=' ), 'version' => get_bloginfo( 'version' ) ],
            'plugin' => [ 'ok' => defined( 'IRANI_LMS_VERSION' ), 'version' => defined( 'IRANI_LMS_VERSION' ) ? IRANI_LMS_VERSION : null ],
            'database' => [ 'ok' => ! empty( $GLOBALS['wpdb'] ) && empty( $GLOBALS['wpdb']->last_error ) ],
            'autoloader' => [ 'ok' => class_exists( \IraniLMS\Plugin::class ) ],
        ];

        $ok = ! in_array( false, array_column( $checks, 'ok' ), true );

        return new \WP_REST_Response( [
            'ok' => $ok,
            'status' => $ok ? 'ready' : 'degraded',
            'version' => defined( 'IRANI_LMS_VERSION' ) ? IRANI_LMS_VERSION : null,
            'checks' => $checks,
        ], $ok ? 200 : 503 );
    }
}
