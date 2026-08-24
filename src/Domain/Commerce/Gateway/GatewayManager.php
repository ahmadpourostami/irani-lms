<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Gateway;

use IraniLMS\Domain\Commerce\Contracts\PaymentGatewayInterface;

defined( 'ABSPATH' ) || exit;

final class GatewayManager {
    /** @var PaymentGatewayInterface[] */
    private array $gateways = [];

    public function register( PaymentGatewayInterface $gateway ): void {
        $this->gateways[ $gateway->get_id() ] = $gateway;
    }

    public function get( string $id ): ?PaymentGatewayInterface {
        return $this->gateways[ sanitize_key( $id ) ] ?? null;
    }

    /** @return PaymentGatewayInterface[] */
    public function all(): array {
        return $this->gateways;
    }
}
