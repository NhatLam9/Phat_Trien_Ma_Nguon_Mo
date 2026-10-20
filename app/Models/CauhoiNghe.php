<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CauhoiNghe extends Model
{
    use HasFactory;

    protected $table = 'cauhoi_nghe';

    public $timestamps = false;

    protected $fillable = [
        'cauhoi_id',
        'loai',
        'noidung',
        'dapandung',
        'giaithich',
        'thutu',
    ];

    protected function casts(): array
    {
        return [
            'thutu' => 'integer',
        ];
    }

    public function cauhoi(): BelongsTo
    {
        return $this->belongsTo(Cauhoi::class, 'cauhoi_id');
    }

    public function luachonNghe(): HasMany
    {
        return $this->hasMany(LuachonNghe::class, 'cauhoi_nghe_id')->orderBy('thutu');
    }
}
