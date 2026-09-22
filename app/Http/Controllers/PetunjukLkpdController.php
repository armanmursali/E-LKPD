<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\PetunjukLkpd;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PetunjukLkpdController extends Controller
{
    public const ICON_KEYS = ['id-card', 'book-open', 'target', 'list-checks', 'pencil-line', 'message-question'];

    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $petunjuk = $this->firstOrCreateFor($kelas);

        return Inertia::render('petunjuk-lkpd/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'petunjukLkpd' => [
                'items' => $petunjuk->items,
            ],
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $petunjuk = $this->firstOrCreateFor($kelas);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.icon' => ['required', 'string', 'in:'.implode(',', self::ICON_KEYS)],
            'items.*.teks' => ['required', 'string'],
        ]);

        $petunjuk->update([
            'items' => collect($validated['items'])
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

    protected function firstOrCreateFor(Kelas $kelas): PetunjukLkpd
    {
        return PetunjukLkpd::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'items' => [
                    ['nomor' => 1, 'icon' => 'id-card', 'teks' => 'update'],
                    ['nomor' => 2, 'icon' => 'book-open', 'teks' => 'update'],
                    ['nomor' => 3, 'icon' => 'target', 'teks' => 'update'],
                    ['nomor' => 4, 'icon' => 'list-checks', 'teks' => 'update'],
                    ['nomor' => 5, 'icon' => 'pencil-line', 'teks' => 'update'],
                    ['nomor' => 6, 'icon' => 'message-question', 'teks' => 'update'],
                ],
            ]
        );
    }
}
