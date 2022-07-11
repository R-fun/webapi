<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';
    protected $with = ['JenisToko'];
    protected $fillable = [
        'nama_toko',
        'alamat',
        'user_id',
        'jenis_toko',
    ];

    public function JenisToko(){
        return $this->belongsTo(JenisToko::class,'jenis_toko');
    }

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }

    use HasFactory;
}
