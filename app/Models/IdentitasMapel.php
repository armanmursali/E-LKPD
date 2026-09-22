<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentitasMapel extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran',
        'materi',
        'satuan_pendidikan',
        'tahun_pelajaran',
        'tahapan_fase',
        'kelas_label',
        'semester',
        'alokasi_waktu',
        'capaian_pembelajaran',
        'alur_tujuan_pembelajaran',
        'tujuan_pembelajaran',
        'indikator_ketercapaian',
        'model_pembelajaran',
    ];

    protected $casts = [
        'tujuan_pembelajaran' => 'array',
        'indikator_ketercapaian' => 'array',
        'model_pembelajaran' => 'array',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
