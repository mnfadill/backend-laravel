<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KunjunganPasien extends Model
{
    use HasFactory;

    protected $fillable = [
        'poli_id',
        'tanggal_kunjungan',
        'jenis_kunjungan'
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'datetime',
    ];

    /**
     * Relasi ke poli
     */
    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    /**
     * Scope untuk kunjungan hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_kunjungan', today());
    }

    /**
     * Scope untuk kunjungan bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal_kunjungan', now()->month)
                     ->whereYear('tanggal_kunjungan', now()->year);
    }

    /**
     * Scope untuk jenis kunjungan
     */
    public function scopeJenisKunjungan($query, $jenis)
    {
        return $query->where('jenis_kunjungan', $jenis);
    }
}

