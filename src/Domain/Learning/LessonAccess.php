<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Learning;

use IraniLMS\Domain\Enrollment\EnrollmentService;

defined( 'ABSPATH' ) || exit;

final class LessonAccess {
    public function __construct( private readonly EnrollmentService $enrollment ) {}

    public function can_access( int $user_id, int $course_id, int $lesson_id ): bool {
        $lesson = get_post( $lesson_id );
        if ( ! $lesson || LessonPostType::POST_TYPE !== $lesson->post_type || 'publish' !== $lesson->post_status ) {
            return false;
        }
        $meta = new LessonMeta();
        if ( absint( $meta->get( $lesson_id, LessonMeta::COURSE_ID, 0 ) ) !== $course_id ) {
            return false;
        }
        if ( '1' === (string) $meta->get( $lesson_id, LessonMeta::IS_PREVIEW, '0' ) ) {
            return true;
        }
        return $this->enrollment->can_access_course( $user_id, $course_id );
    }
}
