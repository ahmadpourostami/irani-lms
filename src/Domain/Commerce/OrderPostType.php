<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce;

defined( 'ABSPATH' ) || exit;

final class OrderPostType {
    public const POST_TYPE = 'irani_order';

    public function register(): void {
        register_post_type(
            self::POST_TYPE,
            [
                'labels' => [
                    'name'          => __( 'سفارش‌ها', 'irani-lms' ),
                    'singular_name' => __( 'سفارش', 'irani-lms' ),
                ],
                'public'       => false,
                'show_ui'      => true,
                'show_in_rest' => false,
                'supports'     => [ 'title', 'author' ],
                'menu_icon'    => 'dashicons-cart',
            ]
        );
    }
}
