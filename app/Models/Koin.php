<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Koin extends Model
{
    use HasFactory;

    protected $table = 'koin';
    //protected $primaryKey = 'koin_id';

    protected $fillable = [
        'koin_id',
        'name',
        'currency',
        'image',
        'fee',
        'ticker',
    ];
}
