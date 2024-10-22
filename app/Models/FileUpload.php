<?php

namespace App\Models;

use App\Enums\FileTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => FileTypeEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function safits()
    {
        return $this->hasMany(Safit::class);
    }

    public function cobranzas()
    {
        return $this->hasMany(Cobranza::class);
    }

    public function rendicions()
    {
        return $this->hasMany(Rendicion::class);
    }

}
