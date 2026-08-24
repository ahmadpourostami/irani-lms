<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Commerce\CommerceServiceProvider;
use IraniLMS\Plugin;

defined( 'ABSPATH' ) || exit;

final class CheckoutController extends RestController {
    public function register(): void {
        $this->register_route( '/checkout', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'create' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
    }

    public function create( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $course_id = absint( $request->get_param( 'course_id' ) );
        $gateway_id = sanitize_key( (string) $request->get_param( 'gateway' ) );

        if ( $course_id <= 0 || '' === $gateway_id ) {
            return new \WP_Error( 'invalid_checkout', __( 'اطلاعات خرید کامل نیست.', 'irani-lms' ), [ 'status' => 400 ] );
        }

        try {
            /** @var CommerceServiceProvider $commerce */
            $commerce = Plugin::container()->get( CommerceServiceProvider::class );
            $result = $commerce->checkout()->create( $this->current_user_id(), $course_id, $gateway_id );
            return new \WP_REST_Response( $result, 201 );
        } catch ( \Throwable $e ) {
            return new \WP_Error( 'checkout_failed', $e->getMessage(), [ 'status' => 400 ] );
        }
    }
}
