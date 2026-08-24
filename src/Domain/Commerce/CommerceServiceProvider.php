<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce;

use IraniLMS\Domain\Commerce\Payment\PaymentRepository;
use IraniLMS\Support\ServiceProviderInterface;

final class CommerceServiceProvider implements ServiceProviderInterface {
    private OrderPostType $post_type;
    private PaymentPostType $payment_post_type;
    private Order $order;
    private PaymentRepository $payments;

    public function register(): void {
        $this->post_type = new OrderPostType();
        $this->payment_post_type = new PaymentPostType();
        $this->order = new Order();
        $this->payments = new PaymentRepository();
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
        add_action( 'init', [ $this->payment_post_type, 'register' ] );
    }

    public function order(): Order {
        return $this->order;
    }

    public function payments(): PaymentRepository {
        return $this->payments;
    }
}
