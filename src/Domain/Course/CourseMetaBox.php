<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

defined( 'ABSPATH' ) || exit;

final class CourseMetaBox {
    public function __construct( private readonly CourseMeta $meta ) {}

    public function register(): void {
        add_meta_box(
            'irani-lms-course-settings',
            __( 'تنظیمات دوره', 'irani-lms' ),
            [ $this, 'render' ],
            CoursePostType::POST_TYPE,
            'normal',
            'high'
        );
    }

    public function render( \WP_Post $post ): void {
        wp_nonce_field( 'irani_lms_save_course', 'irani_lms_course_nonce' );

        $price = (int) $this->meta->get( $post->ID, CourseMeta::PRICE, 0 );
        $sale  = (int) $this->meta->get( $post->ID, CourseMeta::SALE_PRICE, 0 );
        $level = (string) $this->meta->get( $post->ID, CourseMeta::LEVEL, 'all' );
        $duration = (int) $this->meta->get( $post->ID, CourseMeta::DURATION, 0 );
        $access = (int) $this->meta->get( $post->ID, CourseMeta::ACCESS_DAYS, 0 );
        ?>
        <p><label for="irani_lms_price"><strong><?php esc_html_e( 'قیمت (تومان)', 'irani-lms' ); ?></strong></label><br>
        <input class="widefat" type="number" min="0" id="irani_lms_price" name="irani_lms_price" value="<?php echo esc_attr( $price ); ?>"></p>

        <p><label for="irani_lms_sale_price"><strong><?php esc_html_e( 'قیمت فروش ویژه (تومان)', 'irani-lms' ); ?></strong></label><br>
        <input class="widefat" type="number" min="0" id="irani_lms_sale_price" name="irani_lms_sale_price" value="<?php echo esc_attr( $sale ); ?>"></p>

        <p><label for="irani_lms_level"><strong><?php esc_html_e( 'سطح دوره', 'irani-lms' ); ?></strong></label><br>
        <select class="widefat" id="irani_lms_level" name="irani_lms_level">
            <?php foreach ( [ 'all' => 'همه سطوح', 'beginner' => 'مقدماتی', 'intermediate' => 'متوسط', 'advanced' => 'پیشرفته' ] as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $level, $key ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select></p>

        <p><label for="irani_lms_duration"><strong><?php esc_html_e( 'مدت دوره (دقیقه)', 'irani-lms' ); ?></strong></label><br>
        <input class="widefat" type="number" min="0" id="irani_lms_duration" name="irani_lms_duration" value="<?php echo esc_attr( $duration ); ?>"></p>

        <p><label for="irani_lms_access_days"><strong><?php esc_html_e( 'مدت دسترسی (روز)', 'irani-lms' ); ?></strong></label><br>
        <input class="widefat" type="number" min="0" id="irani_lms_access_days" name="irani_lms_access_days" value="<?php echo esc_attr( $access ); ?>">
        <small><?php esc_html_e( 'صفر یعنی دسترسی بدون محدودیت زمانی.', 'irani-lms' ); ?></small></p>
        <?php
    }

    public function save( int $post_id ): void {
        if ( ! isset( $_POST['irani_lms_course_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['irani_lms_course_nonce'] ) ), 'irani_lms_save_course' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $this->meta->update( $post_id, [
            CourseMeta::PRICE       => $_POST['irani_lms_price'] ?? 0,
            CourseMeta::SALE_PRICE  => $_POST['irani_lms_sale_price'] ?? 0,
            CourseMeta::LEVEL       => $_POST['irani_lms_level'] ?? 'all',
            CourseMeta::DURATION    => $_POST['irani_lms_duration'] ?? 0,
            CourseMeta::ACCESS_DAYS => $_POST['irani_lms_access_days'] ?? 0,
        ] );
    }
}
