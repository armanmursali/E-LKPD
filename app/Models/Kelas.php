<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kelas extends Model
{
    /** @use HasFactory<\Database\Factories\KelasFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'deskripsi',
        'public_hero_image',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $kelas) {
            $kelas->public_token ??= (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pendahuluan(): HasOne
    {
        return $this->hasOne(Pendahuluan::class);
    }

    public function deskripsiLkpd(): HasOne
    {
        return $this->hasOne(DeskripsiLkpd::class);
    }

    public function petunjukLkpd(): HasOne
    {
        return $this->hasOne(PetunjukLkpd::class);
    }

    public function identitasMapel(): HasOne
    {
        return $this->hasOne(IdentitasMapel::class);
    }

    public function kegiatanPembelajaran(): HasOne
    {
        return $this->hasOne(KegiatanPembelajaran::class);
    }

    public function evaluasiPembelajaran(): HasMany
    {
        return $this->hasMany(EvaluasiPembelajaran::class);
    }

    public function daftarPustaka(): HasOne
    {
        return $this->hasOne(DaftarPustaka::class);
    }

    public function profilPenulis(): HasOne
    {
        return $this->hasOne(ProfilPenulis::class);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function lkpdSubmissions(): HasMany
    {
        return $this->hasMany(LkpdSubmission::class);
    }
}
