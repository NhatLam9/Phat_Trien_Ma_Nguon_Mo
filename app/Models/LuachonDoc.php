<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuachonDoc extends Model
{
    use HasFactory;

    protected $table = 'luachon_doc';

    public $timestamps = false;

    protected $fillable = ['cauhoi_doc_id', 'noidung', 'ladung', 'thutu'];

    protected function casts(): array
    {
        return [
            'ladung' => 'boolean',
            'thutu' => 'integer',
        ];
    }

    public function cauhoiDoc(): BelongsTo
    {
        return $this->belongsTo(CauhoiDoc::class, 'cauhoi_doc_id');
    }
}
