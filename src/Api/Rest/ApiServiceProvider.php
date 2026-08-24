<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Support\ServiceProviderInterface;

defined( 'ABSPATH' ) || exit;

final class ApiServiceProvider implements ServiceProviderInterface {
    private CourseController $courses;
    private AuthController $auth;
    private LoginController $login;
    private StudentController $student;
    private ProgressController $progress;
    private LearningController $learning;
    private CheckoutController $checkout;
    private PaymentController $payments;
    private GatewayController $gateways;
    private OrderController $orders;
    private EnrollmentController $enrollment;
    private LessonController $lessons;
    private AssessmentController $assessment;
    private HealthController $health;

    public function register(): void {
        $this->courses = new CourseController();
        $this->auth = new AuthController();
        $this->login = new LoginController();
        $this->student = new StudentController();
        $this->progress = new ProgressController();
        $this->learning = new LearningController();
        $this->checkout = new CheckoutController();
        $this->payments = new PaymentController();
        $this->gateways = new GatewayController();
        $this->orders = new OrderController();
        $this->enrollment = new EnrollmentController();
        $this->lessons = new LessonController();
        $this->assessment = new AssessmentController();
        $this->health = new HealthController();
    }

    public function boot(): void {
        add_action( 'rest_api_init', [ $this->courses, 'register' ] );
        add_action( 'rest_api_init', [ $this->auth, 'register' ] );
        add_action( 'rest_api_init', [ $this->login, 'register' ] );
        add_action( 'rest_api_init', [ $this->student, 'register' ] );
        add_action( 'rest_api_init', [ $this->progress, 'register' ] );
        add_action( 'rest_api_init', [ $this->learning, 'register' ] );
        add_action( 'rest_api_init', [ $this->checkout, 'register' ] );
        add_action( 'rest_api_init', [ $this->payments, 'register' ] );
        add_action( 'rest_api_init', [ $this->gateways, 'register' ] );
        add_action( 'rest_api_init', [ $this->orders, 'register' ] );
        add_action( 'rest_api_init', [ $this->enrollment, 'register' ] );
        add_action( 'rest_api_init', [ $this->lessons, 'register' ] );
        add_action( 'rest_api_init', [ $this->assessment, 'register' ] );
        add_action( 'rest_api_init', [ $this->health, 'register' ] );
    }
}
