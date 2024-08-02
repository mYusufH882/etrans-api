<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function customer() 
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

    public function details()
    {
        return $this->hasMany(Sales_Det::class, 'sales_id');
    }

    public static function generateCode($tgl)
    {
        $yearMonth = Carbon::parse($tgl)->format('Ym');
        $latestSale = self::whereYear('tgl', Carbon::parse($tgl)->year)
                            ->whereMonth('tgl', Carbon::parse($tgl)->month)
                            ->orderBy('kode', 'desc')
                            ->first();

        if(!$latestSale) {
            $newNumber = '0001';
        } else {
            $lastNumber = intval(substr($latestSale->kode, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $yearMonth . '-' . $newNumber;
    }
}
