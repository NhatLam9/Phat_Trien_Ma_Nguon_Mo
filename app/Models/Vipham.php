<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vipham extends Model
{
    protected $table = 'vipham';

    public $timestamps = false;

    protected $fillable = [
        'lanthi_id',
        'loai',
        'nguon',
        'mota',
        'thoigian',
    ];

    protected $casts = [
        'thoigian' => 'datetime',
    ];

    public function lanThi(): BelongsTo
    {
        return $this->belongsTo(
            Lanthi::class,
            'lanthi_id',
            'id'
        );
    }
}
