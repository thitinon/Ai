<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ReviewHelpful extends Pivot
{
    protected $table = 'review_helpful';

    protected $fillable = ['user_id','review_id'];
}
