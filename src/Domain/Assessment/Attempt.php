<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Assessment;

defined( 'ABSPATH' ) || exit;

final class Attempt {
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SUBMITTED   = 'submitted';

    private const META_KEY = '_irani_lms_attempt';

    public function start( int $user_id, int $quiz_id ): int {
        $attempt_id = wp_insert_post( [
            'post_type'   => 'irani_attempt',
            'post_status' => 'publish',
            'post_title'  => sprintf( 'Attempt: %d / %d', $user_id, $quiz_id ),
            'post_author' => $user_id,
        ], true );

        if ( is_wp_error( $attempt_id ) ) {
            throw new \RuntimeException( $attempt_id->get_error_message() );
        }

        update_post_meta( $attempt_id, self::META_KEY, [
            'user_id'     => $user_id,
            'quiz_id'     => $quiz_id,
            'status'      => self::STATUS_IN_PROGRESS,
            'answers'     => [],
            'score'       => 0,
            'percentage'  => 0,
            'passed'      => false,
            'started_at'  => current_time( 'mysql', true ),
            'submitted_at'=> null,
        ] );

        return (int) $attempt_id;
    }

    public function get( int $attempt_id ): array {
        $attempt = get_post_meta( $attempt_id, self::META_KEY, true );
        return is_array( $attempt ) ? $attempt : [];
    }

    public function submit( int $attempt_id, array $answers, int $score, int $total_points, int $passing_score ): array {
        $attempt = $this->get( $attempt_id );
        if ( empty( $attempt ) ) {
            throw new \RuntimeException( 'Attempt not found.' );
        }

        $percentage = $total_points > 0 ? (int) round( ( $score / $total_points ) * 100 ) : 0;
        $attempt['answers'] = $answers;
        $attempt['score'] = max( 0, $score );
        $attempt['percentage'] = min( 100, $percentage );
        $attempt['passed'] = $percentage >= $passing_score;
        $attempt['status'] = self::STATUS_SUBMITTED;
        $attempt['submitted_at'] = current_time( 'mysql', true );

        update_post_meta( $attempt_id, self::META_KEY, $attempt );
        do_action( 'irani_lms_attempt_submitted', $attempt_id, $attempt );

        return $attempt;
    }
}
