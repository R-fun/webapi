<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangToko extends Model
{
    protected $table = 'barang_toko';
    protected $with = ['Toko'];
    protected $appends = ['last_history'];
    protected $fillable = [
        'barang_id',
        'toko_id'
    ];

    public function Barang(){
        return $this->belongsTo(Barang::class,'barang_id');
    }

    public function Toko(){
        return $this->belongsTo(Toko::class,'toko_id');
    }
    
    public function getLastHistoryAttribute() {
        $dataLast =  BarangHistory::where("barang_id", $this->attributes['barang_id'])
                                    ->where("toko_id", $this->attributes['toko_id'])
                                    ->orderBy("created_at","desc")
                                    ->first();
        return $dataLast;
    }
    
    use HasFactory;
}
