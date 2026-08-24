<?php

declare(strict_types=1);

namespace IraniLMS\Support;

interface ServiceProviderInterface {
    public function register(): void;
    public function boot(): void;
}
