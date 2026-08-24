<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Enrollment;

defined( 'ABSPATH' ) || exit;

final class Enrollment {
    private const META_KEY = '_irani_lms_enrollments';

    public function enroll( int $user_id, int $course_id, string $source = 'manual' ): bool {
        if ( $user_id <= 0 || $course_id <= 0 || ! get_post( $course_id ) ) {
            return false;
        }

        $enrollments = $this->get_user_enrollments( $user_id );

        if ( isset( $enrollments[ $course_id ] ) ) {
            return true;
        }

        $enrollments[ $course_id ] = [
            'course_id' => $course_id,
            'status'    => 'active',
            'source'    => sanitize_key( $source ),
            'started_at'=> current_time( 'mysql', true ),
        ];

        update_user_meta( $user_id, self::META_KEY, $enrollments );

        do_action( 'irani_lms_enrollment_created', $user_id, $course_id, $enrollments[ $course_id ] );

        return true;
    }

    public function is_enrolled( int $user_id, int $course_id ): bool {
        $enrollments = $this->get_user_enrollments( $user_id );
        return isset( $enrollments[ $course_id ] ) && 'active' === $enrollments[ $course_id ]['status'];
    }

    public function get_user_enrollments( int $user_id ): array {
        $value = get_user_meta( $user_id, self::META_KEY, true );
        return is_array( $value ) ? $value : [];
    }
}
