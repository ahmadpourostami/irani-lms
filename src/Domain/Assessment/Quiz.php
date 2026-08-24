<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Assessment;

defined( 'ABSPATH' ) || exit;

final class Quiz {
    private const QUESTIONS_META = '_irani_lms_quiz_questions';

    public function get_questions( int $quiz_id ): array {
        $questions = get_post_meta( $quiz_id, self::QUESTIONS_META, true );
        return is_array( $questions ) ? $questions : [];
    }

    public function save_questions( int $quiz_id, array $questions ): void {
        $normalized = [];

        foreach ( $questions as $question ) {
            if ( $question instanceof Question ) {
                $normalized[] = $question->to_array();
            } elseif ( is_array( $question ) ) {
                $normalized[] = $question;
            }
        }

        update_post_meta( $quiz_id, self::QUESTIONS_META, $normalized );
    }

    public function total_points( int $quiz_id ): int {
        return array_sum( array_map( static fn ( array $question ): int => max( 0, absint( $question['points'] ?? 0 ) ), $this->get_questions( $quiz_id ) ) );
    }
}
