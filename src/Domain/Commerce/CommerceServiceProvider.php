<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce;

use IraniLMS\Domain\Commerce\Checkout\CheckoutService;
use IraniLMS\Domain\Commerce\Gateway\GatewayManager;
use IraniLMS\Domain\Commerce\Gateway\NullGateway;
use IraniLMS\Domain\Commerce\Payment\PaymentEnrollmentBridge;
use IraniLMS\Domain\Commerce\Payment\PaymentRepository;
use IraniLMS\Domain\Commerce\Payment\PaymentService;
use IraniLMS\Domain\Enrollment\EnrollmentService;
use IraniLMS\Support\ServiceProviderInterface;

final class CommerceServiceProvider implements ServiceProviderInterface {
    private OrderPostType $post_type;
    private PaymentPostType $payment_post_type;
    private Order $order;
    private PaymentRepository $payments;
    private GatewayManager $gateways;
    private CheckoutService $checkout;
    private PaymentService $payment_service;
    private PaymentEnrollmentBridge $enrollment_bridge;

    public function register(): void {
        $this->post_type = new OrderPostType();
        $this->payment_post_type = new PaymentPostType();
        $this->order = new Order();
        $this->payments = new PaymentRepository();
        $this->gateways = new GatewayManager();
        $this->gateways->register( new NullGateway() );
        $this->checkout = new CheckoutService( $this->order, $this->payments, $this->gateways );
        $this->payment_service = new PaymentService( $this->payments, $this->gateways );
        $this->enrollment_bridge = new PaymentEnrollmentBridge( $this->payments, $this->order, new EnrollmentService() );
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
        add_action( 'init', [ $this->payment_post_type, 'register' ] );
        add_action( 'irani_lms_payment_verified', [ $this, 'enroll_after_payment' ], 10, 2 );
        do_action( 'irani_lms_register_payment_gateways', $this->gateways );
    }

    public function enroll_after_payment( int $payment_id, int $order_id ): void {
        $this->enrollment_bridge->handle( $payment_id, $order_id );
    }

    public function order(): Order { return $this->order; }
    public function payments(): PaymentRepository { return $this->payments; }
    public function gateways(): GatewayManager { return $this->gateways; }
    public function checkout(): CheckoutService { return $this->checkout; }
    public function payment_service(): PaymentService { return $this->payment_service; }
}
