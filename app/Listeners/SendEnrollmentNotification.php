<?php

namespace App\Listeners;

use App\Events\UserEnrolledInCourse;
use App\Notifications\EnrollmentConfirmation;

class SendEnrollmentNotification
{
    public function handle(UserEnrolledInCourse $event): void
    {
        $event->enrollment->user->notify(new EnrollmentConfirmation($event->enrollment));
    }
}
