<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

defined( 'ABSPATH' ) || exit;

final class CurriculumManager {
    public function __construct( private readonly Curriculum $curriculum ) {}

    public function add_lesson( int $course_id, int $lesson_id, int $section_index = 0 ): void {
        $sections = $this->curriculum->get_sections( $course_id );

        if ( ! isset( $sections[ $section_index ] ) ) {
            $sections[ $section_index ] = [
                'title'   => __( 'بخش اول', 'irani-lms' ),
                'lessons' => [],
            ];
        }

        if ( ! in_array( $lesson_id, $sections[ $section_index ]['lessons'], true ) ) {
            $sections[ $section_index ]['lessons'][] = $lesson_id;
        }

        $this->curriculum->save( $course_id, $sections );
    }

    public function remove_lesson( int $course_id, int $lesson_id ): void {
        $sections = $this->curriculum->get_sections( $course_id );

        foreach ( $sections as &$section ) {
            if ( ! isset( $section['lessons'] ) || ! is_array( $section['lessons'] ) ) {
                continue;
            }

            $section['lessons'] = array_values(
                array_filter( $section['lessons'], static fn ( int $id ): bool => $id !== $lesson_id )
            );
        }

        unset( $section );
        $this->curriculum->save( $course_id, $sections );
    }

    public function reorder( int $course_id, array $sections ): void {
        $this->curriculum->save( $course_id, $sections );
    }
}
