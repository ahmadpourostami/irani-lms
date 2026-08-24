<?php

declare(strict_types=1);

namespace IraniLMS\Api\Rest;

defined( 'ABSPATH' ) || exit;

abstract class RestController {
    protected const NAMESPACE = 'irani-lms/v1';

    protected function register_route( string $route, array $args ): void {
        register_rest_route( self::NAMESPACE, $route, $args );
    }

    protected function permission_logged_in(): bool {
        return is_user_logged_in();
    }

    protected function current_user_id(): int {
        return get_current_user_id();
    }
}
