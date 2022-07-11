<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
class BarangHistory extends Model
{
    protected $table = 'barang_history';
    protected $with = ['User','Satuan'];
    protected $fillable =[
        'barang_id',
        'harga_barang',
        'angkasatuan',
        'satuan',
        'operation',
        'keterangan',
        'user_id',
        'json_data',
        'harga_sebelumnya',
        'toko_id',
    ];

    public function Barang(){
        return $this->belongsTo(Barang::class,'barang_id');
    }

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function Satuan(){
        return $this->belongsTo(Satuan::class,'satuan');
    }
    
    public function Toko(){
        return $this->belongsTo(Toko::class,'toko_id');
    }
    use HasFactory;
}
