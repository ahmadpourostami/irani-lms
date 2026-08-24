<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Api\Auth\JwtAuthenticator;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

abstract class RestController {
    protected const NAMESPACE = 'irani-lms/v1';

    protected function register_route( string $route, array $args ): void {
        register_rest_route( self::NAMESPACE, $route, $args );
    }

    protected function permission_logged_in(): bool {
        return is_user_logged_in();
    }

    protected function permission_authenticated( WP_REST_Request $request ): bool {
        if ( is_user_logged_in() ) {
            return true;
        }

        $user_id = ( new JwtAuthenticator( new \IraniLMS\Api\Auth\JwtToken() ) )->authenticate();
        if ( $user_id <= 0 ) {
            return false;
        }

        wp_set_current_user( $user_id );
        return true;
    }

    protected function current_user_id(): int {
        if ( get_current_user_id() > 0 ) {
            return get_current_user_id();
        }

        return ( new JwtAuthenticator( new \IraniLMS\Api\Auth\JwtToken() ) )->authenticate();
    }
}
