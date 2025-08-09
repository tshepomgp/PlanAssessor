<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    


    protected $fillable = ['user_id', 'file_path', 'status', 'assessment', 'client_slug', 'client_name'];

    protected $casts = [
        'assessment' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
