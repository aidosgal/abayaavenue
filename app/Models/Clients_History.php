<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients_History extends Model
{
    use HasFactory;

    protected $table = 'clients__histories';

    protected $fillable = [
        'client_id',
        'product_history_id',
    ];
}
