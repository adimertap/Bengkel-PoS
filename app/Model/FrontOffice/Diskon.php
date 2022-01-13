<?php

namespace App\Model\FrontOffice;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    protected $table = "tb_fo_master_diskon";

    protected $primaryKey = 'id_diskon';

    protected $fillable = [
        'id_bengkel', 'nama_diskon', 'jumlah_diskon', 'kode_diskon','status_diskon'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;

    public function Detail(){
        return $this->belongsToMany(Jenissparepart::class,'tb_fo_detail_diskon','id_diskon','id_jenis_sparepart');
    }

    public static function getId()
    {
        return $getId = DB::table('tb_fo_master_diskon')->orderBy('id_diskon', 'DESC')->take(1)->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnershipScope);
    }
}
