<?php

declare(strict_types=1);

namespace IraniLMS;

defined( 'ABSPATH' ) || exit;

use IraniLMS\Api\Rest\ApiServiceProvider;
use IraniLMS\Domain\Assessment\AssessmentServiceProvider;
use IraniLMS\Domain\Commerce\CommerceServiceProvider;
use IraniLMS\Domain\Course\CourseServiceProvider;
use IraniLMS\Domain\Enrollment\EnrollmentServiceProvider;
use IraniLMS\Domain\Learning\LearningServiceProvider;
use IraniLMS\Domain\Progress\ProgressServiceProvider;
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
        add_action( 'init', [ $this, 'load_textdomain' ], 2 );
        add_action( 'init', [ $this, 'register_core_hooks' ], 5 );
        $this->register_services();
    }

    public function load_textdomain(): void {
        load_plugin_textdomain( 'irani-lms', false, dirname( plugin_basename( IRANI_LMS_FILE ) ) . '/languages' );
    }

    public function register_services(): void {
        if ( $this->container->has( CourseServiceProvider::class ) ) {
            return;
        }

        $providers = [
            CourseServiceProvider::class,
            LearningServiceProvider::class,
            EnrollmentServiceProvider::class,
            CommerceServiceProvider::class,
            AssessmentServiceProvider::class,
            ProgressServiceProvider::class,
            ApiServiceProvider::class,
        ];

        foreach ( $providers as $provider_class ) {
            /** @var ServiceProviderInterface $provider */
            $provider = new $provider_class();
            $provider->register();
            $this->container->set( $provider_class, $provider );
        }

        foreach ( $providers as $provider_class ) {
            /** @var ServiceProviderInterface $provider */
            $this->container->get( $provider_class )->boot();
        }
    }

    public function register_core_hooks(): void {}

    public static function container(): ServiceContainer {
        if ( null === self::$instance ) {
            throw new \RuntimeException( 'Irani LMS has not been booted.' );
        }
        return self::$instance->container;
    }
}
