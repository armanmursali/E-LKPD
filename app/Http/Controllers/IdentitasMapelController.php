<?php

namespace App\Http\Controllers;

use App\Models\IdentitasMapel;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IdentitasMapelController extends Controller
{
    public const ICON_KEYS = ['search', 'clipboard-list', 'users', 'presentation', 'search-check'];

    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $identitas = $this->firstOrCreateFor($kelas);

        return Inertia::render('identitas-mapel/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'identitasMapel' => $identitas->only([
                'mata_pelajaran', 'materi', 'satuan_pendidikan', 'tahun_pelajaran',
                'tahapan_fase', 'kelas_label', 'semester', 'alokasi_waktu',
                'capaian_pembelajaran', 'alur_tujuan_pembelajaran',
                'tujuan_pembelajaran', 'indikator_ketercapaian', 'model_pembelajaran',
            ]),
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $identitas = $this->firstOrCreateFor($kelas);

        $validated = $request->validate([
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'materi' => ['required', 'string', 'max:255'],
            'satuan_pendidikan' => ['required', 'string', 'max:255'],
            'tahun_pelajaran' => ['required', 'string', 'max:255'],
            'tahapan_fase' => ['required', 'string', 'max:255'],
            'kelas_label' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:255'],
            'alokasi_waktu' => ['required', 'string', 'max:255'],
            'capaian_pembelajaran' => ['required', 'string'],
            'alur_tujuan_pembelajaran' => ['required', 'string'],
            'tujuan_pembelajaran' => ['required', 'string'],
            'indikator_ketercapaian' => ['required', 'string'],
            'model_pembelajaran' => ['required', 'array', 'min:1'],
            'model_pembelajaran.*.icon' => ['required', 'string', 'in:'.implode(',', self::ICON_KEYS)],
            'model_pembelajaran.*.teks' => ['required', 'string'],
        ]);

        $identitas->update([
            ...collect($validated)->except(['tujuan_pembelajaran', 'indikator_ketercapaian', 'model_pembelajaran'])->all(),
            'tujuan_pembelajaran' => $this->splitListHtml($validated['tujuan_pembelajaran']),
            'indikator_ketercapaian' => $this->splitListHtml($validated['indikator_ketercapaian']),
            'model_pembelajaran' => collect($validated['model_pembelajaran'])
                ->values()
                ->map(fn ($item, $index) => [
                    'nomor' => $index + 1,
                    'icon' => $item['icon'],
                    'teks' => $item['teks'],
                ])
                ->all(),
        ]);

        return redirect()->back();
    }

    protected function splitListHtml(string $value): array
    {
        $value = preg_replace('/<\/?(p|br|div|span)>/i', '', $value);
        preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $value, $matches);

        if (!empty($matches[1])) {
            return array_values(array_filter(array_map(function (string $item) {
                $item = strip_tags($item);
                return trim($item);
            }, $matches[1])));
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value) ?: [])));
    }

    protected function firstOrCreateFor(Kelas $kelas): IdentitasMapel
    {
        return IdentitasMapel::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'mata_pelajaran' => 'update',
                'materi' => 'update',
                'satuan_pendidikan' => 'update',
                'tahun_pelajaran' => 'update',
                'tahapan_fase' => 'update',
                'kelas_label' => 'update',
                'semester' => 'update',
                'alokasi_waktu' => 'update',
                'capaian_pembelajaran' => 'update',
                'alur_tujuan_pembelajaran' => 'update',
                'tujuan_pembelajaran' => [
                    'update',
                    'update',
                    'update',
                ],
                'indikator_ketercapaian' => [
                    'update',
                    'update',
                    'update',
                ],
                'model_pembelajaran' => [
                    ['nomor' => 1, 'icon' => 'search', 'teks' => 'update'],
                    ['nomor' => 2, 'icon' => 'clipboard-list', 'teks' => 'update'],
                    ['nomor' => 3, 'icon' => 'users', 'teks' => 'update'],
                    ['nomor' => 4, 'icon' => 'presentation', 'teks' => 'update'],
                    ['nomor' => 5, 'icon' => 'search-check', 'teks' => 'update'],
                ],
            ]
        );
    }
}
