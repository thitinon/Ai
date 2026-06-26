<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Events\UserEnrolledInCourse;
use App\Notifications\EnrollmentConfirmation;
use App\Notifications\PaymentConfirmation;

class SendPaymentNotification
{
    public function handle(PaymentCompleted $event): void
    {
        $event->order->user->notify(new PaymentConfirmation($event->order));
    }
}
