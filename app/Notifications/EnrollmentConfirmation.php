<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Enrollment $enrollment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Course Enrollment Confirmed - ' . config('app.name'))
            ->greeting('Welcome to ' . $this->enrollment->course->title . '!')
            ->line('You have been successfully enrolled in the course.')
            ->line('Instructor: ' . $this->enrollment->course->instructor->name)
            ->line('Level: ' . ucfirst($this->enrollment->course->level))
            ->action('Start Learning', route('courses.show', $this->enrollment->course))
            ->line('Happy learning!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'course_title' => $this->enrollment->course->title,
            'message' => 'You are now enrolled in ' . $this->enrollment->course->title,
        ];
    }
}
