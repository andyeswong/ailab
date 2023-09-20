<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelUpdate extends Model
{
    use HasFactory;

    public function model()
    {
        return $this->belongsTo(AIModel::class, 'model_id');
    }
}
