<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Single source of truth for all booking/pm status labels.
     * Key = DB ENUM value, Value = display label (identical for customer & admin).
     */
    public static function bookingStatuses(): array
    {
        return [
            'pending_payment'  => 'Pending Payment',
            'awaiting_approval' => 'Awaiting Approval',
            'paid'             => 'Paid',
            'used'             => 'Completed',
            'cancelled'        => 'Cancelled',
            'refund_requested' => 'Refund Requested',
            'refunded'         => 'Refunded',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            'pending'           => 'Pending',
            'awaiting_approval' => 'Awaiting Approval',
            'approved'          => 'Approved',
            'paid'              => 'Paid',
            'rejected'          => 'Rejected',
            'failed'            => 'Failed',
            'expired'           => 'Expired',
            'completed'         => 'Completed',
        ];
    }

    /**
     * Get the badge color class for a booking status.
     */
    public static function bookingBadgeClass(string $status): string
    {
        return match ($status) {
            'paid'             => 'bs-green',
            'pending_payment'  => 'bs-yellow',
            'awaiting_approval' => 'bs-yellow',
            'rejected'         => 'bs-red',
            'used'             => 'bs-blue',
            'expired'          => 'bs-red',
            'cancelled'        => 'bs-red',
            'refund_requested' => 'bs-orange',
            'refunded'         => 'bs-gray',
            default            => 'bs-gray',
        };
    }

    /**
     * Effective display status for a booking:
     * if payment was rejected → show "Rejected", else use booking_status.
     */
    public static function effectiveStatus(object $booking): string
    {
        if (($booking->payment_status ?? null) === 'rejected') {
            return 'rejected';
        }
        return $booking->booking_status;
    }

    public static function effectiveStatusLabel(object $booking): string
    {
        $status = self::effectiveStatus($booking);
        return self::bookingStatuses()[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public static function effectiveBadgeClass(object $booking): string
    {
        $status = self::effectiveStatus($booking);
        return self::bookingBadgeClass($status);
    }
}
