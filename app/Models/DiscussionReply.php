<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionReply extends Model
{
    protected $fillable = [
        'discussion_id','user_id','content','is_instructor','is_accepted_answer'
    ];

    protected $casts = [
        'is_instructor' => 'boolean',
        'is_accepted_answer' => 'boolean',
    ];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
