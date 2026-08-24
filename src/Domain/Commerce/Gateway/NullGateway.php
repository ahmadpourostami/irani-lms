<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Gateway;

use IraniLMS\Domain\Commerce\Contracts\PaymentGatewayInterface;
use IraniLMS\Domain\Commerce\Payment\Payment;

defined( 'ABSPATH' ) || exit;

final class NullGateway implements PaymentGatewayInterface {
    public function purchase( Payment $payment ): string {
        throw new \RuntimeException( 'No payment gateway has been configured.' );
    }

    public function verify( Payment $payment, array $callback_data = [] ): bool {
        return false;
    }

    public function get_id(): string { return 'none'; }
    public function get_name(): string { return __( 'بدون درگاه', 'irani-lms' ); }
}
