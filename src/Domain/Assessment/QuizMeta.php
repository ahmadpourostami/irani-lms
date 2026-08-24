<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Assessment;

defined( 'ABSPATH' ) || exit;

final class QuizMeta {
    public const COURSE_ID = '_irani_lms_quiz_course_id';
    public const PASSING_SCORE = '_irani_lms_quiz_passing_score';
    public const TIME_LIMIT = '_irani_lms_quiz_time_limit';
    public const MAX_ATTEMPTS = '_irani_lms_quiz_max_attempts';
    public const RANDOMIZE = '_irani_lms_quiz_randomize';

    public function get( int $quiz_id, string $key, mixed $default = null ): mixed {
        $value = get_post_meta( $quiz_id, $key, true );
        return '' === $value || false === $value ? $default : $value;
    }

    public function update( int $quiz_id, array $data ): void {
        foreach ( $data as $key => $value ) {
            if ( in_array( $key, [ self::COURSE_ID, self::TIME_LIMIT, self::MAX_ATTEMPTS ], true ) ) {
                $value = max( 0, absint( $value ) );
            } elseif ( self::PASSING_SCORE === $key ) {
                $value = min( 100, max( 0, absint( $value ) ) );
            } elseif ( self::RANDOMIZE === $key ) {
                $value = ! empty( $value ) ? '1' : '0';
            }

            update_post_meta( $quiz_id, $key, $value );
        }
    }
}
