<?php

namespace App\Listeners;

use App\Events\CoursePublished;

class NotifyFollowersOfPublishedCourse
{
    public function handle(CoursePublished $event): void
    {
        // Notify instructor's followers that a new course is published
        // $event->course->instructor->followers()->each(fn ($user) => 
        //     $user->notify(new CoursePublishedNotification($event->course))
        // );
    }
}
