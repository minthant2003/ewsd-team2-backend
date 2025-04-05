<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = [];

    // one to many relation
    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
