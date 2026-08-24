<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Learning;

defined( 'ABSPATH' ) || exit;

final class LessonPostType {
    public const POST_TYPE = 'irani_lesson';

    public function register(): void {
        register_post_type(
            self::POST_TYPE,
            [
                'labels' => [
                    'name'          => __( 'درس‌ها', 'irani-lms' ),
                    'singular_name' => __( 'درس', 'irani-lms' ),
                    'add_new_item'  => __( 'افزودن درس', 'irani-lms' ),
                    'edit_item'     => __( 'ویرایش درس', 'irani-lms' ),
                ],
                'public'       => false,
                'show_ui'      => true,
                'show_in_rest' => true,
                'menu_icon'    => 'dashicons-format-video',
                'supports'     => [ 'title', 'editor', 'thumbnail', 'author' ],
                'capability_type' => [ 'lesson', 'lessons' ],
                'map_meta_cap'    => true,
            ]
        );
    }
}
