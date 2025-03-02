<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $guarded = [];

    // one to many relation
    public function ideas()
    {
        return $this->hasMany(Ideas::class, 'idea_id');
    }
}
