<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['user_id','course_id','certificate_number','issued_at','pdf_url','metadata'];

    protected $casts = [
        'issued_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public static function generateNumber(): string
    {
        return 'CERT-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
}
