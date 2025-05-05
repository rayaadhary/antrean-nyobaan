<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_antrian',
        'status',
        'loket_id'
    ];

    public function loket(){
        return $this->belongsTo(Loket::class);
    }
}
