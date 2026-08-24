<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Enrollment\EnrollmentService;

defined( 'ABSPATH' ) || exit;

final class StudentController extends RestController {
    public function register(): void {
        $this->register_route( '/student/enrollments', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'enrollments' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
    }

    public function enrollments(): \WP_REST_Response {
        $query = new \WP_Query( [
            'post_type' => 'irani_enrollment',
            'post_status' => 'publish',
            'author' => $this->current_user_id(),
            'posts_per_page' => 50,
            'fields' => 'ids',
        ] );

        $items = [];
        foreach ( $query->posts as $id ) {
            $data = get_post_meta( $id, '_irani_lms_enrollment', true );
            if ( is_array( $data ) ) {
                $items[] = [ 'id' => (int) $id, 'course_id' => absint( $data['course_id'] ?? 0 ), 'order_id' => absint( $data['order_id'] ?? 0 ), 'status' => sanitize_key( (string) ( $data['status'] ?? '' ) ), 'enrolled_at' => (string) ( $data['enrolled_at'] ?? '' ) ];
            }
        }
        return new \WP_REST_Response( [ 'items' => $items ] );
    }
}
