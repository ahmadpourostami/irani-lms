<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Commerce\CommerceServiceProvider;
use IraniLMS\Plugin;

defined( 'ABSPATH' ) || exit;

final class OrderController extends RestController {
    public function register(): void {
        $this->register_route( '/student/orders', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'index' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
    }

    public function index(): \WP_REST_Response {
        $commerce = Plugin::container()->get( CommerceServiceProvider::class );
        $orders = get_posts( [
            'post_type' => 'irani_order',
            'post_status' => 'publish',
            'author' => $this->current_user_id(),
            'posts_per_page' => 50,
            'orderby' => 'date',
            'order' => 'DESC',
        ] );

        $items = [];
        foreach ( $orders as $order_post ) {
            $order = $commerce->order()->get( $order_post->ID );
            $items[] = [
                'id' => $order_post->ID,
                'course_id' => absint( $order['course_id'] ?? 0 ),
                'amount' => absint( $order['amount'] ?? 0 ),
                'currency' => (string) ( $order['currency'] ?? 'IRT' ),
                'status' => sanitize_key( (string) ( $order['status'] ?? '' ) ),
                'created_at' => (string) ( $order['created_at'] ?? '' ),
                'updated_at' => (string) ( $order['updated_at'] ?? '' ),
            ];
        }

        return new \WP_REST_Response( [ 'items' => $items ] );
    }
}
