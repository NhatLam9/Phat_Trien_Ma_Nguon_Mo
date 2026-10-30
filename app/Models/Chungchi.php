<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chungchi extends Model
{
    protected $table = 'chungchi';

    public $timestamps = false;

    protected $fillable = [
        'nguoidung_id',
        'lanthi_id',
        'ma',
        'tongdiem',
        'literacy',
        'comprehension',
        'conversation',
        'production',
        'trinhdo',
        'ngaycap',
        'ngaytao',
    ];

    protected $casts = [
        'tongdiem' => 'integer',
        'literacy' => 'integer',
        'comprehension' => 'integer',
        'conversation' => 'integer',
        'production' => 'integer',
        'ngaycap' => 'datetime',
        'ngaytao' => 'datetime',
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
