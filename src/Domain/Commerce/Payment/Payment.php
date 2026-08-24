<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Payment;

defined( 'ABSPATH' ) || exit;

final class Payment {
    public const STATUS_PENDING  = 'pending';
    public const STATUS_REDIRECT = 'redirect';
    public const STATUS_PAID     = 'paid';
    public const STATUS_FAILED   = 'failed';

    public function __construct(
        private readonly int $id,
        private readonly int $order_id,
        private readonly int $amount,
        private readonly string $currency = 'IRT'
    ) {}

    public function get_id(): int { return $this->id; }
    public function get_order_id(): int { return $this->order_id; }
    public function get_amount(): int { return $this->amount; }
    public function get_currency(): string { return $this->currency; }
}
