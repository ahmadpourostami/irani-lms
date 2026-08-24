<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce;

defined( 'ABSPATH' ) || exit;

final class Order {
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID    = 'paid';
    public const STATUS_FAILED  = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    private const META_KEY = '_irani_lms_order';

    public function create( int $user_id, int $course_id, int $amount, string $currency = 'IRT' ): int {
        $order_id = wp_insert_post(
            [
                'post_type'   => 'irani_order',
                'post_status' => 'publish',
                'post_title'  => sprintf( 'Order #%s', wp_generate_uuid4() ),
                'post_author' => $user_id,
            ],
            true
        );

        if ( is_wp_error( $order_id ) ) {
            throw new \RuntimeException( $order_id->get_error_message() );
        }

        update_post_meta(
            $order_id,
            self::META_KEY,
            [
                'user_id'   => $user_id,
                'course_id' => $course_id,
                'amount'    => max( 0, $amount ),
                'currency'  => strtoupper( $currency ),
                'status'    => self::STATUS_PENDING,
                'created_at'=> current_time( 'mysql', true ),
            ]
        );

        return (int) $order_id;
    }

    public function get( int $order_id ): array {
        $order = get_post_meta( $order_id, self::META_KEY, true );
        return is_array( $order ) ? $order : [];
    }

    public function set_status( int $order_id, string $status ): void {
        $allowed = [ self::STATUS_PENDING, self::STATUS_PAID, self::STATUS_FAILED, self::STATUS_REFUNDED ];

        if ( ! in_array( $status, $allowed, true ) ) {
            throw new \InvalidArgumentException( 'Invalid order status.' );
        }

        $order = $this->get( $order_id );
        $order['status'] = $status;
        $order['updated_at'] = current_time( 'mysql', true );
        update_post_meta( $order_id, self::META_KEY, $order );
    }
}
