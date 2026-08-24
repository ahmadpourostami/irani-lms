<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

use IraniLMS\Domain\Commerce\CommerceServiceProvider;
use IraniLMS\Plugin;

defined( 'ABSPATH' ) || exit;

final class GatewayController extends RestController {
    public function register(): void {
        $this->register_route( '/payment-gateways', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'index' ],
            'permission_callback' => [ $this, 'permission_authenticated' ],
        ] );
    }

    public function index(): \WP_REST_Response {
        $commerce = Plugin::container()->get( CommerceServiceProvider::class );
        $items = [];
        foreach ( $commerce->gateways()->all() as $gateway ) {
            $items[] = [ 'id' => $gateway->get_id(), 'name' => $gateway->get_name() ];
        }
        return new \WP_REST_Response( [ 'items' => $items ] );
    }
}
