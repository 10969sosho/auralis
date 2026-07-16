<?php

namespace App\Helpers;

use App\Mail\BoardingSuccessEmail;
use App\Mail\BookingCancelledEmail;
use App\Mail\BookingGuestEmail;
use App\Mail\BookingPendingEmail;
use App\Mail\PaymentApprovedEmail;
use App\Mail\ScheduleChangedEmail;
use App\Mail\WelcomeEmail;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class MailHelper
{
    public static function sendWelcome(User $user, string $password = ''): void
    {
        if (!$user->email) return;
        Mail::to($user->email)->send(new WelcomeEmail($user, $password));
    }

    public static function sendBookingPending(Booking $booking): void
    {
        $user = $booking->user;
        if (!$user?->email) return;
        $paymentUrl = route('booking.payment', $booking->booking_code);
        Mail::to($user->email)->send(new BookingPendingEmail($booking, $paymentUrl));
    }

    public static function sendBookingGuest(Booking $booking): void
    {
        if (!$booking->guest_email) return;
        $bookingLink = route('booking.guest', ['code' => $booking->booking_code, 'token' => $booking->guest_token]);
        Mail::to($booking->guest_email)->send(new BookingGuestEmail($booking, $bookingLink));
    }

    public static function sendPaymentApproved(Booking $booking): void
    {
        $email = $booking->user?->email ?? $booking->guest_email;
        if (!$email) return;

        if ($booking->guest_email) {
            $ticketUrl = route('booking.guest', ['code' => $booking->booking_code, 'token' => $booking->guest_token]);
        } else {
            $ticketUrl = route('booking.detail', $booking->booking_code);
        }

        Mail::to($email)->send(new PaymentApprovedEmail($booking, $ticketUrl));
    }

    public static function sendScheduleChanged(Booking $booking, Schedule $old, Schedule $new): void
    {
        $user = $booking->user;
        if (!$user?->email) return;
        Mail::to($user->email)->send(new ScheduleChangedEmail($booking, $old, $new));
    }

    public static function sendBookingCancelled(Booking $booking, string $reason = ''): void
    {
        $email = $booking->user?->email ?? $booking->guest_email;
        if (!$email) return;
        Mail::to($email)->send(new BookingCancelledEmail($booking, $reason));
    }

    public static function sendBoardingSuccess(Booking $booking, string $passengerName = ''): void
    {
        $email = $booking->user?->email ?? $booking->guest_email;
        if (!$email) return;
        Mail::to($email)->send(new BoardingSuccessEmail($booking, $passengerName));
    }
}
