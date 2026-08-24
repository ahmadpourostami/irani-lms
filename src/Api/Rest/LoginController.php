<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Api\Auth\JwtToken;

defined( 'ABSPATH' ) || exit;

final class LoginController extends RestController {
    public function register(): void {
        $this->register_route( '/auth/login', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'login' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public function login( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $username = sanitize_user( (string) $request->get_param( 'username' ) );
        $password = (string) $request->get_param( 'password' );

        if ( '' === $username || '' === $password ) {
            return new \WP_Error( 'missing_credentials', __( 'نام کاربری و رمز عبور الزامی است.', 'irani-lms' ), [ 'status' => 400 ] );
        }

        $user = wp_authenticate( $username, $password );
        if ( is_wp_error( $user ) ) {
            return new \WP_Error( 'invalid_credentials', __( 'نام کاربری یا رمز عبور نادرست است.', 'irani-lms' ), [ 'status' => 401 ] );
        }

        return new \WP_REST_Response( [
            'token' => ( new JwtToken() )->issue( (int) $user->ID ),
            'user' => [
                'id' => (int) $user->ID,
                'username' => $user->user_login,
                'name' => $user->display_name,
            ],
        ] );
    }
}
