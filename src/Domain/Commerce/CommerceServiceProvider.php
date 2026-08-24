<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce;

use IraniLMS\Domain\Commerce\Gateway\GatewayManager;
use IraniLMS\Domain\Commerce\Gateway\NullGateway;
use IraniLMS\Domain\Commerce\Payment\PaymentRepository;
use IraniLMS\Support\ServiceProviderInterface;

final class CommerceServiceProvider implements ServiceProviderInterface {
    private OrderPostType $post_type;
    private PaymentPostType $payment_post_type;
    private Order $order;
    private PaymentRepository $payments;
    private GatewayManager $gateways;

    public function register(): void {
        $this->post_type = new OrderPostType();
        $this->payment_post_type = new PaymentPostType();
        $this->order = new Order();
        $this->payments = new PaymentRepository();
        $this->gateways = new GatewayManager();
        $this->gateways->register( new NullGateway() );
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
        add_action( 'init', [ $this->payment_post_type, 'register' ] );
        do_action( 'irani_lms_register_payment_gateways', $this->gateways );
    }

    public function order(): Order { return $this->order; }
    public function payments(): PaymentRepository { return $this->payments; }
    public function gateways(): GatewayManager { return $this->gateways; }
}
