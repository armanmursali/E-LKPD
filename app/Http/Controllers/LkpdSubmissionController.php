<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\LkpdKonten;
use App\Models\LkpdSubmission;
use App\Notifications\NewSubmissionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;

class LkpdSubmissionController extends Controller
{
    public function store(Request $request, string $token, int $nomor)
    {
        $kelas = Kelas::where('public_token', $token)->firstOrFail();
        $konten = LkpdKonten::where('kelas_id', $kelas->id)->where('nomor', $nomor)->first();
        abort_unless(!$konten || $konten->aktif, 404);

        $request->validate([
            'nama_peserta' => ['required', 'string', 'max:255'],
            'tipe_peserta' => ['required', 'string', 'in:Individu,Kelompok'],
            'nama_kelompok' => ['nullable', 'required_if:tipe_peserta,Kelompok', 'string', 'max:255'],
            'jawaban' => ['nullable', 'array'],
            'jawaban.*' => ['nullable'],
            'foto_jawaban.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $answers = $this->flatten($request->input('jawaban', []));
        foreach ($request->file('foto_jawaban', []) as $key => $file) {
            $answers['foto_jawaban.'.$key] = rtrim((string) config('filesystems.disks.public.url'), '/').'/'.$file->store('lkpd-answers', 'public');
        }

        $submission = LkpdSubmission::create([
            'kelas_id' => $kelas->id,
            'kegiatan_nomor' => $nomor,
            'nama_peserta' => $request->string('nama_peserta')->trim()->toString(),
            'tipe_peserta' => $request->string('tipe_peserta')->toString(),
            'nama_kelompok' => $request->filled('nama_kelompok') ? $request->string('nama_kelompok')->trim()->toString() : null,
            'jawaban' => $answers,
        ]);

        $kelas->user->notify(new NewSubmissionNotification($submission));

        return back()->with('success', 'Jawaban berhasil dikirim.');
    }

    private function flatten(array $items, string $prefix = 'jawaban'): array
    {
        $result = [];

        foreach ($items as $key => $value) {
            $name = $prefix.'.'.$key;
            if (is_array($value)) {
                $result = [...$result, ...$this->flatten($value, $name)];
            } else {
                $result[$name] = $value;
            }
        }

        return $result;
    }

    public function index(Request $request, Kelas $kelas, int $nomor)
    {
        abort_unless($kelas->user_id === $request->user()->id, 403);
        return Inertia::render('kegiatan-pembelajaran/Submissions', [
            'kelas' => $kelas->only(['id', 'nama']),
            'nomor' => $nomor,
            'submissions' => $kelas->lkpdSubmissions()->where('kegiatan_nomor', $nomor)->latest()->get(),
        ]);
    }

    public function export(Request $request, Kelas $kelas, int $nomor): StreamedResponse
    {
        abort_unless($kelas->user_id === $request->user()->id, 403);

        $submissions = $kelas->lkpdSubmissions()->where('kegiatan_nomor', $nomor)->latest()->get();
        $filename = 'nilai-peserta-'.$kelas->id.'-'.$nomor.'.csv';

        return response()->streamDownload(function () use ($submissions): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Nama siswa/kelompok', 'Tipe', 'Nilai', 'Dikirim'], ';');

            foreach ($submissions as $submission) {
                fputcsv($handle, [
                    $submission->nama_peserta,
                    $submission->tipe_peserta,
                    $submission->nilai ?? 'Belum dinilai',
                    $submission->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function show(Request $request, Kelas $kelas, int $nomor, LkpdSubmission $submission)
    {
        abort_unless($kelas->user_id === $request->user()->id && $submission->kelas_id === $kelas->id && $submission->kegiatan_nomor === $nomor, 404);
        $konten = $kelas->kegiatanPembelajaran;
        $blocks = \App\Models\LkpdKonten::where('kelas_id', $kelas->id)->where('nomor', $nomor)->first()?->blocks ?? [];
        $item = collect($konten?->items ?? [])->firstWhere('id', $nomor);

        return Inertia::render('kegiatan-pembelajaran/SubmissionShow', compact('kelas', 'nomor', 'submission', 'blocks', 'item'));
    }

    public function updateNilai(Request $request, Kelas $kelas, int $nomor, LkpdSubmission $submission)
    {
        abort_unless($kelas->user_id === $request->user()->id && $submission->kelas_id === $kelas->id && $submission->kegiatan_nomor === $nomor, 404);

        $validated = $request->validate([
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $submission->update($validated);

        return back()->with('success', 'Nilai berhasil disimpan.');
    }
}