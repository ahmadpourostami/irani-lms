<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Course\CoursePricing;
use IraniLMS\Domain\Enrollment\EnrollmentService;

defined( 'ABSPATH' ) || exit;

final class EnrollmentController extends RestController {
    public function register(): void {
        $this->register_route( '/student/courses/(?P<course_id>\d+)/enroll', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'enroll' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
    }

    public function enroll( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $course_id = absint( $request['course_id'] );
        $course = get_post( $course_id );
        if ( ! $course || 'irani_course' !== $course->post_type || 'publish' !== $course->post_status ) {
            return new \WP_Error( 'course_not_found', __( 'دوره پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
        }

        $pricing = new CoursePricing();
        if ( $pricing->get_payable_amount( $course_id ) > 0 ) {
            return new \WP_Error( 'payment_required', __( 'این دوره رایگان نیست و نیاز به پرداخت دارد.', 'irani-lms' ), [ 'status' => 402 ] );
        }

        $enrollment = new EnrollmentService();
        $id = $enrollment->enroll( $this->current_user_id(), $course_id, 0 );
        return new \WP_REST_Response( [ 'enrollment_id' => $id, 'course_id' => $course_id, 'status' => 'active' ], 201 );
    }
}
