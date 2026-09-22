<?php

namespace App\Http\Controllers;

use App\Models\DeskripsiLkpd;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeskripsiLkpdController extends Controller
{
    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $deskripsi = $this->firstOrCreateFor($kelas);

        return Inertia::render('deskripsi-lkpd/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'deskripsiLkpd' => [
                'paragraf' => $deskripsi->paragraf,
            ],
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $deskripsi = $this->firstOrCreateFor($kelas);

        $validated = $request->validate([
            'paragraf' => ['required', 'string'],
        ]);

        $paragraf = array_values(array_filter(array_map('trim', explode("\n\n", $validated['paragraf']))));

        $deskripsi->update([
            'paragraf' => $paragraf,
        ]);

        return redirect()->back();
    }

    protected function firstOrCreateFor(Kelas $kelas): DeskripsiLkpd
    {
        return DeskripsiLkpd::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'paragraf' => [
                    'Lembar kerja peserta didik (LKPD) merupakan lembar kerja yang berisikan sebuah kegiatan peserta didik sebagai panduan belajar. Model pembelajaran yang digunakan yaitu model pembelajaran Problem Based Learning (PBL). Model pembelajaran Problem Based Learning (PBL) merupakan model pembelajaran berbasis masalah yang memiliki lima tahapan, yaitu: mengarahkan peserta didik pada masalah; mempersiapkan peserta didik untuk belajar; membimbing penyelidikan individu atau kelompok; mengembangkan & menyajikan hasil karya; serta menganalisis & mengevaluasi proses pemecahan masalah. Beberapa tahapan pada model pembelajaran Problem Based Learning di dalam LKPD ini berisikan komponen keterampilan berpikir kritis dan sikap ilmiah dalam menyelesaikan suatu permasalahan. Adapun komponen berpikir kritis yang digunakan menurut Ennis dengan 5 tahapan, yaitu: klasifikasi dasar; dukungan dasar; kesimpulan/inferensi; klasifikasi/penjelasan lanjut; serta strategi dan teknik. Sedangkan komponen sikap ilmiah terdiri dari; sikap ingin tahu; sikap terhadap data/fakta; sikap berpikir kritis; sikap penemuan dan kreatifitas; sikap berpikir terbuka dan berkerja sama; sikap ketekunan; serta sikap peka terhadap lingkungan sekitar.',
                    'LKPD berbasis Problem Based Learning pada materi pencemaran lingkungan disajikan dengan fakta-fakta mengenai lingkungan. Selain itu pada LKPD ini juga mengimplementasikan berdasarkan permasalahan nyata yang ada di kehidupan sehari-hari peserta didik dan setiap kegiatan pada LKPD ini terdiri dari 2 kegiatan utama yaitu kegiatan pembelajaran 1 dan kegiatan pembelajaran 2. Pada pembelajaran 1 terdiri dari 3 sintaks model pembelajaran Problem Based Learning yaitu (1) Mengarahkan peserta didik pada masalah, (2) Mempersiapkan peserta didik untuk belajar, dan (3) Membimbing penyelidikan individu atau kelompok. Sedangkan pada kegiatan pembelajaran 2 terdiri dari 2 sintaks model pembelajaran Problem Based Learning yaitu (1) Mengembangkan & menyajikan hasil, dan (2) mengevaluasi proses pemecahan masalah.',
                ],
            ]
        );
    }
}
