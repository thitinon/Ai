<?php

namespace App\Jobs;

use App\Models\Video;
use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video as VideoFormat;
use FFMpeg\Format\FormatInterface;
use Illuminate\Support\Facades\Storage;
use Exception;

class TranscodeVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour
    public $tries = 3;

    public function __construct(protected Video $video)
    {
    }

    public function handle(): void
    {
        try {
            $this->video->update(['status' => 'processing']);

            // Get video file from storage
            $disk = Storage::disk(config('filesystems.default'));
            $originalPath = $this->video->original_path;

            if (!$disk->exists($originalPath)) {
                throw new Exception('Original video file not found: ' . $originalPath);
            }

            // Get full path for FFMpeg
            $localPath = $disk->path($originalPath);

            // Initialize FFMpeg
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => env('FFMPEG_BINARIES', '/usr/bin/ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_BINARIES', '/usr/bin/ffprobe'),
            ]);

            $video = $ffmpeg->open($localPath);
            $duration = $video->getDurationInSeconds();

            // Generate poster image
            $this->generatePoster($video, $localPath);

            // Transcode to HLS with multiple bitrates
            $this->transcodeToHLS($video, $localPath);

            // Update video record
            $this->video->update([
                'duration_seconds' => (int) $duration,
                'status' => 'ready',
                'metadata' => [
                    'codec' => 'h264',
                    'bitrates' => ['800k', '2000k', '3500k'],
                ],
            ]);

            // Update lesson duration
            $this->video->lesson->update([
                'video_duration_seconds' => (int) $duration,
            ]);

            // Fire event
            event(new \App\Events\VideoTranscodingCompleted($this->video));
        } catch (Exception $e) {
            $this->video->update([
                'status' => 'failed',
                'metadata' => ['error' => $e->getMessage()],
            ]);
            throw $e;
        }
    }

    protected function generatePoster($video, string $localPath): void
    {
        try {
            $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(5));
            $posterDir = storage_path('app/temp/posters');
            
            if (!is_dir($posterDir)) {
                mkdir($posterDir, 0755, true);
            }

            $posterPath = $posterDir . '/' . $this->video->id . '_poster.jpg';
            $frame->save($posterPath);

            // Upload to S3
            $s3Path = 'posters/' . $this->video->lesson->section->course->slug . '/' . $this->video->id . '.jpg';
            Storage::disk('s3')->put(
                $s3Path,
                file_get_contents($posterPath),
                ['visibility' => 'public']
            );

            $this->video->update([
                'poster_path' => Storage::disk('s3')->url($s3Path),
            ]);

            unlink($posterPath);
        } catch (Exception $e) {
            // Log error but don't fail job
            \Log::error('Failed to generate poster: ' . $e->getMessage());
        }
    }

    protected function transcodeToHLS($video, string $localPath): void
    {
        $outputDir = storage_path('app/temp/hls/' . $this->video->id);
        
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Use FFMpeg to create HLS variants (simplified - use full ffmpeg command in production)
        $formats = [
            ['bitrate' => '800k', 'scale' => '854:-2'],
            ['bitrate' => '2000k', 'scale' => '1280:-2'],
            ['bitrate' => '3500k', 'scale' => '1920:-2'],
        ];

        foreach ($formats as $format) {
            $this->transcodeVariant(
                $video,
                $outputDir,
                $format['bitrate'],
                $format['scale']
            );
        }

        // Create master playlist
        $this->createMasterPlaylist($outputDir);

        // Upload to S3
        $this->uploadHLSToS3($outputDir);

        // Clean up local files
        $this->cleanupTempFiles($outputDir);
    }

    protected function transcodeVariant($video, string $outputDir, string $bitrate, string $scale): void
    {
        $format = new \FFMpeg\Format\Video\X264('libmp3lame');
        $format->setKiloBitrate(intval($bitrate));
        $format->setAudioChannels(2);

        $outputPath = $outputDir . '/' . str_replace('k', '', $bitrate) . '.mp4';
        $video->save($format, $outputPath);
    }

    protected function createMasterPlaylist(string $outputDir): void
    {
        $playlist = <<<'M3U8'
#EXTM3U
#EXT-X-VERSION:3
#EXT-X-STREAM-INF:BANDWIDTH=800000,RESOLUTION=854x480
800k.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=2000000,RESOLUTION=1280x720
2000k.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=3500000,RESOLUTION=1920x1080
3500k.m3u8
M3U8;

        file_put_contents($outputDir . '/master.m3u8', $playlist);
    }

    protected function uploadHLSToS3(string $outputDir): void
    {
        $course = $this->video->lesson->section->course;
        $s3Path = 'videos/' . $course->slug . '/' . $this->video->lesson->slug . '/' . $this->video->id;

        // Upload all HLS files
        $files = glob($outputDir . '/*.m3u8');
        foreach ($files as $file) {
            $filename = basename($file);
            Storage::disk('s3')->put(
                $s3Path . '/' . $filename,
                file_get_contents($file),
                ['visibility' => 'public', 'CacheControl' => 'max-age=3600']
            );
        }

        // Update video HLS path
        $hlsUrl = Storage::disk('s3')->url($s3Path . '/master.m3u8');
        $this->video->update(['hls_path' => $hlsUrl]);
    }

    protected function cleanupTempFiles(string $outputDir): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($outputDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($outputDir);
    }
}
