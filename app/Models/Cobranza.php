<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cobranza extends Model
{
    use HasFactory;
    public const BATCH_SIZE = 100;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fileUpload()
    {
        return $this->belongsTo(FileUpload::class);
    }
}
