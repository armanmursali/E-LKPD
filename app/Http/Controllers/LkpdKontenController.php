<?php

namespace App\Http\Controllers;

use App\Models\KegiatanPembelajaran;
use App\Models\Kelas;
use App\Models\LkpdKonten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LkpdKontenController extends Controller
{
    public const BLOCK_TYPES = [
        'image',
        'image_text',
        'video',
        'paragraph',
        'label',
        'input_short_text',
        'input_long_text',
        'input_image',
        'fill_blank',
        'input_radio',
        'input_checkbox',
        'input_matching',
        'identity',
        'table',
    ];

    public const DEFAULT_PENGATURAN = [
        'jenisPengerjaan' => 'individu',
        'marginAtas' => 20,
        'marginBawah' => 20,
        'marginKiri' => 25,
        'marginKanan' => 20,
        'jarakBaris' => 1.5,
    ];

    public function edit(Kelas $kelas, int $nomor)
    {
        $this->authorize('view', $kelas);

        $judul = $this->judulFor($kelas, $nomor);
        $konten = $this->firstOrCreateFor($kelas, $nomor);

        $identitasMapel = $kelas->identitasMapel?->only([
            'mata_pelajaran', 'materi', 'satuan_pendidikan', 'tahun_pelajaran',
            'tahapan_fase', 'kelas_label', 'semester', 'alokasi_waktu',
            'capaian_pembelajaran', 'alur_tujuan_pembelajaran',
            'tujuan_pembelajaran', 'indikator_ketercapaian', 'model_pembelajaran',
        ]) ?? [];

        return Inertia::render('kegiatan-pembelajaran/Builder', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'nomor' => $nomor,
            'judul' => $judul,
            'blocks' => $konten->blocks,
            'pengaturan' => $konten->pengaturan ?? self::DEFAULT_PENGATURAN,
            'aktif' => $konten->aktif,
            'identitasMapel' => $identitasMapel,
        ]);
    }

    public function uploadMedia(Request $request, Kelas $kelas, int $nomor)
    {
        $this->authorize('update', $kelas);

        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('file')->store('lkpd-media', 'public');

        if ($path === false) {
            throw new \RuntimeException('Media LKPD gagal disimpan.');
        }

        $publicUrl = rtrim((string) config('filesystems.disks.public.url'), '/');

        return response()->json(['url' => $publicUrl.'/'.ltrim($path, '/')]);
    }

    public function update(Request $request, Kelas $kelas, int $nomor)
    {
        $this->authorize('update', $kelas);

        $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'aktif' => ['required', 'boolean'],
            'blocks' => ['present', 'array'],
            'blocks.*.type' => ['required', 'string', 'in:'.implode(',', self::BLOCK_TYPES)],
            'pengaturan' => ['required', 'array'],
            'pengaturan.jenisPengerjaan' => ['required', 'string', 'in:individu,kelompok'],
            'pengaturan.marginAtas' => ['required', 'numeric', 'min:0', 'max:50'],
            'pengaturan.marginBawah' => ['required', 'numeric', 'min:0', 'max:50'],
            'pengaturan.marginKiri' => ['required', 'numeric', 'min:0', 'max:50'],
            'pengaturan.marginKanan' => ['required', 'numeric', 'min:0', 'max:50'],
            'pengaturan.jarakBaris' => ['required', 'numeric', 'min:1', 'max:3'],
        ]);

        $blocks = collect($request->input('blocks', []))
            ->map(fn ($block, $index) => $this->sanitizeBlock($block, $index))
            ->all();

        $konten = $this->firstOrCreateFor($kelas, $nomor);
        $konten->update([
            'blocks' => $blocks,
            'pengaturan' => $request->input('pengaturan'),
            'aktif' => $request->boolean('aktif'),
        ]);

        $kegiatan = $this->kegiatanFor($kelas);
        $kegiatan->update([
            'items' => collect($kegiatan->items)
                ->map(fn ($item) => (int) ($item['id'] ?? 0) === $nomor
                    ? [...$item, 'judul' => $request->input('judul')]
                    : $item
                )
                ->all(),
        ]);

        return redirect()->back();
    }

    protected function sanitizeBlock(array $block, int $index): array
    {
        $rules = match ($block['type'] ?? null) {
            'image' => [
                'url' => ['required', 'string', 'max:2048'],
                'position' => ['required', 'string', 'in:left,right,center,full'],
                'caption' => ['nullable', 'string', 'max:255'],
                'lebar' => ['nullable', 'numeric', 'min:10', 'max:100'],
            ],
            'image_text' => [
                'url' => ['required', 'string', 'max:2048'],
                'position' => ['required', 'string', 'in:left,right'],
                'caption' => ['nullable', 'string', 'max:255'],
                'text' => ['required', 'string'],
                'align' => ['required', 'string', 'in:left,center,right,justify'],
                'lebar' => ['nullable', 'numeric', 'min:10', 'max:100'],
            ],
            'video' => [
                'url' => ['required', 'string', 'max:2048', 'regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//i'],
                'caption' => ['nullable', 'string', 'max:255'],
            ],
            'paragraph' => [
                'text' => ['required', 'string'],
                'align' => ['required', 'string', 'in:left,center,right,justify'],
                'jarakBaris' => ['nullable', 'numeric', 'min:1', 'max:3'],
                'warnaTeks' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            ],
            'label' => [
                'text' => ['required', 'string', 'max:255'],
                'badgeText' => ['nullable', 'string', 'max:50'],
                'badgeBgColor' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'badgeBorderColor' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'badgeTextColor' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'badgeFontSize' => ['nullable', 'numeric', 'min:8', 'max:40'],
                'badgeSize' => ['nullable', 'numeric', 'min:16', 'max:80'],
                'badgeBold' => ['nullable', 'boolean'],
                'bgColor' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'borderColor' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'textColor' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'rounded' => ['nullable', 'numeric', 'min:0', 'max:50'],
                'image' => ['nullable', 'string', 'max:2048'],
                'imagePosition' => ['nullable', 'string', 'in:left,right'],
                'imageSize' => ['nullable', 'numeric', 'min:16', 'max:96'],
            ],
            'input_short_text', 'input_long_text', 'input_image' => [
                'label' => ['required', 'string', 'max:255'],
            ],
            'fill_blank' => [
                'segments' => ['required', 'array', 'min:1'],
                'segments.*.type' => ['required', 'string', 'in:text,input'],
                'segments.*.value' => ['nullable', 'string', 'max:2000'],
            ],
            'input_radio', 'input_checkbox' => [
                'label' => ['required', 'string', 'max:255'],
                'gunakanBackground' => ['nullable', 'boolean'],
                'warnaBackground' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'warnaTeks' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'gunakanBorder' => ['nullable', 'boolean'],
                'warnaBorder' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'options' => ['required', 'array', 'min:2'],
                'options.*.teks' => ['nullable', 'string', 'max:255'],
                'options.*.gambar' => ['nullable', 'string', 'max:2048'],
                'options.*.lebar' => ['nullable', 'numeric', 'min:10', 'max:100'],
                'options.*.mode' => ['nullable', 'string', 'in:teks,gambar'],
            ],
            'input_matching' => [
                'label' => ['required', 'string', 'max:255'],
                'metode' => ['nullable', 'string', 'in:seret,tarik_garis'],
                'gunakanBackground' => ['nullable', 'boolean'],
                'warnaBackground' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'warnaTeks' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'gunakanBorder' => ['nullable', 'boolean'],
                'warnaBorder' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'pairs' => ['required', 'array', 'min:2'],
                'pairs.*.kiri' => ['nullable', 'string', 'max:255'],
                'pairs.*.kanan' => ['nullable', 'string', 'max:255'],
                'pairs.*.gambarKiri' => ['nullable', 'string', 'max:2048'],
                'pairs.*.gambarKanan' => ['nullable', 'string', 'max:2048'],
                'pairs.*.lebarKiri' => ['nullable', 'numeric', 'min:10', 'max:100'],
                'pairs.*.lebarKanan' => ['nullable', 'numeric', 'min:10', 'max:100'],
                'pairs.*.modeKiri' => ['nullable', 'string', 'in:teks,gambar'],
                'pairs.*.modeKanan' => ['nullable', 'string', 'in:teks,gambar'],
            ],
            'identity' => [],
            'table' => [
                'rows' => ['required', 'array', 'min:1'],
                'columnWidths' => ['nullable', 'array', 'min:1'],
                'columnWidths.*' => ['numeric', 'min:10', 'max:90'],
                'rowHeights' => ['nullable', 'array', 'min:1'],
                'rowHeights.*' => ['numeric', 'min:40', 'max:600'],
                'headerRows' => ['nullable', 'integer', 'min:0'],
                'headerColor' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'headerTextColor' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'rows.*' => ['required', 'array', 'min:1'],
                'rows.*.*.type' => ['required', 'string', 'in:paragraph,image,input_short_text,input_long_text'],
                'rows.*.*.text' => ['nullable', 'string'],
                'rows.*.*.url' => ['nullable', 'string', 'max:2048'],
                'rows.*.*.lebar' => ['nullable', 'numeric', 'min:10', 'max:100'],
                'rows.*.*.jarakBaris' => ['nullable', 'numeric', 'min:1', 'max:3'],
            ],
            default => throw ValidationException::withMessages([
                "blocks.{$index}.type" => 'Tipe blok tidak valid.',
            ]),
        };

        if (in_array($block['type'], ['input_radio', 'input_checkbox'], true)) {
            $block['options'] = array_map(
                fn ($option) => is_string($option) ? ['teks' => $option, 'gambar' => ''] : $option,
                $block['options'] ?? [],
            );
        }

        $validator = Validator::make($block, $rules);

        if (in_array($block['type'], ['input_radio', 'input_checkbox'], true)) {
            $validator->after(function ($validator) use ($block) {
                foreach ($block['options'] as $optionIndex => $option) {
                    $mode = $option['mode'] ?? (filled($option['gambar'] ?? null) ? 'gambar' : 'teks');

                    if ($mode === 'teks' && blank($option['teks'] ?? null)) {
                        $validator->errors()->add("options.{$optionIndex}", 'Opsi harus memiliki teks atau gambar.');
                    } elseif ($mode === 'gambar' && blank($option['gambar'] ?? null)) {
                        $validator->errors()->add("options.{$optionIndex}", 'Opsi harus memiliki gambar.');
                    } elseif (filled($option['teks'] ?? null) && filled($option['gambar'] ?? null)) {
                        $validator->errors()->add("options.{$optionIndex}", 'Pilih teks atau gambar saja.');
                    }
                }
            });
        }

        if ($block['type'] === 'input_matching') {
            $validator->after(function ($validator) use ($block) {
                foreach ($block['pairs'] as $pairIndex => $pair) {
                    $modeKiri = $pair['modeKiri'] ?? (filled($pair['gambarKiri'] ?? null) ? 'gambar' : 'teks');
                    $modeKanan = $pair['modeKanan'] ?? (filled($pair['gambarKanan'] ?? null) ? 'gambar' : 'teks');

                    if ($modeKiri === 'teks' && blank($pair['kiri'] ?? null)) {
                        $validator->errors()->add("pairs.{$pairIndex}.kiri", 'Item kiri harus memiliki teks atau gambar.');
                    } elseif ($modeKiri === 'gambar' && blank($pair['gambarKiri'] ?? null)) {
                        $validator->errors()->add("pairs.{$pairIndex}.kiri", 'Item kiri harus memiliki gambar.');
                    } elseif (filled($pair['kiri'] ?? null) && filled($pair['gambarKiri'] ?? null)) {
                        $validator->errors()->add("pairs.{$pairIndex}.kiri", 'Pilih teks atau gambar saja.');
                    }

                    if ($modeKanan === 'teks' && blank($pair['kanan'] ?? null)) {
                        $validator->errors()->add("pairs.{$pairIndex}.kanan", 'Pasangan kanan harus memiliki teks atau gambar.');
                    } elseif ($modeKanan === 'gambar' && blank($pair['gambarKanan'] ?? null)) {
                        $validator->errors()->add("pairs.{$pairIndex}.kanan", 'Pasangan kanan harus memiliki gambar.');
                    } elseif (filled($pair['kanan'] ?? null) && filled($pair['gambarKanan'] ?? null)) {
                        $validator->errors()->add("pairs.{$pairIndex}.kanan", 'Pilih teks atau gambar saja.');
                    }
                }
            });
        }

        $validated = $validator->validate();

        if (array_key_exists('text', $validated)) {
            $validated['text'] = $this->sanitizeRichText($validated['text']);
        }

        if (array_key_exists('label', $validated)) {
            $validated['label'] = $this->sanitizeRichText($validated['label']);
        }

        if ($block['type'] === 'table') {
            $validated['rows'] = array_map(
                fn ($row) => array_map(
                    fn ($cell) => $cell['type'] === 'paragraph'
                        ? [...$cell, 'text' => $this->sanitizeRichText($cell['text'] ?? '')]
                        : $cell,
                    $row,
                ),
                $validated['rows'],
            );
        }

        if ($block['type'] === 'fill_blank') {
            $validated['segments'] = array_map(
                fn ($segment) => $segment['type'] === 'text'
                    ? [...$segment, 'value' => $this->sanitizeRichText($segment['value'] ?? '')]
                    : [...$segment, 'value' => ''],
                $validated['segments'],
            );
        }

        return ['type' => $block['type'], ...$validated];
    }

    /**
     * Strip everything except a small set of formatting tags.
     */
    protected function sanitizeRichText(string $value): string
    {
        $stripped = strip_tags($value, '<b><strong><u><i><em><br><div><p><ul><li><font>');

        $stripped = preg_replace_callback('/<font\s+color=["\']?(#[0-9a-fA-F]{6})["\']?\s*>/i', fn ($match) => '<span style="color:'.$match[1].'">', $stripped);
        $stripped = preg_replace('/<\/font>/i', '</span>', $stripped);

        return preg_replace('/<(\/?)((?:b|strong|u|i|em|br|div|p|ul|li))(\s[^>]*)?>/i', '<$1$2>', $stripped);
    }

    protected function judulFor(Kelas $kelas, int $nomor): string
    {
        $item = collect($this->kegiatanFor($kelas)->items)->firstWhere('id', $nomor);

        return $item['judul'] ?? "Kegiatan Pembelajaran {$nomor}";
    }

    protected function kegiatanFor(Kelas $kelas): KegiatanPembelajaran
    {
        return KegiatanPembelajaran::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'items' => [
                    ['id' => 1, 'nomor' => 1, 'icon' => 'video', 'judul' => 'Kegiatan Pembelajaran 1', 'bahan_ajar_label' => 'Bahan Ajar Tambahan 1'],
                    ['id' => 2, 'nomor' => 2, 'icon' => 'microscope', 'judul' => 'Kegiatan Pembelajaran 2', 'bahan_ajar_label' => 'Bahan Ajar Tambahan 2'],
                ],
            ]
        );
    }

    protected function firstOrCreateFor(Kelas $kelas, int $nomor): LkpdKonten
    {
        return LkpdKonten::firstOrCreate(
            ['kelas_id' => $kelas->id, 'nomor' => $nomor],
            ['blocks' => [], 'pengaturan' => self::DEFAULT_PENGATURAN]
        );
    }
}
