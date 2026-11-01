<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuachonNghe extends Model
{
    use HasFactory;

    protected $table = 'luachon_nghe';

    public $timestamps = false;

    protected $fillable = ['cauhoi_nghe_id', 'noidung', 'ladung', 'thutu'];

    protected function casts(): array
    {
        return [
            'ladung' => 'boolean',
            'thutu' => 'integer',
        ];
    }

    public function cauhoiNghe(): BelongsTo
    {
        return $this->belongsTo(CauhoiNghe::class, 'cauhoi_nghe_id');
    }
}
