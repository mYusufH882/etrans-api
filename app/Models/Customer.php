<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'm_customer';

    protected $fillable = [
        'kode',
        'name',
        'telp'
    ];
}
