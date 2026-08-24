<?php

declare(strict_types=1);

namespace IraniLMS\Api\Auth;

defined( 'ABSPATH' ) || exit;

final class JwtToken {
    public function issue( int $user_id ): string {
        if ( $user_id <= 0 ) {
            throw new \InvalidArgumentException( 'Invalid user.' );
        }

        $header = $this->base64url( wp_json_encode( [ 'alg' => 'HS256', 'typ' => 'JWT' ] ) );
        $payload = $this->base64url( wp_json_encode( [
            'iss' => home_url( '/' ),
            'sub' => $user_id,
            'iat' => time(),
            'exp' => time() + ( 7 * DAY_IN_SECONDS ),
        ] ) );

        $signature = $this->base64url( hash_hmac( 'sha256', $header . '.' . $payload, $this->secret(), true ) );
        return $header . '.' . $payload . '.' . $signature;
    }

    public function verify( string $token ): int {
        $parts = explode( '.', $token );
        if ( 3 !== count( $parts ) ) {
            return 0;
        }

        [ $header, $payload, $signature ] = $parts;
        $expected = $this->base64url( hash_hmac( 'sha256', $header . '.' . $payload, $this->secret(), true ) );
        if ( ! hash_equals( $expected, $signature ) ) {
            return 0;
        }

        $data = json_decode( $this->base64url_decode( $payload ), true );
        if ( ! is_array( $data ) || empty( $data['sub'] ) || ( ! empty( $data['exp'] ) && time() >= (int) $data['exp'] ) ) {
            return 0;
        }

        return absint( $data['sub'] );
    }

    private function secret(): string {
        return wp_salt( 'auth' );
    }

    private function base64url( string $value ): string {
        return rtrim( strtr( base64_encode( $value ), '+/', '-_' ), '=' );
    }

    private function base64url_decode( string $value ): string {
        return base64_decode( strtr( $value, '-_', '+/' ) . str_repeat( '=', ( 4 - strlen( $value ) % 4 ) % 4 ) );
    }
}
