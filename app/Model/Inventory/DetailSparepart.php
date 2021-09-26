<?php

namespace App\Model\Inventory;

use App\Model\Inventory\Kartugudang\Kartugudang;
use App\Scopes\OwnershipScope;
use Illuminate\Database\Eloquent\Model;

class DetailSparepart extends Model
{
    protected $table = "tb_inventory_detail_sparepart";

    protected $primaryKey = 'id_detail_sparepart';

    protected $fillable = [
    	'id_sparepart',
        'id_gudang',
        'qty_stok',
        'stok_min',
        'status_jumlah',
        'harga_market',
        'keterangan'
    ];

    protected $hidden =[ 

    ];

    public $timestamps = false;

    public function Sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'id_sparepart', 'id_sparepart');
    }

    public function Gudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang', 'id_gudang');
    }

    public function Kartugudang()
    {
        return $this->hasMany(Kartugudang::class, 'id_detail_sparepart', 'id_detail_sparepart');
    }

    public function Kartugudangsaldoakhir()
    {
        return $this->hasOne(Kartugudang::class, 'id_detail_sparepart', 'id_detail_sparepart')->orderBy('updated_at', 'DESC');;
    }

    public function Kartugudangterakhir()
    {
        return $this->hasOne(Kartugudang::class, 'id_detail_sparepart', 'id_detail_sparepart')->where('jenis_kartu', 'Receiving')->orderBy('updated_at', 'DESC');
    }

    public function Kartugudangservice()
    {
        return $this->hasOne(Kartugudang::class, 'id_detail_sparepart', 'id_detail_sparepart')->where('jenis_kartu', 'Service')->orderBy('updated_at', 'DESC');
    }

    public function Kartugudangpenjualan()
    {
        return $this->hasOne(Kartugudang::class, 'id_detail_sparepart', 'id_detail_sparepart')->where('jenis_kartu', 'Penjualan')->orderBy('updated_at', 'DESC');
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnershipScope);
    }
}
