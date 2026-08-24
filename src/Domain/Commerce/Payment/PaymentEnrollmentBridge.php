<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Commerce\Payment;

use IraniLMS\Domain\Commerce\Order;
use IraniLMS\Domain\Enrollment\EnrollmentService;

defined( 'ABSPATH' ) || exit;

final class PaymentEnrollmentBridge {
    public function __construct(
        private readonly PaymentRepository $payments,
        private readonly Order $orders,
        private readonly EnrollmentService $enrollments
    ) {}

    public function handle( int $payment_id, int $order_id ): void {
        $payment = $this->payments->get( $payment_id );
        $order = $this->orders->get( $order_id );

        if ( empty( $payment ) || empty( $order ) || ( $payment['status'] ?? '' ) !== Payment::STATUS_PAID ) {
            return;
        }

        if ( ( $order['status'] ?? '' ) !== Order::STATUS_PAID ) {
            $this->orders->set_status( $order_id, Order::STATUS_PAID );
        }

        $this->enrollments->enroll(
            absint( $order['user_id'] ?? 0 ),
            absint( $order['course_id'] ?? 0 ),
            $order_id
        );
    }
}
