<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chamdiem extends Model
{
    protected $table = 'chamdiem';

    public $timestamps = false;

    protected $fillable = [
        'traloi_id',
        'nguoicham',
        'loai',
        'nguphap',
        'tuvung',
        'luloat',
        'phatam',
        'lienquan',
        'machlac',
        'nhanxet',
        'tongdiem',
        'ngaycham',
    ];

    protected $casts = [
        'nguphap' => 'decimal:2',
        'tuvung' => 'decimal:2',
        'luloat' => 'decimal:2',
        'phatam' => 'decimal:2',
        'lienquan' => 'decimal:2',
        'machlac' => 'decimal:2',
        'tongdiem' => 'decimal:2',
        'ngaycham' => 'datetime',
    ];

    public function traLoi(): BelongsTo
    {
        return $this->belongsTo(
            Traloi::class,
            'traloi_id',
            'id'
        );
    }
}
