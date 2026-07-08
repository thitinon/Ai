<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseCompletedNotification extends Notification implements ShouldQueue
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
            ->subject('Course Completed! - ' . config('app.name'))
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line('You have successfully completed the course: ' . $this->enrollment->course->title)
            ->line('Your certificate has been generated and is ready to download.')
            ->action('Download Certificate', route('certificates.download', $this->enrollment->id))
            ->line('Share your achievement with others!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'enrollment_id' => $this->enrollment->id,
            'course_title' => $this->enrollment->course->title,
            'message' => 'Congratulations! You completed ' . $this->enrollment->course->title,
        ];
    }
}
