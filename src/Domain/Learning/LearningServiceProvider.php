<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Learning;

use IraniLMS\Support\ServiceProviderInterface;

final class LearningServiceProvider implements ServiceProviderInterface {
    private LessonPostType $post_type;
    private LessonMeta $meta;
    private LessonMetaBox $meta_box;

    public function register(): void {
        $this->post_type = new LessonPostType();
        $this->meta = new LessonMeta();
        $this->meta_box = new LessonMetaBox( $this->meta );
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
        add_action( 'add_meta_boxes_' . LessonPostType::POST_TYPE, [ $this->meta_box, 'register' ] );
        add_action( 'save_post_' . LessonPostType::POST_TYPE, [ $this->meta_box, 'save' ] );
    }

    public function meta(): LessonMeta {
        return $this->meta;
    }
}
