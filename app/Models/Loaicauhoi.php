<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loaicauhoi extends Model
{
    use HasFactory;

    protected $table = 'loaicauhoi';

    public $timestamps = false;

    protected $fillable = [
        'ma',
        'ten',
        'kynang',
        'cachcham',
        'thoigian',
        'mota',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'thoigian' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function cauhoi(): HasMany
    {
        return $this->hasMany(Cauhoi::class, 'loai_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
