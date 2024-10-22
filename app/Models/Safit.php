<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Safit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'fecha_rendicion' => 'date',
        'fecha_registro' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fileUpload()
    {
        return $this->belongsTo(FileUpload::class);
    }
}
