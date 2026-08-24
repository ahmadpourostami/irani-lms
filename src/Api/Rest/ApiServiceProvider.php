<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Support\ServiceProviderInterface;

defined( 'ABSPATH' ) || exit;

final class ApiServiceProvider implements ServiceProviderInterface {
    private CourseController $courses;
    private AuthController $auth;
    private StudentController $student;
    private ProgressController $progress;
    private LearningController $learning;

    public function register(): void {
        $this->courses = new CourseController();
        $this->auth = new AuthController();
        $this->student = new StudentController();
        $this->progress = new ProgressController();
        $this->learning = new LearningController();
    }

    public function boot(): void {
        add_action( 'rest_api_init', [ $this->courses, 'register' ] );
        add_action( 'rest_api_init', [ $this->auth, 'register' ] );
        add_action( 'rest_api_init', [ $this->student, 'register' ] );
        add_action( 'rest_api_init', [ $this->progress, 'register' ] );
        add_action( 'rest_api_init', [ $this->learning, 'register' ] );
    }
}
