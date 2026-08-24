<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Learning;

defined( 'ABSPATH' ) || exit;

final class LessonMetaBox {
    public function __construct( private readonly LessonMeta $meta ) {}

    public function register(): void {
        add_meta_box(
            'irani-lms-lesson-settings',
            __( 'تنظیمات درس', 'irani-lms' ),
            [ $this, 'render' ],
            LessonPostType::POST_TYPE,
            'normal',
            'high'
        );
    }

    public function render( \WP_Post $post ): void {
        wp_nonce_field( 'irani_lms_save_lesson', 'irani_lms_lesson_nonce' );

        $course_id = (int) $this->meta->get( $post->ID, LessonMeta::COURSE_ID, 0 );
        $type = (string) $this->meta->get( $post->ID, LessonMeta::TYPE, 'video' );
        $video_url = (string) $this->meta->get( $post->ID, LessonMeta::VIDEO_URL, '' );
        $duration = (int) $this->meta->get( $post->ID, LessonMeta::DURATION, 0 );
        $preview = (string) $this->meta->get( $post->ID, LessonMeta::IS_PREVIEW, '0' );
        ?>
        <p><label for="irani_lms_course_id"><strong><?php esc_html_e( 'شناسه دوره', 'irani-lms' ); ?></strong></label><br>
        <input class="widefat" type="number" min="0" id="irani_lms_course_id" name="irani_lms_course_id" value="<?php echo esc_attr( $course_id ); ?>"></p>

        <p><label for="irani_lms_lesson_type"><strong><?php esc_html_e( 'نوع درس', 'irani-lms' ); ?></strong></label><br>
        <select class="widefat" id="irani_lms_lesson_type" name="irani_lms_lesson_type">
            <?php foreach ( [ 'video' => 'ویدئو', 'text' => 'متنی', 'audio' => 'صوتی', 'file' => 'فایل', 'embed' => 'Embed' ] as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $type, $key ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select></p>

        <p><label for="irani_lms_video_url"><strong><?php esc_html_e( 'آدرس ویدئو / رسانه', 'irani-lms' ); ?></strong></label><br>
        <input class="widefat" type="url" id="irani_lms_video_url" name="irani_lms_video_url" value="<?php echo esc_attr( $video_url ); ?>"></p>

        <p><label for="irani_lms_lesson_duration"><strong><?php esc_html_e( 'مدت درس (دقیقه)', 'irani-lms' ); ?></strong></label><br>
        <input class="widefat" type="number" min="0" id="irani_lms_lesson_duration" name="irani_lms_lesson_duration" value="<?php echo esc_attr( $duration ); ?>"></p>

        <p><label><input type="checkbox" name="irani_lms_is_preview" value="1" <?php checked( $preview, '1' ); ?>> <?php esc_html_e( 'این درس برای پیش‌نمایش رایگان باشد', 'irani-lms' ); ?></label></p>
        <?php
    }

    public function save( int $post_id ): void {
        if ( ! isset( $_POST['irani_lms_lesson_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['irani_lms_lesson_nonce'] ) ), 'irani_lms_save_lesson' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $this->meta->update( $post_id, [
            LessonMeta::COURSE_ID => $_POST['irani_lms_course_id'] ?? 0,
            LessonMeta::TYPE => $_POST['irani_lms_lesson_type'] ?? 'video',
            LessonMeta::VIDEO_URL => $_POST['irani_lms_video_url'] ?? '',
            LessonMeta::DURATION => $_POST['irani_lms_lesson_duration'] ?? 0,
            LessonMeta::IS_PREVIEW => $_POST['irani_lms_is_preview'] ?? 0,
        ] );
    }
}
