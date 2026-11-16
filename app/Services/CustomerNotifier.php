<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerNotifier
{
    public function notifyReadyForPickup(Order $order): void
    {
        $order->loadMissing('customer');
        $customer = $order->customer;
        $message = sprintf(
            'Hi %s! Your laundry (Order #%s) at Waterbonia Laundry Shop is now ready for pickup. Total due: PHP %s. Thank you!',
            $customer->full_name,
            $order->order_number,
            number_format($order->balance > 0 ? $order->balance : 0, 2),
        );

        if ($customer->phone) {
            Notification::create([
                'order_id' => $order->id,
                'channel' => 'sms',
                'recipient' => $customer->phone,
                'message' => $message,
                'status' => 'sent',
            ]);

            Log::info('SMS notification queued (no live SMS gateway configured).', [
                'order' => $order->order_number,
                'recipient' => $customer->phone,
                'message' => $message,
            ]);
        }

        if ($customer->email) {
            try {
                Mail::raw($message, function ($mail) use ($customer) {
                    $mail->to($customer->email)->subject('Your laundry is ready for pickup - Waterbonia');
                });

                Notification::create([
                    'order_id' => $order->id,
                    'channel' => 'email',
                    'recipient' => $customer->email,
                    'message' => $message,
                    'status' => 'sent',
                ]);
            } catch (\Throwable $e) {
                Log::warning('Email notification failed: '.$e->getMessage());

                Notification::create([
                    'order_id' => $order->id,
                    'channel' => 'email',
                    'recipient' => $customer->email,
                    'message' => $message,
                    'status' => 'failed',
                ]);
            }
        }
    }
}
