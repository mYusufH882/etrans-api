<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 't_sales';

    protected $fillable = [
        'kode',
        'tgl',
        'cust_id',
        'subtotal',
        'diskon',
        'ongkir',
        'total_bayar'
    ];
}
