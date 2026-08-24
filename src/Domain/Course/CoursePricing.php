<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Course;

defined( 'ABSPATH' ) || exit;

final class CoursePricing {
    public const META_KEY = '_irani_lms_course_pricing';
    public const CURRENCY_IRT = 'IRT';

    public function get( int $course_id ): array {
        $raw = get_post_meta( $course_id, self::META_KEY, true );
        $data = is_array( $raw ) ? $raw : [];

        return [
            'price' => max( 0, absint( $data['price'] ?? 0 ) ),
            'currency' => self::CURRENCY_IRT,
            'sale_price' => max( 0, absint( $data['sale_price'] ?? 0 ) ),
        ];
    }

    public function get_payable_amount( int $course_id ): int {
        $pricing = $this->get( $course_id );
        $sale = $pricing['sale_price'];

        if ( $sale > 0 && $sale < $pricing['price'] ) {
            return $sale;
        }

        return $pricing['price'];
    }

    public function set( int $course_id, int $price, int $sale_price = 0 ): void {
        if ( $course_id <= 0 || $price < 0 || $sale_price < 0 ) {
            throw new \InvalidArgumentException( 'Invalid course pricing.' );
        }

        if ( $sale_price > 0 && $sale_price >= $price ) {
            throw new \InvalidArgumentException( 'Sale price must be lower than regular price.' );
        }

        update_post_meta( $course_id, self::META_KEY, [
            'price' => $price,
            'sale_price' => $sale_price,
            'currency' => self::CURRENCY_IRT,
        ] );
    }
}
