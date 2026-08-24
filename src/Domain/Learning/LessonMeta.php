<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Learning;

defined( 'ABSPATH' ) || exit;

final class LessonMeta {
    public const COURSE_ID = '_irani_lms_course_id';
    public const TYPE = '_irani_lms_lesson_type';
    public const VIDEO_URL = '_irani_lms_video_url';
    public const DURATION = '_irani_lms_duration';
    public const IS_PREVIEW = '_irani_lms_is_preview';
    public const SORT_ORDER = '_irani_lms_sort_order';

    public function get( int $lesson_id, string $key, mixed $default = null ): mixed {
        $value = get_post_meta( $lesson_id, $key, true );
        return '' === $value || false === $value ? $default : $value;
    }

    public function update( int $lesson_id, array $data ): void {
        foreach ( $data as $key => $value ) {
            switch ( $key ) {
                case self::COURSE_ID:
                case self::DURATION:
                case self::SORT_ORDER:
                    $value = absint( $value );
                    break;
                case self::IS_PREVIEW:
                    $value = ! empty( $value ) ? '1' : '0';
                    break;
                case self::TYPE:
                    $value = sanitize_key( (string) $value );
                    $value = in_array( $value, [ 'video', 'text', 'audio', 'file', 'embed' ], true ) ? $value : 'video';
                    break;
                case self::VIDEO_URL:
                    $value = esc_url_raw( (string) $value );
                    break;
            }

            update_post_meta( $lesson_id, $key, $value );
        }
    }
}
