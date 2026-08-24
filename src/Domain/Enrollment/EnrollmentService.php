<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Enrollment;

defined( 'ABSPATH' ) || exit;

final class EnrollmentService {
    private const META_KEY = '_irani_lms_enrollment';

    public function enroll( int $user_id, int $course_id, int $order_id = 0 ): int {
        if ( $user_id <= 0 || $course_id <= 0 ) {
            throw new \InvalidArgumentException( 'Invalid enrollment data.' );
        }
        $existing = $this->find( $user_id, $course_id );
        if ( $existing > 0 ) {
            return $existing;
        }
        $id = wp_insert_post( [ 'post_type' => 'irani_enrollment', 'post_status' => 'publish', 'post_title' => sprintf( 'Enrollment: %d / %d', $user_id, $course_id ), 'post_author' => $user_id ], true );
        if ( is_wp_error( $id ) ) {
            throw new \RuntimeException( $id->get_error_message() );
        }
        update_post_meta( $id, self::META_KEY, [ 'user_id' => $user_id, 'course_id' => $course_id, 'order_id' => $order_id, 'status' => 'active', 'enrolled_at' => current_time( 'mysql', true ) ] );
        do_action( 'irani_lms_student_enrolled', (int) $id, $user_id, $course_id, $order_id );
        return (int) $id;
    }

    public function find( int $user_id, int $course_id ): int {
        $posts = get_posts( [ 'post_type' => 'irani_enrollment', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_query' => [ [ 'key' => self::META_KEY, 'value' => '"user_id";i:' . $user_id, 'compare' => 'LIKE' ], [ 'key' => self::META_KEY, 'value' => '"course_id";i:' . $course_id, 'compare' => 'LIKE' ] ] ] );
        return empty( $posts ) ? 0 : (int) $posts[0];
    }

    public function can_access_course( int $user_id, int $course_id ): bool {
        $enrollment_id = $this->find( $user_id, $course_id );
        if ( $enrollment_id <= 0 ) {
            return false;
        }
        $data = get_post_meta( $enrollment_id, self::META_KEY, true );
        return is_array( $data ) && 'active' === ( $data['status'] ?? '' );
    }
}
