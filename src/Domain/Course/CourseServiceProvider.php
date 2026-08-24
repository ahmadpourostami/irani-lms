<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

use IraniLMS\Support\ServiceProviderInterface;

final class CourseServiceProvider implements ServiceProviderInterface {
    private CoursePostType $post_type;

    public function register(): void {
        $this->post_type = new CoursePostType();
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
    }
}
