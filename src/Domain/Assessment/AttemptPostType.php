<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Assessment;

defined( 'ABSPATH' ) || exit;

final class AttemptPostType {
    public const POST_TYPE = 'irani_attempt';

    public function register(): void {
        register_post_type( self::POST_TYPE, [
            'labels' => [
                'name' => __( 'تلاش‌های آزمون', 'irani-lms' ),
                'singular_name' => __( 'تلاش آزمون', 'irani-lms' ),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => false,
            'supports' => [ 'title', 'author' ],
            'menu_icon' => 'dashicons-chart-bar',
        ] );
    }
}
