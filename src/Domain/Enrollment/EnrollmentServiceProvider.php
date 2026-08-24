<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Enrollment;

use IraniLMS\Support\ServiceProviderInterface;

final class EnrollmentServiceProvider implements ServiceProviderInterface {
    private Enrollment $enrollment;
    private EnrollmentService $service;
    private EnrollmentPostType $post_type;

    public function register(): void {
        $this->enrollment = new Enrollment();
        $this->service = new EnrollmentService();
        $this->post_type = new EnrollmentPostType();
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
    }

    public function enrollment(): Enrollment { return $this->enrollment; }
    public function service(): EnrollmentService { return $this->service; }
}
