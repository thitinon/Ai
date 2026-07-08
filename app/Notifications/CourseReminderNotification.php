<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Course $course)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Continue Learning: ' . $this->course->title . ' - ' . config('app.name'))
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('We noticed you haven\'t completed ' . $this->course->title . ' yet.')
            ->line('Keep up the progress and finish strong!')
            ->action('Continue Course', route('courses.show', $this->course))
            ->line('See you in class!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'message' => 'Don\'t forget to continue ' . $this->course->title,
        ];
    }
}
