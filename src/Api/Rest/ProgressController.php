<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Progress\Progress;

defined( 'ABSPATH' ) || exit;

final class ProgressController extends RestController {
    public function register(): void {
        $this->register_route( '/student/courses/(?P<course_id>\d+)/progress', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'show' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
            'args' => [ 'course_id' => [ 'validate_callback' => static fn ( $value ): bool => absint( $value ) > 0 ] ],
        ] );
    }

    public function show( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $course_id = absint( $request['course_id'] );
        $access = new AccessGuard( new \IraniLMS\Domain\Enrollment\EnrollmentService() );
        if ( ! $access->can_access_course( $this->current_user_id(), $course_id ) ) {
            return new \WP_Error( 'course_access_denied', __( 'شما به این دوره دسترسی ندارید.', 'irani-lms' ), [ 'status' => 403 ] );
        }
        $progress = new Progress();
        return new \WP_REST_Response( $progress->get( $this->current_user_id(), $course_id ) );
    }
}
