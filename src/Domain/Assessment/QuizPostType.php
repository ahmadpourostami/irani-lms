<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Assessment;

defined( 'ABSPATH' ) || exit;

final class QuizPostType {
    public const POST_TYPE = 'irani_quiz';

    public function register(): void {
        register_post_type( self::POST_TYPE, [
            'labels' => [
                'name' => __( 'آزمون‌ها', 'irani-lms' ),
                'singular_name' => __( 'آزمون', 'irani-lms' ),
                'add_new_item' => __( 'افزودن آزمون', 'irani-lms' ),
                'edit_item' => __( 'ویرایش آزمون', 'irani-lms' ),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_in_menu' => \IraniLMS\Admin\AdminMenu::SLUG,
            'menu_icon' => 'dashicons-clipboard',
            'supports' => [ 'title', 'editor', 'author' ],
        ] );
    }
}
