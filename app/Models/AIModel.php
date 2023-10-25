<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIModel extends Model
{
    use HasFactory;

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function is_owner($user_id)
    {
        return $this->user_id == $user_id;
    }

    public function is_public()
    {
        return $this->is_public;
    }

    public function is_listed()
    {
        return $this->is_listed;
    }


    public function dataset()
    {
        return $this->belongsTo(DataSet::class, 'data_set_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'model_users', 'model_id', 'user_id');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'a_i_model_id');
    }

    public function conversationsCount()
    {
        return $this->conversations()->count();
    }

    public function userConversations($user_id)
    {
        return $this->conversations()->where('user_id', $user_id)->get();
    }

    public function userConversationsCount($user_id)
    {
        return $this->conversations()->where('user_id', $user_id)->count();
    }

}
