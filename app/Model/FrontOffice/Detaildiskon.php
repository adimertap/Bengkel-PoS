<?php

namespace App\Model\FrontOffice;

use App\Model\Inventory\Jenissparepart;
use App\Scopes\OwnershipScope;
use Illuminate\Database\Eloquent\Model;

class Detaildiskon extends Model
{
    protected $table = "tb_fo_detail_diskon";

    protected $primaryKey = 'id_detail_diskon';

    protected $fillable = [
        'id_jenis_sparepart',
        'id_diskon',
    ];

    protected $hidden =[ 
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function Jenissparepart()
    {
        return $this->belongsTo(Jenissparepart::class, 'id_jenis_sparepart','id_jenis_sparepart');
    }

    public function Diskon()
    {
        return $this->belongsTo(Diskon::class, 'id_diskon','id_diskon');
    }

}
