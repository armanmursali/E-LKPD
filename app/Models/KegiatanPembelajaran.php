<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanPembelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
