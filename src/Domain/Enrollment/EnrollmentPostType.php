<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Enrollment;

defined( 'ABSPATH' ) || exit;

final class EnrollmentPostType {
    public const POST_TYPE = 'irani_enrollment';

    public function register(): void {
        register_post_type( self::POST_TYPE, [
            'labels' => [
                'name' => __( 'ثبت‌نام‌ها', 'irani-lms' ),
                'singular_name' => __( 'ثبت‌نام', 'irani-lms' ),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => false,
            'show_in_menu' => \IraniLMS\Admin\AdminMenu::SLUG,
            'supports' => [ 'title', 'author' ],
            'menu_icon' => 'dashicons-welcome-learn-more',
        ] );
    }
}
