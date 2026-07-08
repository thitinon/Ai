<?php

namespace App\Services\Certificate;

use App\Models\Certificate;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Generate certificate for completed enrollment
     */
    public function generate(Enrollment $enrollment): ?Certificate
    {
        if (!$enrollment->completed_at || $enrollment->progress_percent < 100) {
            return null;
        }

        // Check if certificate already exists
        $existing = Certificate::where('user_id', $enrollment->user_id)
            ->where('course_id', $enrollment->course_id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Generate PDF
        $pdfPath = $this->generatePDF($enrollment);

        // Upload to S3
        $s3Path = $this->uploadToS3($pdfPath, $enrollment);

        // Create certificate record
        $certificate = Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'certificate_number' => Certificate::generateNumber(),
            'issued_at' => now(),
            'pdf_url' => $s3Path,
            'metadata' => [
                'completed_at' => $enrollment->completed_at,
                'progress' => $enrollment->progress_percent,
            ],
        ]);

        // Clean up local file
        unlink($pdfPath);

        return $certificate;
    }

    /**
     * Generate PDF certificate
     */
    protected function generatePDF(Enrollment $enrollment): string
    {
        $html = view('certificates.template', [
            'enrollment' => $enrollment,
            'certificateNumber' => Certificate::generateNumber(),
            'issuedDate' => now()->format('F d, Y'),
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);

        $filename = 'cert_' . uniqid() . '.pdf';
        $path = storage_path('app/temp/' . $filename);

        $pdf->save($path);

        return $path;
    }

    /**
     * Upload certificate to S3
     */
    protected function uploadToS3(string $localPath, Enrollment $enrollment): string
    {
        $s3Path = 'certificates/' . $enrollment->user_id . '/' . $enrollment->course_id . '.pdf';

        Storage::disk('s3')->put(
            $s3Path,
            file_get_contents($localPath),
            ['visibility' => 'private']
        );

        return Storage::disk('s3')->url($s3Path);
    }

    /**
     * Get signed download URL
     */
    public function getDownloadUrl(Certificate $certificate, int $expirationMinutes = 60): string
    {
        return Storage::disk('s3')->temporaryUrl(
            $this->getS3PathFromUrl($certificate->pdf_url),
            now()->addMinutes($expirationMinutes)
        );
    }

    protected function getS3PathFromUrl(string $url): string
    {
        return str_replace(Storage::disk('s3')->url(''), '', $url);
    }
}
