<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class posts extends Model
{
    /**
     * fillable
     * 
     * @var array
     * 
     */
    protected $fillable = [
        'Foto',
        'Judul',
        'Deskripsi'
    ];
}
