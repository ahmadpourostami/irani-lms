<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Checkout;

use IraniLMS\Domain\Commerce\Gateway\GatewayManager;
use IraniLMS\Domain\Commerce\Order;
use IraniLMS\Domain\Commerce\Payment\PaymentRepository;
use IraniLMS\Domain\Course\CoursePostType;
use IraniLMS\Domain\Course\CoursePricing;

defined( 'ABSPATH' ) || exit;

final class CheckoutService {
    public function __construct(
        private readonly Order $orders,
        private readonly PaymentRepository $payments,
        private readonly GatewayManager $gateways,
        private readonly CoursePricing $pricing = new CoursePricing()
    ) {}

    public function create( int $user_id, int $course_id, string $gateway_id ): array {
        if ( $user_id <= 0 || $course_id <= 0 ) {
            throw new \InvalidArgumentException( 'Invalid checkout data.' );
        }

        $course = get_post( $course_id );
        if ( ! $course || CoursePostType::POST_TYPE !== $course->post_type || 'publish' !== $course->post_status ) {
            throw new \RuntimeException( 'Course not found.' );
        }

        $amount = $this->pricing->get_payable_amount( $course_id );
        if ( $amount <= 0 ) {
            throw new \RuntimeException( 'This course is free and does not require payment.' );
        }

        $gateway = $this->gateways->get( $gateway_id );
        if ( null === $gateway ) {
            throw new \RuntimeException( 'Payment gateway not found.' );
        }

        $order_id = $this->orders->create( $user_id, $course_id, $amount, CoursePricing::CURRENCY_IRT );
        $payment_id = $this->payments->create( $order_id, $amount, CoursePricing::CURRENCY_IRT, $gateway->get_id() );

        do_action( 'irani_lms_checkout_created', $order_id, $payment_id, $course_id, $user_id );

        return [
            'order_id' => $order_id,
            'payment_id' => $payment_id,
            'amount' => $amount,
            'currency' => CoursePricing::CURRENCY_IRT,
            'gateway' => $gateway->get_id(),
        ];
    }
}
