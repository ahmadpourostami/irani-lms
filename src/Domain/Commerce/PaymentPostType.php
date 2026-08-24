<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce;

defined( 'ABSPATH' ) || exit;

final class PaymentPostType {
    public const POST_TYPE = 'irani_payment';

    public function register(): void {
        register_post_type(
            self::POST_TYPE,
            [
                'labels' => [
                    'name'          => __( 'پرداخت‌ها', 'irani-lms' ),
                    'singular_name' => __( 'پرداخت', 'irani-lms' ),
                ],
                'public'       => false,
                'show_ui'      => true,
                'show_in_rest' => false,
                'supports'     => [ 'title' ],
                'menu_icon'    => 'dashicons-money-alt',
            ]
        );
    }
}
