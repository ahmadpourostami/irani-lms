<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Support\ServiceProviderInterface;

defined( 'ABSPATH' ) || exit;

final class ApiServiceProvider implements ServiceProviderInterface {
    private CourseController $courses;

    public function register(): void {
        $this->courses = new CourseController();
    }

    public function boot(): void {
        add_action( 'rest_api_init', [ $this->courses, 'register' ] );
    }
}
