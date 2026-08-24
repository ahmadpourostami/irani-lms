<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Progress\Progress;

defined( 'ABSPATH' ) || exit;

final class LearningController extends RestController {
    public function register(): void {
        $this->register_route( '/student/courses/(?P<course_id>\d+)/lessons/(?P<lesson_id>\d+)/complete', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'complete' ],
            'permission_callback' => [ $this, 'permission_logged_in' ],
        ] );
    }

    public function complete( \WP_REST_Request $request ): \WP_REST_Response {
        $course_id = absint( $request['course_id'] );
        $lesson_id = absint( $request['lesson_id'] );
        $total_lessons = max( 0, absint( $request->get_param( 'total_lessons' ) ) );

        $progress = new Progress();
        return new \WP_REST_Response( $progress->mark_lesson_complete( $this->current_user_id(), $course_id, $lesson_id, $total_lessons ) );
    }
}
