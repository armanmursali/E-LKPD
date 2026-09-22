<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LkpdKonten extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'nomor',
        'blocks',
        'pengaturan',
        'aktif',
    ];

    protected $casts = [
        'blocks' => 'array',
        'pengaturan' => 'array',
        'aktif' => 'boolean',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
