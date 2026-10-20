<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dethi extends Model
{
    use HasFactory;

    protected $table = 'dethi';

    protected $fillable = [
        'ten',
        'mota',
        'chedo',
        'kieu',
        'kynang',
        'thoigian',
        'tongdiem',
        'trangthai',
        'nguoitao',
    ];

    public const CREATED_AT = 'ngaytao';

    public const UPDATED_AT = 'ngaycapnhat';

    protected function casts(): array
    {
        return [
            'thoigian' => 'integer',
            'tongdiem' => 'decimal:2',
            'ngaytao' => 'datetime',
            'ngaycapnhat' => 'datetime',
        ];
    }

    public function nguoitao(): BelongsTo
    {
        return $this->belongsTo(Nguoidung::class, 'nguoitao');
    }

    public function dethicauhoi(): HasMany
    {
        return $this->hasMany(Dethicauhoi::class, 'dethi_id')->orderBy('thutu');
    }

    public function cauhoi(): BelongsToMany
    {
        return $this->belongsToMany(Cauhoi::class, 'dethicauhoi')
            ->withPivot(['thutu', 'diem'])
            ->orderByPivot('thutu');
    }

    public function scopePublished($query)
    {
        return $query->where('trangthai', 'published');
    }
}
