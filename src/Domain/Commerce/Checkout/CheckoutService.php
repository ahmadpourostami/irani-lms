<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Checkout;

use IraniLMS\Domain\Commerce\Gateway\GatewayManager;
use IraniLMS\Domain\Commerce\Order;
use IraniLMS\Domain\Commerce\Payment\PaymentRepository;

defined( 'ABSPATH' ) || exit;

final class CheckoutService {
    public function __construct(
        private readonly Order $orders,
        private readonly PaymentRepository $payments,
        private readonly GatewayManager $gateways
    ) {}

    public function create( int $user_id, int $course_id, int $amount, string $gateway_id ): array {
        if ( $user_id <= 0 || $course_id <= 0 || $amount < 0 ) {
            throw new \InvalidArgumentException( 'Invalid checkout data.' );
        }

        $gateway = $this->gateways->get( $gateway_id );
        if ( null === $gateway ) {
            throw new \RuntimeException( 'Payment gateway not found.' );
        }

        $order_id = $this->orders->create( $user_id, $course_id, $amount, 'IRT' );
        $payment_id = $this->payments->create( $order_id, $amount, 'IRT', $gateway->get_id() );

        do_action( 'irani_lms_checkout_created', $order_id, $payment_id, $course_id, $user_id );

        return [
            'order_id' => $order_id,
            'payment_id' => $payment_id,
            'gateway' => $gateway->get_id(),
        ];
    }
}
