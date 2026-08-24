<?php

declare(strict_types=1);

namespace IraniLMS\Commerce\Payment;

final class Payment {
    public function __construct(
        private readonly int $id,
        private readonly int $order_id,
        private readonly int $amount,
        private readonly string $currency = 'IRR',
        private string $status = 'pending',
    ) {}

    public function id(): int { return $this->id; }
    public function order_id(): int { return $this->order_id; }
    public function amount(): int { return $this->amount; }
    public function currency(): string { return $this->currency; }
    public function status(): string { return $this->status; }

    public function mark_paid(): void {
        $this->status = 'paid';
    }

    public function mark_failed(): void {
        $this->status = 'failed';
    }
}
