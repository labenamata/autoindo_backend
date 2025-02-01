<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jaring extends Model
{
    use HasFactory;

    // Define the custom table name
    protected $table = 'jaring';

    // Define the primary key
    protected $primaryKey = 'id';

    // Use fillable for mass assignment
    protected $fillable = [
        'email',
        'koin_id',
        'modal',
        'buy',
        'sell',
        'profit',
        'status',
        'order_id',
    ];

    // Guard unnecessary attributes
    protected $guarded = ['id'];

    public function pairs()
    {
        return $this->belongsTo(Koin::class, 'koin_id', 'ticker');
    }
}
