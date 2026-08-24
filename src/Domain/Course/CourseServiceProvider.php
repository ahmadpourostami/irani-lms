<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

use IraniLMS\Support\ServiceProviderInterface;

final class CourseServiceProvider implements ServiceProviderInterface {
    private CoursePostType $post_type;
    private CourseMeta $meta;
    private CourseMetaBox $meta_box;
    private Curriculum $curriculum;
    private CurriculumManager $curriculum_manager;
    private CurriculumMetaBox $curriculum_box;
    private CoursePricingMetaBox $pricing_box;

    public function register(): void {
        $this->post_type = new CoursePostType();
        $this->meta = new CourseMeta();
        $this->meta_box = new CourseMetaBox( $this->meta );
        $this->curriculum = new Curriculum();
        $this->curriculum_manager = new CurriculumManager( $this->curriculum );
        $this->curriculum_box = new CurriculumMetaBox( $this->curriculum );
        $this->pricing_box = new CoursePricingMetaBox();
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
        add_action( 'add_meta_boxes_' . CoursePostType::POST_TYPE, [ $this->meta_box, 'register' ] );
        add_action( 'save_post_' . CoursePostType::POST_TYPE, [ $this->meta_box, 'save' ] );
        add_action( 'add_meta_boxes_' . CoursePostType::POST_TYPE, [ $this->curriculum_box, 'register' ] );
        add_action( 'save_post_' . CoursePostType::POST_TYPE, [ $this->curriculum_box, 'save' ] );
        add_action( 'add_meta_boxes_' . CoursePostType::POST_TYPE, [ $this->pricing_box, 'register' ] );
        add_action( 'save_post_' . CoursePostType::POST_TYPE, [ $this->pricing_box, 'save' ] );
    }

    public function meta(): CourseMeta {
        return $this->meta;
    }

    public function curriculum(): Curriculum {
        return $this->curriculum;
    }

    public function curriculum_manager(): CurriculumManager {
        return $this->curriculum_manager;
    }
}
