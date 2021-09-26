<?php

namespace App\Model\Inventory;

use App\Model\Inventory\Kartugudang\Kartugudang;
use App\Model\Inventory\Purchase\PO;
use App\Model\Inventory\Purchase\POdetail;
use App\Model\Inventory\Stockopname\Opname;
use App\Model\SingleSignOn\JenisBengkel;
use App\Scopes\OwnershipScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sparepart extends Model
{
    protected $table = "tb_inventory_master_sparepart";

    protected $primaryKey = 'id_sparepart';
    
    protected $fillable = [
        'id_merk',
        'id_jenis_bengkel',
        'id_jenis_sparepart',
        'id_konversi',
        'id_kemasan',
        'kode_sparepart',
        'nama_sparepart',
        'status_sparepart',
        'lifetime',
        'jenis_barang',
        'dimensi_berat'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = true;

    public function Jenissparepart()
    {
        return $this->belongsTo(Jenissparepart::class, 'id_jenis_sparepart', 'id_jenis_sparepart')->withTrashed();
    }

    public $with = ['Merksparepart', 'Jenissparepart', 'Kemasan','Konversi','Detailsparepart','JenisBengkel'];
    public function Merksparepart()
    {
        return $this->belongsTo(Merksparepart::class, 'id_merk', 'id_merk')->withTrashed();
    }

    public function Konversi()
    {
        return $this->belongsTo(Konversi::class, 'id_konversi', 'id_konversi')->withTrashed();
    }

    public function Kemasan()
    {
        return $this->belongsTo(Kemasan::class, 'id_kemasan', 'id_kemasan');
    }

    public function Detailsparepart()
    {
        return $this->belongsTo(DetailSparepart::class, 'id_sparepart', 'id_sparepart');
    }

    public function JenisBengkel()
    {
        return $this->belongsTo(JenisBengkel::class, 'id_jenis_bengkel', 'id_jenis_bengkel');
    }

    public static function getId()
    {
        $getId = DB::table('tb_inventory_master_sparepart')->orderBy('id_sparepart', 'DESC')->take(1)->get();
        if (count($getId) > 0) return $getId;
        return (object)[
            (object)[
                'id_sparepart' => 0
            ]
        ];
    }
}
