<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idea extends Model
{
    protected $guarded = [];

    public function ideaDocuments()
    {
        return $this->hasMany(IdeaDocument::class);
    }
        
    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
