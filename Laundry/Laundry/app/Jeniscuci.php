<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jeniscuci extends Model
{
    protected $table="jenis_cuci";
    protected $tableprimaryKey="id_jenis_cuci";
    public $timestamps=false;

    protected $fillable = [
        'nama_jenis', 'harga_per_kilo'
    ];
    
}
