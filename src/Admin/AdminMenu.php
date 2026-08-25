<?php

declare(strict_types=1);

namespace IraniLMS\Admin;

use IraniLMS\Domain\Commerce\OrderPostType;
use IraniLMS\Domain\Commerce\PaymentPostType;
use IraniLMS\Domain\Course\CoursePostType;
use IraniLMS\Domain\Enrollment\EnrollmentPostType;

defined( 'ABSPATH' ) || exit;

final class AdminMenu {
    public const SLUG = 'irani-lms';
    public const CAPABILITY = 'edit_posts';

    /**
     * Registered on 'admin_menu' at priority 5, i.e. before WordPress core's
     * _add_post_type_menus() (priority 10) turns each post type's
     * 'show_in_menu' => self::SLUG into a submenu here. The parent page must
     * exist first or the submenus have nowhere to attach.
     */
    public function register(): void {
        add_menu_page(
            __( 'ایرانی ال‌ام‌اس', 'irani-lms' ),
            __( 'Irani LMS', 'irani-lms' ),
            self::CAPABILITY,
            self::SLUG,
            [ $this, 'render_overview' ],
            'dashicons-welcome-learn-more',
            25
        );

        add_submenu_page(
            self::SLUG,
            __( 'بررسی کلی', 'irani-lms' ),
            __( 'بررسی کلی', 'irani-lms' ),
            self::CAPABILITY,
            self::SLUG,
            [ $this, 'render_overview' ]
        );
    }

    public function render_overview(): void {
        if ( ! current_user_can( self::CAPABILITY ) ) {
            return;
        }

        $counts = [
            'courses'     => wp_count_posts( CoursePostType::POST_TYPE )->publish ?? 0,
            'enrollments' => wp_count_posts( EnrollmentPostType::POST_TYPE )->publish ?? 0,
            'orders'      => wp_count_posts( OrderPostType::POST_TYPE )->publish ?? 0,
            'payments'    => wp_count_posts( PaymentPostType::POST_TYPE )->publish ?? 0,
        ];
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Irani LMS', 'irani-lms' ); ?></h1>
            <p><?php esc_html_e( 'خلاصه‌ی وضعیت پلتفرم آموزشی شما.', 'irani-lms' ); ?></p>

            <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:16px;">
                <?php
                $cards = [
                    'courses'     => __( 'دوره‌ها', 'irani-lms' ),
                    'enrollments' => __( 'ثبت‌نام‌ها', 'irani-lms' ),
                    'orders'      => __( 'سفارش‌ها', 'irani-lms' ),
                    'payments'    => __( 'پرداخت‌ها', 'irani-lms' ),
                ];
                foreach ( $cards as $key => $label ) :
                    ?>
                    <div class="card" style="min-width:160px;">
                        <h2 style="margin:0 0 8px;font-size:28px;"><?php echo esc_html( (string) $counts[ $key ] ); ?></h2>
                        <p style="margin:0;color:#646970;"><?php echo esc_html( $label ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <p style="margin-top:24px;">
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . CoursePostType::POST_TYPE ) ); ?>" class="button button-primary">
                    <?php esc_html_e( 'مدیریت دوره‌ها', 'irani-lms' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . CoursePostType::POST_TYPE ) ); ?>" class="button">
                    <?php esc_html_e( 'افزودن دوره جدید', 'irani-lms' ); ?>
                </a>
            </p>
        </div>
        <?php
    }
}
