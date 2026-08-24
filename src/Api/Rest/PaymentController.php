<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Commerce\CommerceServiceProvider;
use IraniLMS\Plugin;

defined( 'ABSPATH' ) || exit;

final class PaymentController extends RestController {
    public function register(): void {
        $this->register_route( '/payments/(?P<id>\d+)/redirect', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'redirect' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );

        $this->register_route( '/payments/(?P<id>\d+)/verify', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'verify' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
    }

    public function redirect( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $payment_id = absint( $request['id'] );
        try {
            $commerce = $this->commerce();
            $payment = $commerce->payments()->get( $payment_id );
            if ( empty( $payment ) || absint( $payment['user_id'] ?? 0 ) !== $this->current_user_id() ) {
                return new \WP_Error( 'payment_not_found', __( 'پرداخت پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
            }

            return new \WP_REST_Response( [
                'payment_id' => $payment_id,
                'redirect_url' => $commerce->payment_service()->get_redirect_url( $payment_id ),
            ] );
        } catch ( \Throwable $e ) {
            return new \WP_Error( 'payment_redirect_failed', $e->getMessage(), [ 'status' => 400 ] );
        }
    }

    public function verify( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $payment_id = absint( $request['id'] );
        $commerce = $this->commerce();
        $payment = $commerce->payments()->get( $payment_id );

        if ( empty( $payment ) || absint( $payment['user_id'] ?? 0 ) !== $this->current_user_id() ) {
            return new \WP_Error( 'payment_not_found', __( 'پرداخت پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
        }

        $callback = $request->get_json_params();
        $callback = is_array( $callback ) ? $callback : [];
        $success = $commerce->payment_service()->verify( $payment_id, $callback );

        return new \WP_REST_Response( [
            'payment_id' => $payment_id,
            'verified' => $success,
            'status' => $success ? 'paid' : 'failed',
        ], $success ? 200 : 400 );
    }

    private function commerce(): CommerceServiceProvider {
        /** @var CommerceServiceProvider $commerce */
        $commerce = Plugin::container()->get( CommerceServiceProvider::class );
        return $commerce;
    }
}
