<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Payment;

use IraniLMS\Domain\Commerce\Gateway\GatewayManager;

defined( 'ABSPATH' ) || exit;

final class PaymentService {
    public function __construct(
        private readonly PaymentRepository $payments,
        private readonly GatewayManager $gateways
    ) {}

    public function get_redirect_url( int $payment_id ): string {
        $data = $this->payments->get( $payment_id );
        if ( empty( $data ) ) {
            throw new \RuntimeException( 'Payment not found.' );
        }

        $gateway = $this->gateways->get( (string) ( $data['gateway'] ?? '' ) );
        if ( null === $gateway ) {
            throw new \RuntimeException( 'Payment gateway not found.' );
        }

        $payment = new Payment( $payment_id, $data );
        $redirect = $gateway->purchase( $payment );
        $this->payments->update( $payment_id, [ 'redirect_url' => esc_url_raw( $redirect ) ] );

        return $redirect;
    }

    public function verify( int $payment_id, array $callback_data = [] ): bool {
        $data = $this->payments->get( $payment_id );
        if ( empty( $data ) || ( $data['status'] ?? '' ) === Payment::STATUS_PAID ) {
            return false;
        }

        $gateway = $this->gateways->get( (string) ( $data['gateway'] ?? '' ) );
        if ( null === $gateway ) {
            return false;
        }

        $payment = new Payment( $payment_id, $data );
        if ( ! $gateway->verify( $payment, $callback_data ) ) {
            return false;
        }

        $this->payments->update( $payment_id, [
            'status' => Payment::STATUS_PAID,
            'paid_at' => current_time( 'mysql', true ),
        ] );

        do_action( 'irani_lms_payment_verified', $payment_id, $data );
        return true;
    }
}
