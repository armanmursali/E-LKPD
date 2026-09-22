<?php

namespace App\Http\Controllers;

use App\Models\KegiatanPembelajaran;
use App\Models\Kelas;
use App\Models\LkpdKonten;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KegiatanPembelajaranController extends Controller
{
    public const ICON_KEYS = ['video', 'microscope'];

    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $kegiatan = $this->firstOrCreateFor($kelas);
        $identitasMapel = $kelas->identitasMapel;
        $activeByNumber = LkpdKonten::where('kelas_id', $kelas->id)
            ->pluck('aktif', 'nomor')
            ->mapWithKeys(fn ($aktif, $nomor) => [(int) $nomor => (bool) $aktif]);

        return Inertia::render('kegiatan-pembelajaran/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'tujuanPembelajaran' => $identitasMapel?->tujuan_pembelajaran ?? [],
            'kegiatanPembelajaran' => [
                'items' => collect($kegiatan->items)
                    ->map(fn ($item) => [...$item, 'aktif' => $activeByNumber->get((int) ($item['id'] ?? 0), true)])
                    ->all(),
            ],
        ]);
    }

    public function toggleActive(Request $request, Kelas $kelas, int $item)
    {
        $this->authorize('update', $kelas);

        $validated = $request->validate([
            'aktif' => ['required', 'boolean'],
        ]);

        $konten = LkpdKonten::firstOrCreate(
            ['kelas_id' => $kelas->id, 'nomor' => $item],
            ['blocks' => [], 'aktif' => true],
        );
        $konten->update(['aktif' => $validated['aktif']]);

        return redirect()->back();
    }

    public function store(Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $kegiatan = $this->firstOrCreateFor($kelas);
        $items = collect($kegiatan->items);
        $nextId = ((int) $items->max('id')) + 1;
        $nomor = $items->count() + 1;

        $items->push([
            'id' => $nextId,
            'nomor' => $nomor,
            'icon' => 'video',
            'judul' => "Kegiatan Pembelajaran {$nomor}",
            'bahan_ajar_label' => "Bahan Ajar Tambahan {$nomor}",
        ]);

        $kegiatan->update(['items' => $items->values()->all()]);

        return redirect()->route('kelas.kegiatan-pembelajaran.builder', [$kelas, $nextId]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $kegiatan = $this->firstOrCreateFor($kelas);

        $validated = $request->validate([
            'items' => ['present', 'array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.icon' => ['required', 'string', 'in:'.implode(',', self::ICON_KEYS)],
            'items.*.judul' => ['required', 'string', 'max:255'],
            'items.*.bahan_ajar_label' => ['required', 'string', 'max:255'],
        ]);

        $kegiatan->update([
            'items' => collect($validated['items'])
                ->values()
                ->map(fn ($item, $index) => [
                    'id' => $item['id'],
                    'nomor' => $index + 1,
                    'icon' => $item['icon'],
                    'judul' => $item['judul'],
                    'bahan_ajar_label' => $item['bahan_ajar_label'],
                ])
                ->all(),
        ]);

        return redirect()->back();
    }

    public function destroy(Kelas $kelas, int $item)
    {
        $this->authorize('update', $kelas);

        $kegiatan = $this->firstOrCreateFor($kelas);

        $kegiatan->update([
            'items' => collect($kegiatan->items)
                ->reject(fn ($existing) => (int) $existing['id'] === $item)
                ->values()
                ->map(fn ($existing, $index) => [...$existing, 'nomor' => $index + 1])
                ->all(),
        ]);

        LkpdKonten::where('kelas_id', $kelas->id)->where('nomor', $item)->delete();

        return redirect()->back();
    }

    protected function firstOrCreateFor(Kelas $kelas): KegiatanPembelajaran
    {
        $kegiatan = KegiatanPembelajaran::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'items' => [
                    ['id' => 1, 'nomor' => 1, 'icon' => 'video', 'judul' => 'Kegiatan Pembelajaran 1', 'bahan_ajar_label' => 'Bahan Ajar Tambahan 1'],
                    ['id' => 2, 'nomor' => 2, 'icon' => 'microscope', 'judul' => 'Kegiatan Pembelajaran 2', 'bahan_ajar_label' => 'Bahan Ajar Tambahan 2'],
                ],
            ]
        );

        // Backfill 'id' for items created before that key existed, falling back to 'nomor'.
        $items = collect($kegiatan->items);
        if ($items->contains(fn ($item) => ! array_key_exists('id', $item))) {
            $kegiatan->update([
                'items' => $items->map(fn ($item) => ['id' => $item['id'] ?? $item['nomor'], ...$item])->all(),
            ]);
        }

        return $kegiatan;
    }
}
