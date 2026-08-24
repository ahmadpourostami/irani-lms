<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

use IraniLMS\Support\ServiceProviderInterface;

final class CourseServiceProvider implements ServiceProviderInterface {
    private CoursePostType $post_type;
    private CourseMeta $meta;
    private CourseMetaBox $meta_box;

    public function register(): void {
        $this->post_type = new CoursePostType();
        $this->meta = new CourseMeta();
        $this->meta_box = new CourseMetaBox( $this->meta );
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
        add_action( 'add_meta_boxes_' . CoursePostType::POST_TYPE, [ $this->meta_box, 'register' ] );
        add_action( 'save_post_' . CoursePostType::POST_TYPE, [ $this->meta_box, 'save' ] );
    }

    public function meta(): CourseMeta {
        return $this->meta;
    }
}
