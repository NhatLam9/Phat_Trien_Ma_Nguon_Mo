<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lanthicauhoi extends Model
{
    protected $table = 'lanthicauhoi';

    public $timestamps = false;

    protected $fillable = [
        'lanthi_id',
        'cauhoi_id',
        'thutu',
        'dokho_lucra',
        'diemtoida',
        'thoigian_cap',
        'thoigian_tra_loi',
        'datraloi',
    ];

    protected $casts = [
        'dokho_lucra' => 'decimal:2',
        'diemtoida' => 'decimal:2',
        'thoigian_cap' => 'datetime',
        'thoigian_tra_loi' => 'datetime',
        'datraloi' => 'boolean',
    ];

    public function lanThi(): BelongsTo
    {
        return $this->belongsTo(
            Lanthi::class,
            'lanthi_id',
            'id'
        );
    }

    public function traLoi(): HasOne
    {
        return $this->hasOne(
            Traloi::class,
            'lanthicauhoi_id',
            'id'
        );
    }
}