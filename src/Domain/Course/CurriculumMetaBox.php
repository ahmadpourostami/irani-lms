<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

defined( 'ABSPATH' ) || exit;

final class CurriculumMetaBox {
    public function __construct( private readonly Curriculum $curriculum ) {}

    public function register(): void {
        add_meta_box(
            'irani-lms-curriculum',
            __( 'سرفصل دوره', 'irani-lms' ),
            [ $this, 'render' ],
            CoursePostType::POST_TYPE,
            'normal',
            'default'
        );
    }

    public function render( \WP_Post $post ): void {
        $sections = $this->curriculum->get_sections( $post->ID );
        wp_nonce_field( 'irani_lms_save_curriculum', 'irani_lms_curriculum_nonce' );
        ?>
        <div id="irani-lms-curriculum-builder">
            <?php if ( empty( $sections ) ) : ?>
                <p><?php esc_html_e( 'هنوز بخشی برای این دوره ایجاد نشده است.', 'irani-lms' ); ?></p>
            <?php else : ?>
                <?php foreach ( $sections as $section_index => $section ) : ?>
                    <div class="irani-lms-section" style="margin-bottom:16px;padding:12px;border:1px solid #ddd;">
                        <p><input class="widefat" type="text" name="irani_lms_sections[<?php echo esc_attr( $section_index ); ?>][title]" value="<?php echo esc_attr( $section['title'] ?? '' ); ?>" placeholder="عنوان بخش"></p>
                        <?php foreach ( (array) ( $section['lessons'] ?? [] ) as $lesson_index => $lesson_id ) : ?>
                            <input type="hidden" name="irani_lms_sections[<?php echo esc_attr( $section_index ); ?>][lessons][<?php echo esc_attr( $lesson_index ); ?>]" value="<?php echo esc_attr( $lesson_id ); ?>">
                        <?php endforeach; ?>
                        <p><small><?php echo esc_html( count( (array) ( $section['lessons'] ?? [] ) ) ); ?> درس</small></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <p class="description"><?php esc_html_e( 'ساخت رابط Drag & Drop در مرحله UI Builder انجام می‌شود؛ داده‌ها از همین ساختار پایدار استفاده خواهند کرد.', 'irani-lms' ); ?></p>
        </div>
        <?php
    }

    public function save( int $post_id ): void {
        if ( ! isset( $_POST['irani_lms_curriculum_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['irani_lms_curriculum_nonce'] ) ), 'irani_lms_save_curriculum' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $sections = isset( $_POST['irani_lms_sections'] ) && is_array( $_POST['irani_lms_sections'] )
            ? wp_unslash( $_POST['irani_lms_sections'] )
            : [];

        $this->curriculum->save( $post_id, $sections );
    }
}
