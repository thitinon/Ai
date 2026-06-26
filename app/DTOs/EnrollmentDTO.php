<?php

namespace App\DTOs;

class EnrollmentDTO
{
    public function __construct(
        public int $userId,
        public int $courseId,
        public ?int $paymentId = null,
        public float $progressPercent = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            courseId: $data['course_id'],
            paymentId: $data['payment_id'] ?? null,
            progressPercent: (float) ($data['progress_percent'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'course_id' => $this->courseId,
            'payment_id' => $this->paymentId,
            'progress_percent' => $this->progressPercent,
            'enrolled_at' => now(),
        ];
    }
}
