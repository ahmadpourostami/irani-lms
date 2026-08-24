<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Enrollment;

use IraniLMS\Support\ServiceProviderInterface;

final class EnrollmentServiceProvider implements ServiceProviderInterface {
    private Enrollment $enrollment;
    private EnrollmentService $service;

    public function register(): void {
        $this->enrollment = new Enrollment();
        $this->service = new EnrollmentService();
    }

    public function boot(): void {
        // Enrollment is triggered by successful commerce transactions.
    }

    public function enrollment(): Enrollment { return $this->enrollment; }
    public function service(): EnrollmentService { return $this->service; }
}
