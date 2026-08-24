<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Enrollment;

use IraniLMS\Support\ServiceProviderInterface;

final class EnrollmentServiceProvider implements ServiceProviderInterface {
    private Enrollment $enrollment;

    public function register(): void {
        $this->enrollment = new Enrollment();
    }

    public function boot(): void {
        // Enrollment is currently consumed by domain services and checkout.
    }

    public function enrollment(): Enrollment {
        return $this->enrollment;
    }
}
