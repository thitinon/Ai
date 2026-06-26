<?php

namespace App\Services\Video;

use App\Models\Video;
use App\Models\Lesson;
use App\Jobs\TranscodeVideoJob;
use Illuminate\Support\Facades\Storage;

class VideoService
{
    /**
     * Upload and queue video for processing
     */
    public function uploadVideo(Lesson $lesson, $file): Video
    {
        // Store original file
        $path = $file->store('videos/originals', 's3');

        // Create video record
        $video = Video::create([
            'lesson_id' => $lesson->id,
            'original_path' => $path,
            'status' => 'processing',
        ]);

        // Queue transcoding job
        TranscodeVideoJob::dispatch($video);

        return $video;
    }

    /**
     * Get video streaming URL (can be signed if private)
     */
    public function getStreamingUrl(Video $video, int $expirationMinutes = 60): ?string
    {
        if (!$video->hls_path) {
            return null;
        }

        // If private, generate signed URL
        if ($this->isPrivate($video)) {
            return Storage::disk('s3')->temporaryUrl(
                $this->getS3PathFromUrl($video->hls_path),
                now()->addMinutes($expirationMinutes)
            );
        }

        return $video->hls_path;
    }

    /**
     * Check if video should be private (behind paywall)
     */
    protected function isPrivate(Video $video): bool
    {
        return !$video->lesson->is_free_preview && !$video->lesson->section->is_free_preview;
    }

    /**
     * Extract S3 path from full URL
     */
    protected function getS3PathFromUrl(string $url): string
    {
        return str_replace(Storage::disk('s3')->url(''), '', $url);
    }
}
