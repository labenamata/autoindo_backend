<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bstate extends Model
{
    protected $table      = 'bstate'; // Custom table name
    protected $primaryKey = 'id';     // Explicit primary key

    protected $fillable = [
        'email', 'state', // Mass-assignable attributes
    ];

    public function kredentials()
    {
        return $this->hasOne(Kredential::class, 'email', 'email');
    }
}
