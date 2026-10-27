<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Danhmuc extends Model
{
    use HasFactory;

    protected $table = 'danhmuc';

    protected $fillable = ['ten', 'mota'];

    public const CREATED_AT = 'ngaytao';

    public const UPDATED_AT = 'ngaycapnhat';

    protected function casts(): array
    {
        return [
            'ngaytao' => 'datetime',
            'ngaycapnhat' => 'datetime',
        ];
    }

    public function cauhoi(): HasMany
    {
        return $this->hasMany(Cauhoi::class, 'danhmuc_id');
    }
}
