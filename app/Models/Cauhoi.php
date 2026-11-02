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

    public const TRANGTHAI_DRAFT = 'draft';

    public const TRANGTHAI_REVIEW = 'review';

    public const TRANGTHAI_PUBLISHED = 'published';

    public const TRANGTHAI_ARCHIVED = 'archived';

    /**
     * @var array<int, string>
     */
    public const TRANGTHAI = [
        self::TRANGTHAI_DRAFT,
        self::TRANGTHAI_REVIEW,
        self::TRANGTHAI_PUBLISHED,
        self::TRANGTHAI_ARCHIVED,
    ];

    /**
     * Nhan hien thi cho tung trang thai.
     *
     * @var array<string, string>
     */
    public const TRANGTHAI_NHAN = [
        self::TRANGTHAI_DRAFT => 'Bản nháp',
        self::TRANGTHAI_REVIEW => 'Chờ duyệt',
        self::TRANGTHAI_PUBLISHED => 'Đã xuất bản',
        self::TRANGTHAI_ARCHIVED => 'Lưu trữ',
    ];

    protected $fillable = [
        'ma',
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

    protected static function booted(): void
    {
        // Moi cau hoi phai co ma on dinh. Neu nguoi tao khong truyen ma
        // (seeder, du lieu cu), sinh ma theo id sau khi insert.
        static::created(function (self $cauhoi): void {
            if ($cauhoi->ma === null || $cauhoi->ma === '') {
                $cauhoi->forceFill([
                    'ma' => 'q-'.str_pad((string) $cauhoi->getKey(), 6, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function loai(): BelongsTo
    {
        return $this->belongsTo(Loaicauhoi::class, 'loai_id');
    }

    public function danhmuc(): BelongsTo
    {
        return $this->belongsTo(Danhmuc::class, 'danhmuc_id');
    }

    public function nguoiTao(): BelongsTo
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
        return $query->where('trangthai', self::TRANGTHAI_PUBLISHED);
    }

    public function scopeTrangThai(Builder $query, ?string $trangthai): Builder
    {
        if ($trangthai === null || ! in_array($trangthai, self::TRANGTHAI, true)) {
            return $query;
        }

        return $query->where('trangthai', $trangthai);
    }

    public function scopeLoaiCauHoi(Builder $query, int|string|null $loaiId): Builder
    {
        if ($loaiId === null || $loaiId === '' || (int) $loaiId <= 0) {
            return $query;
        }

        return $query->where('loai_id', (int) $loaiId);
    }

    public function scopeKyNang(Builder $query, ?string $kynang): Builder
    {
        if ($kynang === null || ! in_array($kynang, ['reading', 'listening', 'writing', 'speaking'], true)) {
            return $query;
        }

        return $query->whereHas('loai', fn (Builder $q) => $q->where('kynang', $kynang));
    }

    public function scopeDoKho(Builder $query, int|string|null $dokho): Builder
    {
        $dokho = (int) $dokho;

        if ($dokho < 1 || $dokho > 10) {
            return $query;
        }

        return $query->where('dokho', $dokho);
    }

    public function scopeTimKiem(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $query;
        }

        $like = '%'.$keyword.'%';

        return $query->where(function (Builder $q) use ($like): void {
            $q->where('ma', 'like', $like)
                ->orWhere('noidung', 'like', $like)
                ->orWhere('giaithich', 'like', $like);
        });
    }

    public function nhanTrangThai(): string
    {
        return self::TRANGTHAI_NHAN[$this->trangthai] ?? (string) $this->trangthai;
    }

    public function soDapAnDung(): int
    {
        return $this->luachon->where('ladung', true)->count();
    }

    public function laTracNghiemTuDong(): bool
    {
        return in_array($this->loai?->ma, [
            'read_and_select',
            'fill_in_the_blanks',
            'read_and_complete',
        ], true);
    }
}
