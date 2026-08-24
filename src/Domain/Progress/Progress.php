<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Progress;

defined( 'ABSPATH' ) || exit;

final class Progress {
    private const META_KEY = '_irani_lms_progress';

    public function get( int $user_id, int $course_id ): array {
        $all = get_user_meta( $user_id, self::META_KEY, true );
        if ( ! is_array( $all ) || ! isset( $all[ $course_id ] ) || ! is_array( $all[ $course_id ] ) ) {
            return [ 'course_id' => $course_id, 'completed_lessons' => [], 'last_lesson_id' => 0, 'percentage' => 0, 'completed' => false ];
        }
        return $all[ $course_id ];
    }

    public function mark_lesson_complete( int $user_id, int $course_id, int $lesson_id, int $total_lessons ): array {
        $all = get_user_meta( $user_id, self::META_KEY, true );
        $all = is_array( $all ) ? $all : [];
        $progress = $this->get( $user_id, $course_id );

        if ( ! in_array( $lesson_id, $progress['completed_lessons'], true ) ) {
            $progress['completed_lessons'][] = $lesson_id;
        }

        $progress['last_lesson_id'] = $lesson_id;
        $progress['percentage'] = $total_lessons > 0 ? min( 100, (int) round( count( $progress['completed_lessons'] ) / $total_lessons * 100 ) ) : 0;
        $progress['completed'] = 100 === $progress['percentage'];
        $progress['updated_at'] = current_time( 'mysql', true );
        $all[ $course_id ] = $progress;

        update_user_meta( $user_id, self::META_KEY, $all );
        do_action( 'irani_lms_lesson_completed', $user_id, $course_id, $lesson_id, $progress );

        return $progress;
    }
}
