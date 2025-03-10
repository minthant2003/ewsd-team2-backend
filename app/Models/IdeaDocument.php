<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeaDocument extends Model
{
    protected $guarded = [];

    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }
}
