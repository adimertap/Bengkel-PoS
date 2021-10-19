<?php

namespace App\Model\FrontOffice;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    protected $table = "tb_fo_master_diskon";

    protected $primaryKey = 'id_diskon';

    protected $fillable = [
        'nama_diskon',
        'kode_diskon',
        'jumlah_diskon',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
