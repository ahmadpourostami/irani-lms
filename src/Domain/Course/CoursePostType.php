<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

defined( 'ABSPATH' ) || exit;

final class CoursePostType {
    public const POST_TYPE = 'irani_course';
    public const TAXONOMY  = 'irani_course_category';

    public function register(): void {
        register_post_type(
            self::POST_TYPE,
            [
                'labels' => [
                    'name'          => __( 'دوره‌ها', 'irani-lms' ),
                    'singular_name' => __( 'دوره', 'irani-lms' ),
                    'add_new_item'  => __( 'افزودن دوره', 'irani-lms' ),
                    'edit_item'     => __( 'ویرایش دوره', 'irani-lms' ),
                ],
                'public'             => true,
                'show_ui'            => true,
                'show_in_rest'       => true,
                'show_in_menu'       => \IraniLMS\Admin\AdminMenu::SLUG,
                'menu_icon'          => 'dashicons-welcome-learn-more',
                'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author' ],
                'has_archive'        => true,
                'rewrite'            => [ 'slug' => 'courses' ],
                'capability_type'    => [ 'course', 'courses' ],
                'map_meta_cap'       => true,
            ]
        );

        register_taxonomy(
            self::TAXONOMY,
            self::POST_TYPE,
            [
                'labels' => [
                    'name'          => __( 'دسته‌بندی دوره‌ها', 'irani-lms' ),
                    'singular_name' => __( 'دسته‌بندی دوره', 'irani-lms' ),
                ],
                'public'       => true,
                'show_ui'      => true,
                'show_in_rest' => true,
                'hierarchical' => true,
                'rewrite'      => [ 'slug' => 'course-category' ],
            ]
        );
    }
}
