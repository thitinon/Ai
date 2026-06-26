<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Confirmation - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your payment has been received successfully.')
            ->line('Order ID: ' . $this->order->id)
            ->line('Amount: ' . number_format($this->order->final_amount, 2) . ' ' . $this->order->currency)
            ->line('Courses:')
            ->line($this->order->items->map(fn ($item) => '- ' . $item->course->title)->implode('\n'))
            ->action('View Dashboard', route('dashboard'))
            ->line('Thank you for your purchase!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'amount' => $this->order->final_amount,
            'courses' => $this->order->items->pluck('course.title')->toArray(),
            'message' => 'Your payment of ' . number_format($this->order->final_amount, 2) . ' has been confirmed.',
        ];
    }
}
