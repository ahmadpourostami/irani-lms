<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce;

use IraniLMS\Support\ServiceProviderInterface;

final class CommerceServiceProvider implements ServiceProviderInterface {
    private OrderPostType $post_type;
    private Order $order;

    public function register(): void {
        $this->post_type = new OrderPostType();
        $this->order = new Order();
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
    }

    public function order(): Order {
        return $this->order;
    }
}
