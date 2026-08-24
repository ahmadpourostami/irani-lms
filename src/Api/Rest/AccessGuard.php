<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Enrollment\EnrollmentService;

defined( 'ABSPATH' ) || exit;

final class AccessGuard {
    public function __construct(private readonly EnrollmentService $enrollments) {}

    public function can_access_course(int $user_id, int $course_id): bool {
        if ($user_id <= 0 || $course_id <= 0) {
            return false;
        }
        return $this->enrollments->find($user_id, $course_id) > 0;
    }
}
