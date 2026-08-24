<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

defined( 'ABSPATH' ) || exit;

final class AuthController extends RestController {
    public function register(): void {
        $this->register_route( '/me', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'me' ],
            'permission_callback' => [ $this, 'permission_logged_in' ],
        ] );
    }

    public function me(): \WP_REST_Response|\WP_Error {
        $user = wp_get_current_user();
        if ( ! $user->exists() ) {
            return new \WP_Error( 'not_authenticated', __( 'احراز هویت انجام نشده است.', 'irani-lms' ), [ 'status' => 401 ] );
        }

        return new \WP_REST_Response( [
            'id' => $user->ID,
            'username' => $user->user_login,
            'name' => $user->display_name,
            'email' => $user->user_email,
        ] );
    }
}
