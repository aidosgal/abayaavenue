<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products_Files extends Model
{
    use HasFactory;

    protected $table = 'products__files';

    protected $fillable = [
        'product_id',
        'file_path',
        'file_name'
    ];
}
