<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Assessment\Attempt;
use IraniLMS\Domain\Assessment\Quiz;
use IraniLMS\Domain\Assessment\QuizMeta;
use IraniLMS\Domain\Assessment\QuizPostType;
use IraniLMS\Domain\Enrollment\EnrollmentService;

defined( 'ABSPATH' ) || exit;

final class AssessmentController extends RestController {
    private EnrollmentService $enrollment;
    private Quiz $quiz;
    private Attempt $attempt;
    private QuizMeta $meta;

    public function __construct() {
        $this->enrollment = new EnrollmentService();
        $this->quiz = new Quiz();
        $this->attempt = new Attempt();
        $this->meta = new QuizMeta();
    }

    public function register(): void {
        $this->register_route( '/quizzes/(?P<quiz_id>\d+)', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'show' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
        $this->register_route( '/quizzes/(?P<quiz_id>\d+)/attempts', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'start' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
        $this->register_route( '/attempts/(?P<attempt_id>\d+)', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'get_attempt' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
        $this->register_route( '/attempts/(?P<attempt_id>\d+)/submit', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'submit' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
    }

    public function show( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $quiz_id = absint( $request['quiz_id'] );
        $quiz_post = get_post( $quiz_id );
        if ( ! $quiz_post || QuizPostType::POST_TYPE !== $quiz_post->post_type || 'publish' !== $quiz_post->post_status ) {
            return new \WP_Error( 'quiz_not_found', __( 'آزمون پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
        }

        if ( ! $this->can_access_quiz( $quiz_id ) ) {
            return new \WP_Error( 'quiz_access_denied', __( 'شما به این آزمون دسترسی ندارید.', 'irani-lms' ), [ 'status' => 403 ] );
        }

        $questions = $this->public_questions( $this->quiz->get_questions( $quiz_id ) );
        return new \WP_REST_Response( [
            'id' => $quiz_id,
            'title' => get_the_title( $quiz_id ),
            'questions' => $questions,
            'total_points' => $this->quiz->total_points( $quiz_id ),
            'passing_score' => $this->passing_score( $quiz_id ),
        ] );
    }

    public function start( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $quiz_id = absint( $request['quiz_id'] );
        if ( ! $this->valid_quiz( $quiz_id ) ) {
            return new \WP_Error( 'quiz_not_found', __( 'آزمون پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
        }
        if ( ! $this->can_access_quiz( $quiz_id ) ) {
            return new \WP_Error( 'quiz_access_denied', __( 'شما به این آزمون دسترسی ندارید.', 'irani-lms' ), [ 'status' => 403 ] );
        }

        try {
            $attempt_id = $this->attempt->start( $this->current_user_id(), $quiz_id );
            return new \WP_REST_Response( [ 'attempt_id' => $attempt_id, 'quiz_id' => $quiz_id, 'status' => Attempt::STATUS_IN_PROGRESS ], 201 );
        } catch ( \Throwable $e ) {
            return new \WP_Error( 'attempt_start_failed', $e->getMessage(), [ 'status' => 400 ] );
        }
    }

    public function get_attempt( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $attempt_id = absint( $request['attempt_id'] );
        $attempt = $this->attempt->get( $attempt_id );
        if ( ! $this->owns_attempt( $attempt ) ) {
            return new \WP_Error( 'attempt_not_found', __( 'تلاش آزمون پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
        }
        return new \WP_REST_Response( $attempt );
    }

    public function submit( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $attempt_id = absint( $request['attempt_id'] );
        $attempt = $this->attempt->get( $attempt_id );
        if ( ! $this->owns_attempt( $attempt ) ) {
            return new \WP_Error( 'attempt_not_found', __( 'تلاش آزمون پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
        }
        if ( Attempt::STATUS_IN_PROGRESS !== ( $attempt['status'] ?? '' ) ) {
            return new \WP_Error( 'attempt_already_submitted', __( 'این آزمون قبلاً ارسال شده است.', 'irani-lms' ), [ 'status' => 409 ] );
        }

        $payload = $request->get_json_params();
        $answers = is_array( $payload['answers'] ?? null ) ? $payload['answers'] : (array) $request->get_param( 'answers' );
        $quiz_id = absint( $attempt['quiz_id'] ?? 0 );
        $questions = $this->quiz->get_questions( $quiz_id );
        $score = $this->calculate_score( $questions, $answers );

        try {
            $result = $this->attempt->submit( $attempt_id, $this->sanitize_answers( $answers ), $score, $this->quiz->total_points( $quiz_id ), $this->passing_score( $quiz_id ) );
            return new \WP_REST_Response( $result );
        } catch ( \Throwable $e ) {
            return new \WP_Error( 'attempt_submit_failed', $e->getMessage(), [ 'status' => 400 ] );
        }
    }

    private function valid_quiz( int $quiz_id ): bool {
        $post = get_post( $quiz_id );
        return $post && QuizPostType::POST_TYPE === $post->post_type && 'publish' === $post->post_status;
    }

    private function can_access_quiz( int $quiz_id ): bool {
        $course_id = absint( $this->meta->get( $quiz_id, QuizMeta::COURSE_ID, 0 ) );
        return $course_id > 0 && $this->enrollment->can_access_course( $this->current_user_id(), $course_id );
    }

    private function owns_attempt( array $attempt ): bool {
        return ! empty( $attempt ) && absint( $attempt['user_id'] ?? 0 ) === $this->current_user_id();
    }

    private function passing_score( int $quiz_id ): int {
        return min( 100, max( 0, absint( $this->meta->get( $quiz_id, QuizMeta::PASSING_SCORE, 60 ) ) ) );
    }

    private function public_questions( array $questions ): array {
        return array_map( static function ( array $question ): array {
            return [
                'id' => sanitize_key( (string) ( $question['id'] ?? '' ) ),
                'type' => sanitize_key( (string) ( $question['type'] ?? '' ) ),
                'text' => wp_kses_post( (string) ( $question['text'] ?? '' ) ),
                'options' => is_array( $question['options'] ?? null ) ? array_values( $question['options'] ) : [],
                'points' => max( 0, absint( $question['points'] ?? 0 ) ),
            ];
        }, $questions );
    }

    private function calculate_score( array $questions, array $answers ): int {
        $score = 0;
        foreach ( $questions as $question ) {
            $id = (string) ( $question['id'] ?? '' );
            if ( '' === $id || ! array_key_exists( $id, $answers ) ) {
                continue;
            }
            $expected = $question['answer'] ?? null;
            $given = $answers[ $id ];
            if ( $this->answers_match( $expected, $given ) ) {
                $score += max( 0, absint( $question['points'] ?? 0 ) );
            }
        }
        return $score;
    }

    private function answers_match( mixed $expected, mixed $given ): bool {
        if ( is_array( $expected ) || is_array( $given ) ) {
            $expected = array_map( 'strval', (array) $expected );
            $given = array_map( 'strval', (array) $given );
            sort( $expected );
            sort( $given );
            return $expected === $given;
        }
        return trim( (string) $expected ) === trim( (string) $given );
    }

    private function sanitize_answers( array $answers ): array {
        $clean = [];
        foreach ( $answers as $key => $value ) {
            $clean[ sanitize_key( (string) $key ) ] = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( (string) $value );
        }
        return $clean;
    }
}
