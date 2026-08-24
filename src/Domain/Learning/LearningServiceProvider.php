<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Learning;

use IraniLMS\Support\ServiceProviderInterface;

final class LearningServiceProvider implements ServiceProviderInterface {
    private LessonPostType $post_type;

    public function register(): void {
        $this->post_type = new LessonPostType();
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
    }
}
