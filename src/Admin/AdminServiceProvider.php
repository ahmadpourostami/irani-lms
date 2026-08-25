<?php

declare(strict_types=1);

namespace IraniLMS\Admin;

use IraniLMS\Support\ServiceProviderInterface;

defined( 'ABSPATH' ) || exit;

final class AdminServiceProvider implements ServiceProviderInterface {
    private AdminMenu $menu;

    public function register(): void {
        $this->menu = new AdminMenu();
    }

    public function boot(): void {
        add_action( 'admin_menu', [ $this->menu, 'register' ], 5 );
    }
}
