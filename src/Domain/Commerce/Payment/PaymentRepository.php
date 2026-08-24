<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Payment;

defined( 'ABSPATH' ) || exit;

final class PaymentRepository {
    private const META_KEY = '_irani_lms_payment';

    public function create( int $order_id, int $amount, string $currency = 'IRT' ): int {
        $payment_id = wp_insert_post(
            [
                'post_type'   => 'irani_payment',
                'post_status' => 'publish',
                'post_title'  => sprintf( 'Payment for order #%d', $order_id ),
            ],
            true
        );

        if ( is_wp_error( $payment_id ) ) {
            throw new \RuntimeException( $payment_id->get_error_message() );
        }

        update_post_meta( $payment_id, self::META_KEY, [
            'order_id'  => $order_id,
            'amount'    => max( 0, $amount ),
            'currency'  => strtoupper( $currency ),
            'status'    => Payment::STATUS_PENDING,
            'gateway'   => '',
            'authority' => '',
            'created_at'=> current_time( 'mysql', true ),
        ] );

        return (int) $payment_id;
    }

    public function get( int $payment_id ): array {
        $payment = get_post_meta( $payment_id, self::META_KEY, true );
        return is_array( $payment ) ? $payment : [];
    }

    public function update( int $payment_id, array $data ): void {
        $current = $this->get( $payment_id );
        update_post_meta( $payment_id, self::META_KEY, array_merge( $current, $data ) );
    }
}
