<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

defined( 'ABSPATH' ) || exit;

final class CourseMeta {
    public const PRICE          = '_irani_lms_price';
    public const SALE_PRICE     = '_irani_lms_sale_price';
    public const CURRENCY       = '_irani_lms_currency';
    public const LEVEL          = '_irani_lms_level';
    public const DURATION       = '_irani_lms_duration';
    public const ACCESS_DAYS    = '_irani_lms_access_days';
    public const STATUS         = '_irani_lms_course_status';
    public const PREVIEW_LESSON = '_irani_lms_preview_lesson';

    public function get( int $course_id, string $key, mixed $default = null ): mixed {
        $value = get_post_meta( $course_id, $key, true );
        return '' === $value || false === $value ? $default : $value;
    }

    public function update( int $course_id, array $data ): void {
        $integer_keys = [ self::PRICE, self::SALE_PRICE, self::DURATION, self::ACCESS_DAYS, self::PREVIEW_LESSON ];
        $allowed_levels = [ 'beginner', 'intermediate', 'advanced', 'all' ];
        $allowed_statuses = [ 'draft', 'published', 'private' ];

        foreach ( $data as $key => $value ) {
            if ( in_array( $key, $integer_keys, true ) ) {
                $value = max( 0, absint( $value ) );
            } elseif ( self::CURRENCY === $key ) {
                $value = strtoupper( sanitize_key( (string) $value ) );
                $value = in_array( $value, [ 'IRT', 'IRR' ], true ) ? $value : 'IRT';
            } elseif ( self::LEVEL === $key ) {
                $value = sanitize_key( (string) $value );
                $value = in_array( $value, $allowed_levels, true ) ? $value : 'all';
            } elseif ( self::STATUS === $key ) {
                $value = sanitize_key( (string) $value );
                $value = in_array( $value, $allowed_statuses, true ) ? $value : 'draft';
            }

            update_post_meta( $course_id, $key, $value );
        }
    }

    public function price( int $course_id ): int {
        $sale = (int) $this->get( $course_id, self::SALE_PRICE, 0 );
        return $sale > 0 ? $sale : (int) $this->get( $course_id, self::PRICE, 0 );
    }
}
