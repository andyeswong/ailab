<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelComment extends Model
{
    use HasFactory;

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function model()
    {
        return $this->belongsTo(AIModel::class, 'model_id');
    }

    public function replies()
    {
        return $this->hasMany(ModelComment::class, 'parent_id');
    }

    public function repliesCount()
    {
        return $this->replies()->count();
    }

    public function userReplies($user_id)
    {
        return $this->replies()->where('user_id', $user_id)->get();
    }

    public function userRepliesCount($user_id)
    {
        return $this->replies()->where('user_id', $user_id)->count();
    }

    public function parent()
    {
        return $this->belongsTo(ModelComment::class, 'parent_id');
    }

    public function parentAuthor()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function isReply()
    {
        return $this->parent_id !== null;
    }
}
