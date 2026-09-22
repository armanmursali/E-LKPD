<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pendahuluan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PendahuluanController extends Controller
{
    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $pendahuluan = $this->firstOrCreateFor($kelas);
        $pembimbing = json_decode((string) $pendahuluan->pembimbing, true);
        $pembimbing = is_array($pembimbing) ? $pembimbing : [$pendahuluan->pembimbing];

        return Inertia::render('pendahuluan/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'pendahuluan' => [
                'sampul' => [
                    ...$pendahuluan->only([
                        'judul', 'mapel', 'jenjang', 'kelas_label', 'fase', 'kurikulum',
                        'penulis', 'validator_media', 'validator_materi',
                    ]),
                    'pembimbing' => array_values(array_filter($pembimbing)),
                ],
                'kata_pengantar' => $pendahuluan->kata_pengantar,
            ],
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $pendahuluan = $this->firstOrCreateFor($kelas);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'mapel' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'string', 'max:255'],
            'kelas_label' => ['required', 'string', 'max:255'],
            'fase' => ['required', 'string', 'max:255'],
            'kurikulum' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'pembimbing' => ['required', 'array', 'min:1'],
            'pembimbing.*' => ['required', 'string', 'max:255'],
            'validator_media' => ['nullable', 'string', 'max:255'],
            'validator_materi' => ['nullable', 'string', 'max:255'],
            'kata_pengantar' => ['required', 'string'],
        ]);

        $paragraf = array_values(array_filter(array_map('trim', explode("\n\n", $validated['kata_pengantar']))));
        $pembimbing = array_values(array_filter(array_map('trim', $validated['pembimbing'])));

        $pendahuluan->update([
            ...collect($validated)->except(['kata_pengantar', 'pembimbing'])->all(),
            'pembimbing' => json_encode($pembimbing, JSON_UNESCAPED_UNICODE),
            'kata_pengantar' => $paragraf,
        ]);

        return redirect()->back();
    }

    protected function firstOrCreateFor(Kelas $kelas): Pendahuluan
    {
        return Pendahuluan::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'judul' => 'LKPD Berbasis Problem Based Learning Pada Materi Pencemaran Lingkungan',
                'mapel' => 'Biologi',
                'jenjang' => 'SMA',
                'kelas_label' => 'Kelas X',
                'fase' => 'Fase E',
                'kurikulum' => 'Kurikulum Merdeka',
                'penulis' => 'Nurul Istiqomah, S.Pd',
                'pembimbing' => json_encode(['Dr. Tien Aminatun, S.Si., M.Si'], JSON_UNESCAPED_UNICODE),
                'validator_media' => '',
                'validator_materi' => '',
                'kata_pengantar' => [
                    'Puji Syukur atas kehadirat Allah SWT yang telah melimpahkan rahmat dan hidayah-Nya sehingga penulis dapat menyelesaikan Lembar Kerja Peserta Didik (LKPD) Berbasis Problem Based Learning.',
                    'LKPD ini disusun dengan harapan agar dapat digunakan sebagai bahan ajar peserta didik dalam pembelajaran Biologi serta LKPD yang dikembangkan oleh penulis memuat materi tentang pencemaran lingkungan untuk kelas X/Fase E SMA dengan menggunakan kurikulum merdeka. LKPD dengan menggunakan model pembelajaran berbasis Problem Based Learning, peserta didik akan melakukan kegiatan diskusi dan kegiatan praktek yang disuguhkan dengan permasalahan sehari-hari yang berkaitan langsung dengan materi yang dipelajari. Selain itu, peserta didik juga dilatih untuk membuat dugaan sementara atau hipotesis sebelum melakukan kegiatan.',
                    'Penulis menyadari bahwa LKPD berbasis Problem Based Learning ini masih banyak yang harus diperbaiki. Oleh karena itu, penulis mengharapkan kritik dan saran yang bersifat membangun demi menyempurnakan LKPD ini untuk selanjutnya dapat menjadi lebih baik dan mempunyai potensi untuk dikembangkan. Dengan terselesaikannya LKPD ini, besar harapan penulis agar dapat bermanfaat bagi orang lain dan bagi peneliti sendiri.',
                ],
            ]
        );
    }
}
    