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
            'permission_callback' => [ $this, 'permission_logged_in' ],
            'args' => [ 'course_id' => [ 'validate_callback' => static fn ( $value ): bool => absint( $value ) > 0 ] ],
        ] );
    }

    public function show( \WP_REST_Request $request ): \WP_REST_Response {
        $progress = new Progress();
        return new \WP_REST_Response( $progress->get( $this->current_user_id(), absint( $request['course_id'] ) ) );
    }
}
