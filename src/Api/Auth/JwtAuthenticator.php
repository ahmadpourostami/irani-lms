<?php

declare(strict_types=1);

namespace IraniLMS\Api\Auth;

defined( 'ABSPATH' ) || exit;

final class JwtAuthenticator {
    public function __construct(private readonly JwtToken $tokens) {}

    public function authenticate(): int {
        $header = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? trim( (string) wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) ) : '';
        if ( ! preg_match( '/^Bearer\s+(.+)$/i', $header, $matches ) ) {
            return 0;
        }

        return $this->tokens->verify( sanitize_text_field( $matches[1] ) );
    }

    public function current_user(): \WP_User|\WP_Error {
        $user_id = $this->authenticate();
        if ( $user_id <= 0 ) {
            return new \WP_Error( 'jwt_invalid', __( 'توکن احراز هویت نامعتبر یا منقضی شده است.', 'irani-lms' ), [ 'status' => 401 ] );
        }

        $user = get_user_by( 'id', $user_id );
        return $user ?: new \WP_Error( 'jwt_user_not_found', __( 'کاربر توکن پیدا نشد.', 'irani-lms' ), [ 'status' => 401 ] );
    }
}
