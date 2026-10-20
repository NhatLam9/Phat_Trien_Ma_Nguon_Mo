<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cauhoi extends Model
{
    use HasFactory;

    protected $table = 'cauhoi';

    protected $fillable = [
        'loai_id',
        'danhmuc_id',
        'noidung',
        'giaithich',
        'dokho',
        'anh',
        'audio',
        'thoigian',
        'diem',
        'trangthai',
        'nguoitao',
    ];

    public const CREATED_AT = 'ngaytao';

    public const UPDATED_AT = 'ngaycapnhat';

    protected function casts(): array
    {
        return [
            'dokho' => 'integer',
            'thoigian' => 'integer',
            'diem' => 'decimal:2',
            'ngaytao' => 'datetime',
            'ngaycapnhat' => 'datetime',
        ];
    }

    public function loai(): BelongsTo
    {
        return $this->belongsTo(Loaicauhoi::class, 'loai_id');
    }

    public function danhmuc(): BelongsTo
    {
        return $this->belongsTo(Danhmuc::class, 'danhmuc_id');
    }

    public function nguoitao(): BelongsTo
    {
        return $this->belongsTo(Nguoidung::class, 'nguoitao');
    }

    public function luachon(): HasMany
    {
        return $this->hasMany(Luachon::class, 'cauhoi_id')->orderBy('thutu');
    }

    public function dapan(): HasMany
    {
        return $this->hasMany(Dapan::class, 'cauhoi_id')->orderBy('thutu');
    }

    public function cauhoiDoc(): HasMany
    {
        return $this->hasMany(CauhoiDoc::class, 'cauhoi_id')->orderBy('thutu');
    }

    public function cauhoiNghe(): HasMany
    {
        return $this->hasMany(CauhoiNghe::class, 'cauhoi_id')->orderBy('thutu');
    }

    public function dethicauhoi(): HasMany
    {
        return $this->hasMany(Dethicauhoi::class, 'cauhoi_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('trangthai', 'published');
    }
}
