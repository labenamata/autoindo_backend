<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kredential extends Model
{
    use HasFactory;

    // Define the custom table name
    protected $table = 'kredential';

    // Define the primary key
    protected $primaryKey = 'id';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'email',
        'key',
        'secret',
    ];

    // Guard unnecessary attributes
    protected $guarded = [];

    public function state()
    {
        return $this->belongsTo(Bstate::class, 'email', 'email');
    }

    public function jarings()
    {
        return $this->hasMany(jaring::class, 'email', 'email');
    }
}
