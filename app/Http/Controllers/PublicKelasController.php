<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KegiatanPembelajaran;
use App\Models\Pendahuluan;
use App\Models\LkpdKonten;
use App\Models\DeskripsiLkpd;
use App\Models\PetunjukLkpd;
use App\Models\DaftarPustaka;
use App\Models\ProfilPenulis;
use App\Models\Game;
use Inertia\Inertia;

class PublicKelasController extends Controller
{
    public function show(string $token, ?string $section = null)
    {
        $kelas = Kelas::where('public_token', $token)->firstOrFail();

        if ($section === 'game') {
            return $this->game($token);
        }

        if ($section === 'pendahuluan') {
            $pendahuluan = Pendahuluan::where('kelas_id', $kelas->id)->first();

            return Inertia::render('public/kelas/Pendahuluan', [
                'kelas' => $kelas->only(['id', 'nama', 'deskripsi', 'public_token']),
                'pendahuluan' => $pendahuluan ? [
                    'sampul' => [
                        ...$pendahuluan->only(['judul', 'mapel', 'jenjang', 'kelas_label', 'fase', 'kurikulum', 'penulis', 'validator_media', 'validator_materi']),
                        'pembimbing' => $this->pembimbing($pendahuluan->pembimbing),
                    ],
                    'kata_pengantar' => $pendahuluan->kata_pengantar ?? [],
                ] : null,
            ]);
        }

        if ($section === 'kegiatan-pembelajaran') {
            $kegiatan = KegiatanPembelajaran::where('kelas_id', $kelas->id)->first();
            $identitas = $kelas->identitasMapel;
            $inactiveNumbers = LkpdKonten::where('kelas_id', $kelas->id)
                ->where('aktif', false)
                ->pluck('nomor')
                ->map(fn ($nomor) => (int) $nomor)
                ->all();

            return Inertia::render('public/kelas/KegiatanPembelajaran', [
                'kelas' => $kelas->only(['id', 'nama', 'deskripsi', 'public_token']),
                'tujuanPembelajaran' => $identitas?->tujuan_pembelajaran ?? [],
                'kegiatanPembelajaran' => ['items' => collect($kegiatan?->items ?? [])->reject(fn ($item) => in_array((int) ($item['id'] ?? 0), $inactiveNumbers, true))->values()->all()],
            ]);
        }

        if (in_array($section, ['deskripsi-lkpd', 'petunjuk-lkpd', 'identitas-mapel', 'evaluasi-pembelajaran', 'daftar-pustaka', 'profil-penulis'], true)) {
            return $this->renderPublicSection($kelas, $section);
        }

        return Inertia::render('public/kelas/Show', [
            'kelas' => [
                ...$kelas->only(['id', 'nama', 'deskripsi', 'public_token']),
                'public_hero_image' => $kelas->public_hero_image
                    ? (str_starts_with($kelas->public_hero_image, 'http')
                        ? $kelas->public_hero_image
                        : rtrim((string) config('filesystems.disks.public.url'), '/').'/'.ltrim($kelas->public_hero_image, '/'))
                    : null,
                'pemilik' => $kelas->user?->name ?? 'Guru',
                'mata_pelajaran' => $kelas->identitasMapel?->mata_pelajaran ?? 'Mata Pelajaran',
            ],
            'section' => $section,
        ]);
    }

    public function game(string $token)
    {
        $kelas = Kelas::where('public_token', $token)->firstOrFail();

        return Inertia::render('public/kelas/Game', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi', 'public_token']),
            'games' => Game::where('kelas_id', $kelas->id)->latest()->get(),
        ]);
    }

    private function renderPublicSection(Kelas $kelas, string $section)
    {
        $base = ['kelas' => $kelas->only(['id', 'nama', 'deskripsi', 'public_token'])];

        return match ($section) {
            'deskripsi-lkpd' => Inertia::render('public/kelas/DeskripsiLkpd', [
                ...$base,
                'paragraf' => DeskripsiLkpd::where('kelas_id', $kelas->id)->first()?->paragraf ?? [],
            ]),
            'petunjuk-lkpd' => Inertia::render('public/kelas/PetunjukLkpd', [
                ...$base,
                'items' => PetunjukLkpd::where('kelas_id', $kelas->id)->first()?->items ?? [],
            ]),
            'identitas-mapel' => Inertia::render('public/kelas/IdentitasMapel', [
                ...$base,
                'identitasMapel' => $kelas->identitasMapel?->only([
                    'mata_pelajaran', 'materi', 'satuan_pendidikan', 'tahun_pelajaran',
                    'tahapan_fase', 'kelas_label', 'semester', 'alokasi_waktu',
                    'capaian_pembelajaran', 'alur_tujuan_pembelajaran',
                    'tujuan_pembelajaran', 'indikator_ketercapaian', 'model_pembelajaran',
                ]),
            ]),
            'evaluasi-pembelajaran' => Inertia::render('public/kelas/EvaluasiPembelajaran', [
                ...$base,
                'evaluasi' => $kelas->evaluasiPembelajaran()->latest()->get(),
            ]),
            'daftar-pustaka' => Inertia::render('public/kelas/DaftarPustaka', [
                ...$base,
                'items' => DaftarPustaka::where('kelas_id', $kelas->id)->first()?->items ?? [],
            ]),
            'profil-penulis' => Inertia::render('public/kelas/ProfilPenulis', [
                ...$base,
                'profilPenulis' => ($profil = ProfilPenulis::where('kelas_id', $kelas->id)->first()) ? [
                    'paragraf' => $profil->paragraf,
                    'gambar' => $profil->gambar ? rtrim((string) config('filesystems.disks.public.url'), '/').'/'.ltrim($profil->gambar, '/') : null,
                ] : ['paragraf' => '', 'gambar' => null],
            ]),
        };
    }

    public function activity(string $token, int $nomor)
    {
        $kelas = Kelas::where('public_token', $token)->firstOrFail();
        $kegiatan = KegiatanPembelajaran::where('kelas_id', $kelas->id)->first();
        $item = collect($kegiatan?->items ?? [])->firstWhere('id', $nomor);
        abort_unless($item, 404);

        $konten = LkpdKonten::where('kelas_id', $kelas->id)->where('nomor', $nomor)->first();
        abort_unless(!$konten || $konten->aktif, 404);
        $identitas = $kelas->identitasMapel;

        return Inertia::render('public/kelas/Activity', [
            'kelas' => $kelas->only(['id', 'nama', 'public_token']),
            'kegiatan' => $item,
            'blocks' => $konten?->blocks ?? [],
            'pengaturan' => $konten?->pengaturan ?? [],
            'identitasMapel' => $identitas?->only([
                'mata_pelajaran', 'materi', 'satuan_pendidikan', 'tahun_pelajaran',
                'tahapan_fase', 'kelas_label', 'semester', 'alokasi_waktu',
                'capaian_pembelajaran', 'alur_tujuan_pembelajaran',
                'tujuan_pembelajaran', 'indikator_ketercapaian', 'model_pembelajaran',
            ]) ?? [],
        ]);
    }

    private function pembimbing(?string $value): array
    {
        $decoded = json_decode((string) $value, true);

        return array_values(array_filter(is_array($decoded) ? $decoded : [$value]));
    }
}