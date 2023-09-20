<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_set_name',
        'data_set_path',
        'user_id',
        'data_set_description',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function models()
    {
        return $this->hasMany(AIModel::class, 'data_set_id');
    }

    public function getDatasetPathAttribute($value)
    {
        return asset('storage/' . $value);
    }
}
