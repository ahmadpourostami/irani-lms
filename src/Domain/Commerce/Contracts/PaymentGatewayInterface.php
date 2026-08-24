<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Contracts;

use IraniLMS\Domain\Commerce\Payment\Payment;

interface PaymentGatewayInterface {
    public function purchase( Payment $payment ): string;
    public function verify( Payment $payment, array $callback_data = [] ): bool;
    public function get_id(): string;
    public function get_name(): string;
}
