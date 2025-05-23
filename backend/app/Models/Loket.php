<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'aktif'
    ];

    public function antrians(){
        return $this->hasMany(Antrian::class);
    }
}
