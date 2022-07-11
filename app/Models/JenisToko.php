<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisToko extends Model
{
    protected $table = 'jenis_toko';
    protected $fillable = [
        'jenis_toko',
    ];
    use HasFactory;
}
