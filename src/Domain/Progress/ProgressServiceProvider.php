<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Progress;

use IraniLMS\Support\ServiceProviderInterface;

final class ProgressServiceProvider implements ServiceProviderInterface {
    private Progress $progress;

    public function register(): void {
        $this->progress = new Progress();
    }

    public function boot(): void {
        // Progress is consumed by learning, assessment and enrollment flows.
    }

    public function progress(): Progress {
        return $this->progress;
    }
}
