<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

defined( 'ABSPATH' ) || exit;

final class Curriculum {
    public function get_sections( int $course_id ): array {
        $sections = get_post_meta( $course_id, '_irani_lms_curriculum', true );

        if ( ! is_array( $sections ) ) {
            return [];
        }

        return array_values( array_filter( $sections, static fn ( $section ): bool => is_array( $section ) ) );
    }

    public function save( int $course_id, array $sections ): void {
        $normalized = [];

        foreach ( $sections as $section ) {
            if ( ! is_array( $section ) ) {
                continue;
            }

            $title   = isset( $section['title'] ) ? sanitize_text_field( (string) $section['title'] ) : '';
            $lessons = isset( $section['lessons'] ) && is_array( $section['lessons'] ) ? $section['lessons'] : [];

            $normalized[] = [
                'title'   => $title,
                'lessons' => array_values( array_filter( array_map( 'absint', $lessons ) ) ),
            ];
        }

        update_post_meta( $course_id, '_irani_lms_curriculum', $normalized );
    }
}
