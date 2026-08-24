<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Course\CoursePostType;

defined( 'ABSPATH' ) || exit;

final class CourseController extends RestController {
    public function register(): void {
        $this->register_route( '/courses', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'index' ],
            'permission_callback' => '__return_true',
        ] );

        $this->register_route( '/courses/(?P<id>\d+)', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'show' ],
            'permission_callback' => '__return_true',
            'args' => [
                'id' => [ 'validate_callback' => static fn ( $value ): bool => absint( $value ) > 0 ],
            ],
        ] );
    }

    public function index( \WP_REST_Request $request ): \WP_REST_Response {
        $query = new \WP_Query( [
            'post_type' => CoursePostType::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => min( 50, max( 1, absint( $request->get_param( 'per_page' ) ?: 12 ) ) ),
            'paged' => max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ),
        ] );

        $items = array_map( static function ( \WP_Post $post ): array {
            return [
                'id' => $post->ID,
                'title' => get_the_title( $post ),
                'slug' => $post->post_name,
                'excerpt' => wp_strip_all_tags( get_the_excerpt( $post ) ),
                'url' => get_permalink( $post ),
            ];
        }, $query->posts );

        return new \WP_REST_Response( [
            'items' => $items,
            'page' => max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ),
            'per_page' => count( $items ),
            'total' => (int) $query->found_posts,
            'pages' => (int) $query->max_num_pages,
        ] );
    }

    public function show( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $post = get_post( absint( $request['id'] ) );
        if ( ! $post || CoursePostType::POST_TYPE !== $post->post_type || 'publish' !== $post->post_status ) {
            return new \WP_Error( 'course_not_found', __( 'دوره پیدا نشد.', 'irani-lms' ), [ 'status' => 404 ] );
        }

        return new \WP_REST_Response( [
            'id' => $post->ID,
            'title' => get_the_title( $post ),
            'slug' => $post->post_name,
            'content' => apply_filters( 'the_content', $post->post_content ),
            'excerpt' => wp_strip_all_tags( get_the_excerpt( $post ) ),
            'url' => get_permalink( $post ),
        ] );
    }
}
