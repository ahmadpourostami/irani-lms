<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Enrollment\EnrollmentService;
use IraniLMS\Domain\Learning\LessonMeta;
use IraniLMS\Domain\Learning\LessonPostType;

defined( 'ABSPATH' ) || exit;

final class LessonController extends RestController {
    private EnrollmentService $enrollment;
    private LessonMeta $meta;

    public function __construct() {
        $this->enrollment = new EnrollmentService();
        $this->meta = new LessonMeta();
    }

    public function register(): void {
        $this->register_route( '/student/courses/(?P<course_id>\d+)/lessons', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'index' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
        $this->register_route( '/student/courses/(?P<course_id>\d+)/lessons/(?P<id>\d+)', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'show' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
    }

    public function index( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $course_id = absint( $request['course_id'] );
        if ( ! $this->enrollment->can_access_course( $this->current_user_id(), $course_id ) ) {
            return new \WP_Error( 'course_access_denied', __( 'شما به این دوره دسترسی ندارید.', 'irani-lms' ), [ 'status' => 403 ] );
        }

        $lessons = get_posts( [
            'post_type' => LessonPostType::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'meta_key' => LessonMeta::SORT_ORDER,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => [ [ 'key' => LessonMeta::COURSE_ID, 'value' => $course_id, 'compare' => '=' ] ],
        ] );

        return new \WP_REST_Response( [ 'items' => array_map( fn ( \WP_Post $lesson ): array => $this->serialize( $lesson ), $lessons ) ] );
    }

    public function show( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $course_id = absint( $request['course_id'] );
        $lesson_id = absint( $request['id'] );
        if ( ! $this->enrollment->can_access_course( $this->current_user_id(), $course_id ) ) {
            return new \WP_Error( 'course_access_denied', __( 'شما به این دوره دسترسی ندارید.', 'irani-lms' ), [ 'status' => 403 ] );
        }
        $lesson = get_post( $lesson_id );
        if ( ! $lesson || LessonPostType::POST_TYPE !== $lesson->post_type || 'publish' !== $lesson->post_status || absint( $this->meta->get( $lesson_id, LessonMeta::COURSE_ID, 0 ) ) !== $course_id ) {
            return new \WP_Error( 'lesson_not_found', __( 'درس پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
        }
        return new \WP_REST_Response( $this->serialize( $lesson, true ) );
    }

    private function serialize( \WP_Post $lesson, bool $full = false ): array {
        $data = [
            'id' => $lesson->ID,
            'title' => get_the_title( $lesson ),
            'type' => (string) $this->meta->get( $lesson->ID, LessonMeta::TYPE, 'video' ),
            'duration' => absint( $this->meta->get( $lesson->ID, LessonMeta::DURATION, 0 ) ),
            'is_preview' => '1' === (string) $this->meta->get( $lesson->ID, LessonMeta::IS_PREVIEW, '0' ),
            'sort_order' => absint( $this->meta->get( $lesson->ID, LessonMeta::SORT_ORDER, 0 ) ),
        ];
        if ( $full ) {
            $data['content'] = apply_filters( 'the_content', $lesson->post_content );
            $data['video_url'] = (string) $this->meta->get( $lesson->ID, LessonMeta::VIDEO_URL, '' );
        }
        return $data;
    }
}
