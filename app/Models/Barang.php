<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Barang extends Model
{
    protected $table = 'barang';
    protected $with = ['Category'];
    protected $appends = ['barang_toko'];
    protected $fillable =[
        'nama_barang',
        'merk',
        'category'
    ];

    public function Category(){
        return $this->belongsTo(Category::class,'category');
    }

    // public function Toko(){
    //     return $this->belongsTo(Toko::class,'toko');
    // }

    public function getBarangTokoAttribute() {
        $dataLast =  BarangToko::where("barang_id", $this->attributes['id'])
                                    ->where("toko_id", Auth::user()->store->id)
                                    ->orderBy("created_at","desc")
                                    ->first();
        return $dataLast;
    }
    use HasFactory;
}
