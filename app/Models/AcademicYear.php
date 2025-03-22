<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $guarded = [];

    public function ideas()
    {
        return $this->hasMany(Idea::class, 'idea_id');
    }
}
