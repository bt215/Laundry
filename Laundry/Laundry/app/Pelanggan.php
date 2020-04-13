<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table="pelanggan";
    protected $tableprimaryKey="id_pelanggan";
    public $timestamps=false;

    protected $fillable = [
        'nama_pelanggan', 'alamat', 'telp'
    ];
}
