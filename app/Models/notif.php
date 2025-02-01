<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notif extends Model
{
    use HasFactory;

    protected $table = 'notifs';

    // Define the primary key
    protected $primaryKey = 'id';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'email',
        'notification',
        'read',
    ];

    protected $guarded = ['id'];

}
