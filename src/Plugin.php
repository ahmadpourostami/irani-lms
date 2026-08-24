<?php

declare(strict_types=1);

namespace IraniLMS;

defined( 'ABSPATH' ) || exit;

use IraniLMS\Domain\Course\CourseServiceProvider;
use IraniLMS\Domain\Learning\LearningServiceProvider;
use IraniLMS\Support\ServiceContainer;
use IraniLMS\Support\ServiceProviderInterface;

final class Plugin {
    private static ?self $instance = null;
    private ServiceContainer $container;

    public static function boot(): void {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
    }

    private function __construct() {
        $this->container = new ServiceContainer();

        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
        add_action( 'plugins_loaded', [ $this, 'register_services' ], 20 );
        add_action( 'init', [ $this, 'register_core_hooks' ], 5 );
    }

    public function load_textdomain(): void {
        load_plugin_textdomain(
            'irani-lms',
            false,
            dirname( plugin_basename( IRANI_LMS_FILE ) ) . '/languages'
        );
    }

    public function register_services(): void {
        $providers = [
            CourseServiceProvider::class,
            LearningServiceProvider::class,
        ];

        foreach ( $providers as $provider_class ) {
            /** @var ServiceProviderInterface $provider */
            $provider = new $provider_class();
            $provider->register();
            $this->container->set( $provider_class, $provider );
        }

        foreach ( $providers as $provider_class ) {
            /** @var ServiceProviderInterface $provider */
            $provider = $this->container->get( $provider_class );
            $provider->boot();
        }
    }

    public function register_core_hooks(): void {
        // Core hooks will be registered here as the domain modules are introduced.
    }

    public static function container(): ServiceContainer {
        if ( null === self::$instance ) {
            throw new \RuntimeException( 'Irani LMS has not been booted.' );
        }

        return self::$instance->container;
    }
}
