<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LkpdSubmission extends Model
{
    use HasFactory;

    protected $fillable = ['kelas_id', 'kegiatan_nomor', 'nama_peserta', 'nama_kelompok', 'tipe_peserta', 'jawaban', 'nilai'];

    protected $casts = ['jawaban' => 'array', 'nilai' => 'integer'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}