<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

defined( 'ABSPATH' ) || exit;

final class CoursePricingMetaBox {
    public function register(): void {
        add_meta_box(
            'irani_lms_course_pricing',
            __( 'قیمت دوره', 'irani-lms' ),
            [ $this, 'render' ],
            CoursePostType::POST_TYPE,
            'side',
            'high'
        );
    }

    public function render( \WP_Post $post ): void {
        wp_nonce_field( 'irani_lms_save_course_pricing', 'irani_lms_course_pricing_nonce' );
        $pricing = ( new CoursePricing() )->get( $post->ID );
        ?>
        <p>
            <label for="irani_lms_price"><strong><?php esc_html_e( 'قیمت اصلی (تومان)', 'irani-lms' ); ?></strong></label>
            <input class="widefat" type="number" min="0" step="1" id="irani_lms_price" name="irani_lms_price" value="<?php echo esc_attr( $pricing['price'] ); ?>">
        </p>
        <p>
            <label for="irani_lms_sale_price"><strong><?php esc_html_e( 'قیمت فروش ویژه (تومان)', 'irani-lms' ); ?></strong></label>
            <input class="widefat" type="number" min="0" step="1" id="irani_lms_sale_price" name="irani_lms_sale_price" value="<?php echo esc_attr( $pricing['sale_price'] ); ?>">
        </p>
        <p class="description"><?php esc_html_e( 'مبلغ نهایی در Checkout از سمت سرور محاسبه می‌شود.', 'irani-lms' ); ?></p>
        <?php
    }

    public function save( int $post_id ): void {
        if ( CoursePostType::POST_TYPE !== get_post_type( $post_id ) ) {
            return;
        }
        if ( ! isset( $_POST['irani_lms_course_pricing_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['irani_lms_course_pricing_nonce'] ) ), 'irani_lms_save_course_pricing' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $price = isset( $_POST['irani_lms_price'] ) ? absint( $_POST['irani_lms_price'] ) : 0;
        $sale = isset( $_POST['irani_lms_sale_price'] ) ? absint( $_POST['irani_lms_sale_price'] ) : 0;

        if ( $sale > 0 && $sale >= $price ) {
            $sale = 0;
        }

        ( new CoursePricing() )->set( $post_id, $price, $sale );
    }
}
