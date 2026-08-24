<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Support\ServiceProviderInterface;

defined( 'ABSPATH' ) || exit;

final class ApiServiceProvider implements ServiceProviderInterface {
    private CourseController $courses;
    private AuthController $auth;
    private StudentController $student;

    public function register(): void {
        $this->courses = new CourseController();
        $this->auth = new AuthController();
        $this->student = new StudentController();
    }

    public function boot(): void {
        add_action( 'rest_api_init', [ $this->courses, 'register' ] );
        add_action( 'rest_api_init', [ $this->auth, 'register' ] );
        add_action( 'rest_api_init', [ $this->student, 'register' ] );
    }
}
