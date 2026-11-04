<?php

namespace Database\Seeders;

use App\Models\DaftarPustaka;
use App\Models\DeskripsiLkpd;
use App\Models\IdentitasMapel;
use App\Models\KegiatanPembelajaran;
use App\Models\Kelas;
use App\Models\LkpdKonten;
use App\Models\Pendahuluan;
use App\Models\PetunjukLkpd;
use App\Models\ProfilPenulis;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LkpdStemPblSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(2) ?? User::first();
        if (! $user) {
            $user = User::create([
                'name' => 'Guru IPA STEM-PBL',
                'email' => 'nuranaaayubaa@gmail.com',
                'password' => bcrypt('password123'),
            ]);
        }

        $kelasList = Kelas::where('user_id', $user->id)->get();
        if ($kelasList->isEmpty()) {
            $created = Kelas::create([
                'user_id' => $user->id,
                'nama' => 'E-LKPD IPA Kelas VII Fase D - Suhu, Kalor, dan Pemuaian (STEM-PBL)',
                'deskripsi' => 'E-LKPD IPA Berbasis STEM-PBL Kelas VII SMP/MTs Fase D pada materi Suhu dan Pengukurannya, Kalor, Perpindahan Kalor, Konduktor-Isolator, serta Pemuaian Zat (Pertemuan 1 s.d. Pertemuan 4).',
                'public_hero_image' => '/images/lkpd/pertemuan-1-cover.jpg',
            ]);
            $kelasList = collect([$created]);
        }

        $blocksPertemuan1 = [
            [
                'type' => 'image_text',
                'url' => '/images/lkpd/pertemuan-1-cover.jpg',
                'caption' => 'E-LKPD IPA Berbasis STEM-PBL • Pertemuan 1',
                'position' => 'left',
                'lebar' => 32,
                'text' => '<p><strong>E-LKPD IPA Berbasis STEM-PBL (Kelas VII • Fase D)</strong></p><p><strong>Pertemuan 1: Suhu dan Pengukurannya</strong></p><p>Selamat datang di Lembar Kerja Peserta Didik Elektronik (E-LKPD) Pertemuan 1! Pada kegiatan ini, kalian akan menyelidiki mengapa indra peraba (sentuhan tangan) tidak dapat dijadikan alat ukur suhu yang tepat serta bagaimana menggunakan termometer dan mengonversi skala suhu secara ilmiah.</p>',
                'align' => 'justify',
            ],
            $this->blueHeader('A', 'A. Identitas Kelompok'),
            ['type' => 'identity'],
            $this->longInput('<strong>Anggota Kelompok (3–4 orang):</strong><br/>Tuliskan nama lengkap seluruh anggota kelompokmu (1, 2, 3, 4)!'),
            $this->amberHeader('B', 'B. Tujuan Pembelajaran'),
            $this->paragraphBlock('<p>Peserta didik mampu menyusun dugaan awal, merencanakan dan melakukan pengukuran suhu, membandingkan perkiraan dengan data termometer, mengonversi skala suhu, serta menyimpulkan dan mengevaluasi hasil percobaan.</p>'),
            $this->blueHeader('C', 'C. Petunjuk Kegiatan'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Kerjakan E-LKPD bersama kelompok (3–4 orang).</li><li><strong>2.</strong> Bacalah permasalahan dan diskusikan tiap jawaban.</li><li><strong>3.</strong> Lakukan percobaan dengan arahan guru (air hangat disiapkan dan ditangani guru).</li><li><strong>4.</strong> Masukkan jawaban serta data hasil pengukuran.</li><li><strong>5.</strong> Presentasikan hasil diskusi sesuai arahan guru.</li></ul>'),
            $this->blueHeader('1', '1. Ayo Amati! (Mengidentifikasi Masalah)'),
            [
                'type' => 'image_text',
                'url' => '/images/lkpd/pertemuan-1-amati.jpg',
                'caption' => 'Ilustrasi mengecek suhu tubuh dengan sentuhan tangan',
                'position' => 'left',
                'lebar' => 34,
                'text' => '<p><strong>Wacana Permasalahan:</strong></p><p>Ani melihat temannya tampak lemas. Ketika menyentuh kening temannya, Ani merasa kening tersebut panas. Ani kemudian mengatakan bahwa temannya sedang demam, padahal ia belum menggunakan termometer.</p>',
                'align' => 'justify',
            ],
            [
                'type' => 'input_radio',
                'label' => '<strong>❓ Apakah sentuhan tangan sudah cukup untuk mengetahui suhu tubuh secara tepat?</strong>',
                'options' => [
                    ['teks' => 'Sudah cukup', 'mode' => 'teks', 'gambar' => '', 'lebar' => 50],
                    ['teks' => 'Belum cukup', 'mode' => 'teks', 'gambar' => '', 'lebar' => 50],
                ],
                'gunakanBackground' => true,
                'warnaBackground' => '#fef9c3',
                'warnaTeks' => '#451a03',
                'gunakanBorder' => true,
                'warnaBorder' => '#eab308',
            ],
            $this->longInput('<strong>✏️ Tuliskan dugaan awal (hipotesis) kelompokmu beserta alasannya!</strong>'),
            $this->blueHeader('2', '2. Ayo Rencanakan! (Menyusun Rencana)'),
            $this->paragraphBlock('<p>Kita akan membandingkan perkiraan suhu dua sampel air dengan hasil pengukuran termometer.</p>'),
            [
                'type' => 'image',
                'url' => '/images/lkpd/pertemuan-1-alat.jpg',
                'caption' => 'Alat dan Bahan: Dua wadah sejenis, Air suhu ruang, Air agak hangat (aman disentuh), dan Termometer untuk air',
                'position' => 'center',
                'lebar' => 75,
            ],
            $this->amberHeader('📋', 'Rancangan Percobaan'),
            $this->shortInput('<strong>a. Variabel yang dikontrol (dibuat sama):</strong>'),
            $this->shortInput('<strong>b. Variabel yang diubah (variabel bebas):</strong>'),
            $this->shortInput('<strong>c. Variabel yang diukur (variabel terikat):</strong>'),
            $this->longInput('<strong>📋 Tuliskan langkah kerja kelompokmu secara berurutan! (1, 2, 3, 4)</strong>'),
            $this->blueHeader('3', '3. Ayo Selidiki! (Melaksanakan Percobaan)'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Siapkan dua wadah berisi air dengan volume sama: <strong>A (suhu ruang)</strong> dan <strong>B (agak hangat)</strong>.</li><li><strong>2.</strong> Perkirakan sampel yang lebih hangat dengan menyentuh bagian luar wadah secara aman.</li><li><strong>3.</strong> Ukur suhu kedua sampel menggunakan termometer; tunggu pembacaan stabil.</li><li><strong>4.</strong> Catat hasil pengukuran pada tabel berikut.</li></ul>'),
            $this->amberHeader('📊', 'Tabel Hasil Pengamatan'),
            [
                'type' => 'table',
                'rows' => [
                    [
                        ['type' => 'paragraph', 'text' => '<strong>Sampel</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Perkiraan indra peraba</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Hasil termometer (°C)</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '<strong>Air A (suhu ruang)</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Perkiraan indra peraba Air A'],
                        ['type' => 'input_short_text', 'label' => 'Hasil termometer Air A (°C)'],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '<strong>Air B (agak hangat)</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Perkiraan indra peraba Air B'],
                        ['type' => 'input_short_text', 'label' => 'Hasil termometer Air B (°C)'],
                    ],
                ],
                'columnWidths' => [34, 33, 33],
                'rowHeights' => [48, 64, 64],
                'headerRows' => 1,
                'headerColor' => '#bae6fd',
                'headerTextColor' => '#0c4a6e',
            ],
            $this->blueHeader('📊', 'Ayo Analisis! (Mengolah dan Menafsirkan Data)'),
            $this->longInput('<strong>Apakah perkiraan kelompokmu sesuai dengan hasil pengukuran termometer? Jelaskan menggunakan data!</strong>'),
            $this->blueHeader('🧮', 'Ayo Berhitung! (Menerapkan Konsep)'),
            $this->paragraphBlock('<p>Pilih salah satu hasil pengukuran dalam Celsius, lalu ubah ke <strong>Reamur</strong>, <strong>Fahrenheit</strong>, dan <strong>Kelvin</strong> menggunakan rumus berikut:</p><ul><li><strong>Reamur (R):</strong> R = (4/5) × C</li><li><strong>Fahrenheit (F):</strong> F = ((9/5) × C) + 32</li><li><strong>Kelvin (K):</strong> K = C + 273</li></ul>'),
            $this->longInput('<strong>Tuliskan suhu awal (°C) dan langkah perhitungannya ke skala Reamur, Fahrenheit, dan Kelvin!</strong>'),
            $this->blueHeader('4', '4. Ayo Bagikan dan Simpulkan! (Mengomunikasikan)'),
            $this->longInput('<strong>Diskusikan hasil pengukuran, lalu tuliskan kesimpulan: Mengapa termometer lebih tepat digunakan untuk mengetahui suhu dibandingkan indra peraba?</strong>'),
            $this->blueHeader('5', '5. Ayo Evaluasi dan Refleksi!'),
            $this->longInput('<strong>• Apakah hasil percobaan mendukung dugaan awal kelompokmu? Jelaskan alasannya!</strong>'),
            $this->longInput('<strong>• Tuliskan hal yang kamu pelajari, kesulitan yang dihadapi, dan cara mengatasinya pada pembelajaran hari ini!</strong>'),
        ];

        $blocksPertemuan2 = [
            [
                'type' => 'image_text',
                'url' => '/images/lkpd/pertemuan-2-cover.jpg',
                'caption' => 'E-LKPD IPA Berbasis STEM-PBL • Pertemuan 2',
                'position' => 'left',
                'lebar' => 32,
                'text' => '<p><strong>E-LKPD IPA Berbasis STEM-PBL (Kelas VII • Fase D)</strong></p><p><strong>Pertemuan 2: Kalor, Perpindahan Kalor, Konduktor–Isolator, dan Pemuaian</strong></p><p>Pada Pertemuan 2 ini, kalian akan menyelidiki kemampuan berbagai bahan (logam, kayu, plastik) dalam menghantarkan kalor, mengelompokkan konduktor dan isolator, serta menerapkan persamaan kalor <strong>Q = m · c · ΔT</strong>.</p>',
                'align' => 'justify',
            ],
            $this->blueHeader('A', 'A. Identitas Kelompok'),
            ['type' => 'identity'],
            $this->longInput('<strong>Anggota Kelompok (3–4 orang):</strong><br/>Tuliskan nama lengkap seluruh anggota kelompokmu (1, 2, 3, 4)!'),
            $this->amberHeader('B', 'B. Tujuan Pembelajaran'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Menjelaskan pengertian kalor dan menghitung kalor (Q = m · c · ΔT).</li><li><strong>2.</strong> Mengidentifikasi perpindahan kalor (konduksi, konveksi, radiasi) dan contohnya.</li><li><strong>3.</strong> Menentukan bahan konduktor dan isolator kalor.</li><li><strong>4.</strong> Mendeskripsikan pemuaian zat dalam kehidupan sehari-hari.</li></ul>'),
            $this->blueHeader('C', 'C. Petunjuk Kegiatan'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Kerjakan E-LKPD secara berkelompok (3–4 orang).</li><li><strong>2.</strong> Bacalah permasalahan dengan teliti.</li><li><strong>3.</strong> Lakukan penyelidikan sesuai arahan guru.</li><li><strong>4.</strong> Isi data, analisis, dan kesimpulan pada kolom yang tersedia.</li><li><strong>5.</strong> Presentasikan hasil diskusi kelompok.</li></ul>'),
            $this->blueHeader('1', '1. Ayo Amati! (Mengidentifikasi Masalah)'),
            [
                'type' => 'image_text',
                'url' => '/images/lkpd/pertemuan-2-amati.jpg',
                'caption' => 'Panci yang dipanaskan di atas kompor',
                'position' => 'left',
                'lebar' => 34,
                'text' => '<p><strong>Wacana Permasalahan:</strong></p><p>Saat memasak, bagian tubuh panci menjadi panas, termasuk gagangnya. Jika gagang panci terbuat dari logam, tangan dapat terasa panas. Bagaimana cara memilih bahan gagang panci yang lebih aman dan nyaman digunakan?</p>',
                'align' => 'justify',
            ],
            $this->longInput('<strong>❓ Faktor apa saja yang memengaruhi cepat atau lambatnya panas berpindah pada suatu bahan?</strong>'),
            $this->blueHeader('2', '2. Ayo Rencanakan! (Menyusun Rencana)'),
            $this->paragraphBlock('<p>Rancanglah penyelidikan sederhana untuk membandingkan kemampuan beberapa bahan dalam menghantarkan kalor.</p>'),
            [
                'type' => 'image',
                'url' => '/images/lkpd/pertemuan-2-alat.jpg',
                'caption' => 'Alat dan Bahan: Gelas beker berisi air panas, sendok logam, sendok kayu, sendok plastik, dan stopwatch (jam tangan)',
                'position' => 'center',
                'lebar' => 75,
            ],
            $this->amberHeader('📋', 'Rancangan Percobaan'),
            $this->shortInput('<strong>a. Variabel yang dikontrol:</strong>'),
            $this->shortInput('<strong>b. Variabel yang diubah:</strong>'),
            $this->shortInput('<strong>c. Variabel yang diukur:</strong>'),
            $this->longInput('<strong>📋 Langkah Kerja (berurutan 1, 2, 3, 4):</strong>'),
            $this->blueHeader('3', '3. Ayo Selidiki! (Melakukan Percobaan)'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Masukkan ujung masing-masing sendok ke dalam gelas berisi air panas.</li><li><strong>2.</strong> Diamkan selama ±3 menit.</li><li><strong>3.</strong> Sentuh bagian ujung sendok yang tidak tercelup, lalu bandingkan tingkat kepanasannya.</li><li><strong>4.</strong> Catat hasil pengamatan pada tabel.</li></ul>'),
            $this->amberHeader('📊', 'Tabel Hasil Pengamatan'),
            [
                'type' => 'table',
                'rows' => [
                    [
                        ['type' => 'paragraph', 'text' => '<strong>No.</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Bahan Sendok</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Terasa Panas (Ya/Tidak)</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Keterangan</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '1', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Sendok logam</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Sendok logam terasa panas (Ya/Tidak)'],
                        ['type' => 'input_short_text', 'label' => 'Keterangan sendok logam'],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '2', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Sendok kayu</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Sendok kayu terasa panas (Ya/Tidak)'],
                        ['type' => 'input_short_text', 'label' => 'Keterangan sendok kayu'],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '3', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Sendok plastik</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Sendok plastik terasa panas (Ya/Tidak)'],
                        ['type' => 'input_short_text', 'label' => 'Keterangan sendok plastik'],
                    ],
                ],
                'columnWidths' => [10, 28, 30, 32],
                'rowHeights' => [48, 60, 60, 60],
                'headerRows' => 1,
                'headerColor' => '#bae6fd',
                'headerTextColor' => '#0c4a6e',
            ],
            $this->blueHeader('4', '4. Ayo Analisis! (Mengolah dan Menafsirkan Data)'),
            $this->longInput('<strong>1. Jelaskan jenis perpindahan kalor yang terjadi pada percobaan!<br/>2. Kelompokkan bahan pada tabel ke dalam konduktor atau isolator kalor, berikan alasannya!<br/>3. Bagaimana kaitannya dengan pemilihan bahan gagang panci yang aman?</strong>'),
            $this->blueHeader('5', '5. Ayo Berhitung! (Menerapkan Konsep)'),
            $this->paragraphBlock('<p>Sebuah logam bermassa <strong>0,5 kg</strong> dipanaskan dari <strong>27 °C</strong> menjadi <strong>77 °C</strong>. Jika kalor jenis logam tersebut <strong>900 J/kg·°C</strong>, hitunglah kalor yang diperlukan menggunakan rumus <strong>Q = m · c · ΔT</strong>!</p>'),
            $this->longInput('<strong>Tuliskan diketahui, ditanya, dan langkah perhitungan Q = m · c · ΔT secara lengkap!</strong>'),
            $this->blueHeader('6', '6. Ayo Simpulkan dan Refleksi!'),
            $this->longInput('<strong>Tuliskan kesimpulan dari kegiatan pembelajaran hari ini!</strong>'),
            $this->longInput('<strong>Hal apa yang kamu pelajari, kesulitan yang dihadapi, dan bagaimana mengatasinya?</strong>'),
        ];

        $blocksPertemuan3 = [
            [
                'type' => 'image_text',
                'url' => '/images/lkpd/pertemuan-3-cover.jpg',
                'caption' => 'E-LKPD IPA Berbasis STEM-PBL • Pertemuan 3: Pemuaian Zat',
                'position' => 'left',
                'lebar' => 32,
                'text' => '<p><strong>E-LKPD IPA Berbasis STEM-PBL (Kelas VII • Fase D)</strong></p><p><strong>Pertemuan 3: Pemuaian Zat</strong></p><p>Pada Pertemuan 3 ini, kalian akan mengamati fenomena pemuaian zat akibat perubahan suhu, melakukan percobaan pemuaian zat cair, serta menganalisis penerapan konsep pemuaian pada konstruksi jembatan, rel kereta api, dan kehidupan sehari-hari.</p>',
                'align' => 'justify',
            ],
            $this->blueHeader('A', 'A. Identitas Kelompok'),
            ['type' => 'identity'],
            $this->longInput('<strong>Anggota Kelompok (3–4 orang):</strong><br/>Tuliskan nama lengkap seluruh anggota kelompokmu (1, 2, 3, 4)!'),
            $this->blueHeader('B', 'B. Tujuan Pembelajaran'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Menjelaskan pengertian pemuaian zat.</li><li><strong>2.</strong> Mengidentifikasi faktor yang memengaruhi pemuaian.</li><li><strong>3.</strong> Mendeskripsikan pemuaian pada zat padat, cair, dan gas.</li><li><strong>4.</strong> Menerapkan konsep pemuaian dalam kehidupan sehari-hari.</li></ul>'),
            $this->blueHeader('C', 'C. Petunjuk Kegiatan'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Kerjakan E-LKPD secara berkelompok (3–4 orang).</li><li><strong>2.</strong> Bacalah masalah, lakukan percobaan, dan isi setiap bagian dengan data yang diperoleh.</li><li><strong>3.</strong> Diskusikan hasilnya dan presentasikan secara singkat.</li></ul>'),
            $this->blueHeader('1', '1. Ayo Amati! (Mengidentifikasi Masalah)'),
            [
                'type' => 'image_text',
                'url' => '/images/lkpd/pertemuan-3-amati.jpg',
                'caption' => 'Celah sambungan baja pada jembatan',
                'position' => 'left',
                'lebar' => 34,
                'text' => '<p><strong>Wacana Permasalahan:</strong></p><p>Pada jembatan, dua bagian baja tidak disambung rapat, melainkan diberi celah. Celah ini dibuat karena baja dapat memuai ketika suhunya meningkat.</p>',
                'align' => 'justify',
            ],
            $this->longInput('<strong>❓ Mengapa pada jembatan atau rel kereta api diberi celah? Jelaskan dugaan awal kelompokmu!</strong>'),
            $this->blueHeader('2', '2. Ayo Rencanakan! (Menyusun Rencana)'),
            $this->paragraphBlock('<p>Rancanglah percobaan sederhana untuk menyelidiki pemuaian zat cair!</p>'),
            [
                'type' => 'image',
                'url' => '/images/lkpd/pertemuan-3-alat.jpg',
                'caption' => 'Alat dan Bahan: Labu kaca berisi air berwarna, Baskom berisi air panas, Baskom berisi air dingin, serta Penutup karet dan pipa kaca',
                'position' => 'center',
                'lebar' => 75,
            ],
            $this->blueHeader('3', '3. Ayo Selidiki! (Melakukan Percobaan)'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Siapkan alat dan bahan sesuai rancangan.</li><li><strong>2.</strong> Amati posisi permukaan air dalam pipa sebelum dipanaskan.</li><li><strong>3.</strong> Masukkan labu ke dalam baskom air panas, amati perubahan permukaan air dalam pipa.</li><li><strong>4.</strong> Catat hasil pengamatan pada tabel berikut.</li></ul>'),
            $this->amberHeader('📊', 'Tabel Hasil Pengamatan'),
            [
                'type' => 'table',
                'rows' => [
                    [
                        ['type' => 'paragraph', 'text' => '<strong>Kondisi</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Tinggi permukaan cairan (cm)</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '<strong>Sebelum dipanaskan</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Tinggi permukaan cairan sebelum dipanaskan (cm)'],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '<strong>Setelah dipanaskan</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Tinggi permukaan cairan setelah dipanaskan (cm)'],
                    ],
                ],
                'columnWidths' => [50, 50],
                'rowHeights' => [48, 60, 60],
                'headerRows' => 1,
                'headerColor' => '#bae6fd',
                'headerTextColor' => '#0c4a6e',
            ],
            $this->blueHeader('4', '4. Ayo Analisis! (Mengolah Data)'),
            $this->longInput('<strong>1. Apa yang terjadi pada permukaan cairan setelah dipanaskan?<br/>2. Mengapa hal tersebut dapat terjadi? Jelaskan berdasarkan konsep pemuaian!</strong>'),
            $this->blueHeader('5', '5. Ayo Berpikir Kritis! (Penerapan dalam Kehidupan)'),
            $this->paragraphBlock('<p>Sebutkan <strong>3 contoh penerapan pemuaian zat</strong> dalam kehidupan sehari-hari dan jelaskan manfaatnya pada tabel berikut!</p>'),
            [
                'type' => 'table',
                'rows' => [
                    [
                        ['type' => 'paragraph', 'text' => '<strong>No.</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Contoh Penerapan</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Manfaat</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '1.', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Contoh Penerapan 1'],
                        ['type' => 'input_long_text', 'label' => 'Manfaat 1'],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '2.', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Contoh Penerapan 2'],
                        ['type' => 'input_long_text', 'label' => 'Manfaat 2'],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '3.', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'input_short_text', 'label' => 'Contoh Penerapan 3'],
                        ['type' => 'input_long_text', 'label' => 'Manfaat 3'],
                    ],
                ],
                'columnWidths' => [10, 42, 48],
                'rowHeights' => [48, 84, 84, 84],
                'headerRows' => 1,
                'headerColor' => '#bae6fd',
                'headerTextColor' => '#0c4a6e',
            ],
            $this->blueHeader('6', '6. Ayo Simpulkan dan Refleksi!'),
            $this->longInput('<strong>Tuliskan kesimpulan pembelajaran hari ini!</strong>'),
            $this->longInput('<strong>Apa hal baru yang kamu pelajari dan bagian apa yang masih menjadi kesulitan? Jelaskan!</strong>'),
        ];

        $blocksPertemuan4 = [
            [
                'type' => 'image_text',
                'url' => '/images/lkpd/pertemuan-4-cover.jpg',
                'caption' => 'E-LKPD IPA Berbasis STEM-PBL • Pertemuan 4: Konduksi, Konveksi, dan Radiasi',
                'position' => 'left',
                'lebar' => 32,
                'text' => '<p><strong>E-LKPD IPA Berbasis STEM-PBL (Kelas VII • Fase D)</strong></p><p><strong>Pertemuan 4: Suhu, Kalor, dan Penerapannya dalam Kehidupan Sehari-hari</strong></p><p>Pada Pertemuan 4 ini, kalian akan mengintegrasikan konsep suhu, kalor, serta ketiga mekanisme perpindahan kalor (<strong>konduksi, konveksi, dan radiasi</strong>) untuk merancang solusi masalah nyata dalam kehidupan sehari-hari.</p>',
                'align' => 'justify',
            ],
            $this->blueHeader('A', 'A. Identitas Kelompok'),
            ['type' => 'identity'],
            $this->longInput('<strong>Anggota Kelompok (3–4 orang):</strong><br/>Tuliskan nama lengkap seluruh anggota kelompokmu (1, 2, 3, 4)!'),
            $this->blueHeader('B', 'B. Tujuan Pembelajaran'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Menjelaskan pengertian kalor dan rumus Q = m · c · ΔT.</li><li><strong>2.</strong> Membedakan perpindahan kalor: konduksi, konveksi, dan radiasi, serta contohnya.</li><li><strong>3.</strong> Mengidentifikasi bahan konduktor dan isolator panas.</li><li><strong>4.</strong> Menerapkan konsep suhu dan kalor untuk memecahkan masalah dalam kehidupan sehari-hari.</li></ul>'),
            $this->blueHeader('C', 'C. Petunjuk Kegiatan'),
            $this->paragraphBlock('<ul><li><strong>1.</strong> Kerjakan E-LKPD ini secara berkelompok.</li><li><strong>2.</strong> Gunakan data hasil pengamatan, diskusi, dan sumber belajar yang tersedia.</li><li><strong>3.</strong> Tuliskan jawaban pada tempat yang disediakan.</li></ul>'),
            $this->blueHeader('1', '1. Ayo Amati! (Mengidentifikasi Masalah)'),
            [
                'type' => 'image_text',
                'url' => '/images/lkpd/pertemuan-4-amati.jpg',
                'caption' => 'Perpindahan panas saat memasak air menggunakan panci',
                'position' => 'left',
                'lebar' => 34,
                'text' => '<p><strong>Wacana Permasalahan:</strong></p><p>Saat memasak, bagian dasar panci cepat menjadi panas, sementara gagangnya tetap lebih dingin jika terbuat dari bahan tertentu. Selain itu, perpindahan panas juga dapat terjadi melalui aliran air dan pancaran panas api.</p>',
                'align' => 'justify',
            ],
            $this->longInput('<strong>❓ Masalah: Bagaimana panas dapat berpindah pada panci tersebut? Mengapa gagang panci tidak secepat dasar panci menjadi panas?</strong>'),
            $this->blueHeader('2', '2. Ayo Rencanakan! (Merancang Penyelidikan)'),
            $this->paragraphBlock('<p>Rancanglah kegiatan sederhana untuk menyelidiki perpindahan kalor!</p>'),
            $this->amberHeader('🧪', 'Variabel Percobaan'),
            $this->shortInput('<strong>Variabel bebas:</strong>'),
            $this->shortInput('<strong>Variabel terikat:</strong>'),
            $this->shortInput('<strong>Variabel kontrol:</strong>'),
            $this->blueHeader('3', '3. Ayo Selidiki! (Melakukan Percobaan)'),
            $this->paragraphBlock('<p>Lakukan penyelidikan sederhana sesuai rancanganmu! Catat hasil pengamatan pada tabel berikut.</p>'),
            [
                'type' => 'table',
                'rows' => [
                    [
                        ['type' => 'paragraph', 'text' => '<strong>No.</strong>', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Jenis Perpindahan Kalor</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Bukti / Hasil Pengamatan (apa yang terjadi?)</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '1.', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Konduksi</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_long_text', 'label' => 'Bukti/Hasil Pengamatan Konduksi'],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '2.', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Konveksi</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_long_text', 'label' => 'Bukti/Hasil Pengamatan Konveksi'],
                    ],
                    [
                        ['type' => 'paragraph', 'text' => '3.', 'align' => 'center', 'jarakBaris' => 1.4],
                        ['type' => 'paragraph', 'text' => '<strong>Radiasi</strong>', 'align' => 'left', 'jarakBaris' => 1.4],
                        ['type' => 'input_long_text', 'label' => 'Bukti/Hasil Pengamatan Radiasi'],
                    ],
                ],
                'columnWidths' => [10, 30, 60],
                'rowHeights' => [48, 84, 84, 84],
                'headerRows' => 1,
                'headerColor' => '#bae6fd',
                'headerTextColor' => '#0c4a6e',
            ],
            $this->blueHeader('4', '4. Ayo Analisis! (Mengolah Data)'),
            $this->longInput('<strong>1. Jelaskan bagaimana kalor berpindah pada masing-masing jenis perpindahan kalor (konduksi, konveksi, radiasi) berdasarkan hasil penyelidikanmu!</strong>'),
            $this->longInput('<strong>2. Berdasarkan persamaan Q = m · c · ΔT, hitunglah kalor yang dibutuhkan untuk menaikkan suhu 0,5 kg air dari 30°C menjadi 70°C! (Diketahui c air = 4.200 J/kg°C)</strong>'),
            $this->blueHeader('5', '5. Ayo Berpikir Kritis! (Mencari Solusi)'),
            $this->longInput('<strong>💡 Berdasarkan hasil penyelidikan dan konsep yang telah kamu pelajari, berikan solusi untuk masalah berikut!<br/>Gagang panci sering terasa panas saat memasak dalam waktu lama. Bagaimana cara mengurangi panas pada gagang panci? Jelaskan alasanmu!</strong>'),
            $this->blueHeader('6', '6. Ayo Simpulkan dan Refleksi!'),
            $this->longInput('<strong>✏️ Kesimpulan: Tuliskan kesimpulan dari seluruh kegiatan pembelajaran hari ini!</strong>'),
            $this->longInput('<strong>💬 Refleksi: Apa hal baru yang kamu pelajari? Bagian mana yang masih sulit dipahami?</strong>'),
        ];

        foreach ($kelasList as $kelas) {
            if (! $kelas->public_token) {
                $kelas->public_token = (string) Str::uuid();
            }
            $kelas->nama = 'E-LKPD IPA Kelas VII Fase D - Suhu, Kalor, dan Pemuaian (STEM-PBL)';
            $kelas->deskripsi = 'Lembar Kerja Peserta Didik Elektronik (E-LKPD) IPA Berbasis STEM-PBL untuk Kelas VII Fase D: Pertemuan 1 (Suhu dan Pengukurannya), Pertemuan 2 (Kalor, Perpindahan Kalor, Konduktor–Isolator, dan Pemuaian), Pertemuan 3 (Pemuaian Zat), dan Pertemuan 4 (Suhu, Kalor, dan Penerapannya dalam Kehidupan Sehari-hari).';
            $kelas->save();

            Pendahuluan::updateOrCreate(
                ['kelas_id' => $kelas->id],
                [
                    'judul' => 'E-LKPD IPA Berbasis STEM-PBL: Suhu, Kalor, dan Pemuaian',
                    'mapel' => 'Ilmu Pengetahuan Alam (IPA)',
                    'jenjang' => 'SMP / MTs',
                    'kelas_label' => 'Kelas VII (Tujuh)',
                    'fase' => 'Fase D',
                    'kurikulum' => 'Kurikulum Merdeka',
                    'penulis' => $user->name,
                    'pembimbing' => json_encode(['Dosen Pembimbing 1', 'Dosen Pembimbing 2']),
                    'validator_media' => 'Validator Ahli Media Pembelajaran',
                    'validator_materi' => 'Validator Ahli Materi IPA',
                    'kata_pengantar' => [
                        'Puji syukur kehadirat Tuhan Yang Maha Esa karena atas rahmat dan karunia-Nya, Lembar Kerja Peserta Didik Elektronik (E-LKPD) Ilmu Pengetahuan Alam (IPA) Berbasis STEM-PBL (Science, Technology, Engineering, and Mathematics - Problem Based Learning) pada materi Suhu, Kalor, dan Pemuaian untuk Kelas VII SMP/MTs Fase D ini dapat diselesaikan dengan baik.',
                        'E-LKPD ini dirancang untuk memfasilitasi peserta didik belajar aktif melalui 4 pertemuan terstruktur: Pertemuan 1 (Suhu dan Pengukurannya), Pertemuan 2 (Kalor, Perpindahan Kalor, Konduktor–Isolator, dan Pemuaian), Pertemuan 3 (Pemuaian Zat), serta Pertemuan 4 (Suhu, Kalor, dan Penerapannya dalam Kehidupan Sehari-hari).',
                        'Setiap kegiatan dalam E-LKPD ini mengikuti tahapan STEM-PBL yang meliputi: Ayo Amati! (Mengidentifikasi Masalah), Ayo Rencanakan! (Menyusun Rencana Penyelidikan), Ayo Selidiki! (Melaksanakan Percobaan), Ayo Analisis & Berhitung! (Mengolah Data dan Menerapkan Konsep), serta Ayo Simpulkan dan Refleksi! Semoga E-LKPD interaktif ini bermanfaat bagi guru dan peserta didik.',
                    ],
                ]
            );

            DeskripsiLkpd::updateOrCreate(
                ['kelas_id' => $kelas->id],
                [
                    'paragraf' => [
                        'E-LKPD IPA Berbasis STEM-PBL ini merupakan bahan ajar interaktif untuk peserta didik Kelas VII SMP/MTs (Fase D) pada pokok bahasan Suhu, Kalor, dan Pemuaian. Melalui pendekatan STEM (Science, Technology, Engineering, and Mathematics) yang dipadukan dengan model Problem Based Learning (PBL), peserta didik diajak memecahkan permasalahan kontekstual dalam kehidupan sehari-hari melalui penyelidikan ilmiah.',
                        'Materi dalam E-LKPD ini terbagi ke dalam 4 pertemuan berkesinambungan: (1) Pertemuan 1 membahas Suhu, ketepatan pengukuran termometer dibandingkan indra peraba, dan konversi skala suhu (Celsius, Reamur, Fahrenheit, Kelvin); (2) Pertemuan 2 membahas Kalor (Q = m · c · ΔT), perpindahan kalor, serta penyelidikan bahan konduktor dan isolator pada alat masak; (3) Pertemuan 3 membahas Pemuaian Zat padat, cair, dan gas beserta aplikasinya pada celah jembatan dan rel kereta api; serta (4) Pertemuan 4 membahas integrasi konsep Suhu, Kalor, dan Perpindahan Kalor (Konduksi, Konveksi, Radiasi) untuk merancang solusi masalah sehari-hari.',
                    ],
                ]
            );

            PetunjukLkpd::updateOrCreate(
                ['kelas_id' => $kelas->id],
                [
                    'items' => [
                        'Kerjakan E-LKPD ini secara berkelompok (3–4 orang) dengan berdiskusi secara aktif dan tertib.',
                        'Tuliskan Nama Lengkap perwakilan/anggota serta Nama Kelompok pada bagian A. Identitas Kelompok sebelum memulai pengerjaan.',
                        'Bacalah setiap wacana permasalahan pada tahap "1. Ayo Amati!" dengan teliti, lalu rumuskan dugaan awal (hipotesis) atau identifikasi masalahnya.',
                        'Susunlah variabel percobaan dan langkah kerja pada tahap "2. Ayo Rencanakan!", kemudian lakukan percobaan sesuai arahan dan pengawasan guru pada tahap "3. Ayo Selidiki!".',
                        'Catat data hasil pengukuran secara jujur pada Tabel Hasil Pengamatan, lalu jawab pertanyaan pada tahap "Ayo Analisis!", "Ayo Berhitung!", dan "Ayo Berpikir Kritis!".',
                        'Tuliskan kesimpulan dan refleksi pembelajaran pada bagian akhir, lalu klik tombol "Kirim Jawaban LKPD" untuk mengumpulkan hasil kerja kelompok kepada guru.',
                    ],
                ]
            );

            IdentitasMapel::updateOrCreate(
                ['kelas_id' => $kelas->id],
                [
                    'mata_pelajaran' => 'Ilmu Pengetahuan Alam (IPA)',
                    'materi' => 'Suhu, Kalor, dan Pemuaian (Berbasis STEM-PBL)',
                    'satuan_pendidikan' => 'SMP / MTs',
                    'tahun_pelajaran' => '2025/2026',
                    'tahapan_fase' => 'Fase D',
                    'kelas_label' => 'VII (Tujuh)',
                    'semester' => 'Ganjil',
                    'alokasi_waktu' => '4 Pertemuan (8 JP × 40 Menit)',
                    'capaian_pembelajaran' => 'Peserta didik mampu melakukan pengukuran terhadap aspek fisis yang mereka temui dan memanfaatkan ragam gerak dan gaya, serta memahami hubungan konsep usaha dan energi, mengukur besaran suhu yang diakibatkan oleh energi kalor yang diberikan, sekaligus dapat membedakan isolator dan konduktor kalor serta penerapannya dalam kehidupan sehari-hari.',
                    'alur_tujuan_pembelajaran' => '1. Menyelidiki konsep suhu, pengukuran suhu dengan termometer, dan konversi skala suhu (Pertemuan 1). 2. Menyelidiki hubungan kalor (Q = m · c · ΔT) serta membedakan bahan konduktor dan isolator kalor (Pertemuan 2). 3. Menyelidiki fenomena pemuaian zat padat, cair, dan gas serta penerapannya (Pertemuan 3). 4. Menganalisis mekanisme perpindahan kalor (konduksi, konveksi, radiasi) dan merancang solusi permasalahan sehari-hari (Pertemuan 4).',
                    'tujuan_pembelajaran' => [
                        'Pertemuan 1: Peserta didik mampu menyusun dugaan awal, merencanakan dan melakukan pengukuran suhu, membandingkan perkiraan dengan data termometer, mengonversi skala suhu (C, R, F, K), serta menyimpulkan dan mengevaluasi hasil percobaan.',
                        'Pertemuan 2: Peserta didik mampu menjelaskan pengertian kalor dan menghitung kalor (Q = m · c · ΔT), mengidentifikasi perpindahan kalor, menentukan bahan konduktor dan isolator kalor, serta mendeskripsikan pemuaian zat.',
                        'Pertemuan 3: Peserta didik mampu menjelaskan pengertian pemuaian zat, mengidentifikasi faktor yang memengaruhi pemuaian, mendeskripsikan pemuaian pada zat padat, cair, dan gas, serta menerapkan konsep pemuaian dalam kehidupan sehari-hari.',
                        'Pertemuan 4: Peserta didik mampu membedakan perpindahan kalor (konduksi, konveksi, dan radiasi), menghitung kalor pada pemanasan air, serta menerapkan konsep suhu dan kalor untuk memecahkan masalah dalam kehidupan sehari-hari.',
                    ],
                    'indikator_ketercapaian' => [
                        'Membandingkan hasil perkiraan suhu menggunakan indra peraba dengan hasil pengukuran termometer secara objektif.',
                        'Mengonversi skala suhu dari derajat Celsius (°C) ke skala Reamur (°R), Fahrenheit (°F), dan Kelvin (K).',
                        'Mengelompokkan bahan konduktor (sendok logam) dan isolator kalor (sendok kayu, sendok plastik) berdasarkan data percobaan.',
                        'Menghitung besarnya energi kalor yang diperlukan suatu zat menggunakan persamaan Q = m · c · ΔT.',
                        'Menganalisis peristiwa pemuaian zat cair dan menjelaskan fungsi celah muai pada jembatan serta rel kereta api.',
                        'Merancang solusi pemilihan bahan isolator pada gagang panci berdasarkan konsep perpindahan kalor konduksi, konveksi, dan radiasi.',
                    ],
                    'model_pembelajaran' => [
                        [
                            'nama' => 'STEM - Problem Based Learning (STEM-PBL)',
                            'sintaks' => [
                                '1. Ayo Amati! (Mengidentifikasi Masalah / Orientasi Peserta Didik pada Masalah)',
                                '2. Ayo Rencanakan! (Menyusun Rencana & Merancang Penyelidikan)',
                                '3. Ayo Selidiki! (Melaksanakan Percobaan & Pengamatan Ilmiah)',
                                '4. Ayo Analisis, Berhitung & Berpikir Kritis! (Mengolah Data dan Menerapkan Konsep STEM)',
                                '5. Ayo Bagikan, Simpulkan & Refleksi! (Mengomunikasikan dan Mengevaluasi Pemecahan Masalah)',
                            ],
                        ],
                    ],
                ]
            );

            DaftarPustaka::updateOrCreate(
                ['kelas_id' => $kelas->id],
                [
                    'items' => [
                        'Inabuy, Victoriani, dkk. (2021). Ilmu Pengetahuan Alam untuk SMP Kelas VII. Jakarta: Pusat Kurikulum dan Perbukuan, Badan Penelitian dan Pengembangan dan Perbukuan, Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi.',
                        'Hardanie, Budiyanti Dwi, dkk. (2021). Buku Panduan Guru Ilmu Pengetahuan Alam untuk SMP Kelas VII. Jakarta: Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi.',
                        'Hewitt, Paul G. (2015). Conceptual Physics (12th Edition). Pearson Education.',
                        'Zubaidah, Siti, dkk. (2017). Ilmu Pengetahuan Alam SMP/MTs Kelas VII Semester 1. Jakarta: Kementerian Pendidikan dan Kebudayaan.',
                    ],
                ]
            );

            ProfilPenulis::updateOrCreate(
                ['kelas_id' => $kelas->id],
                [
                    'paragraf' => 'Pengembang E-LKPD IPA Berbasis STEM-PBL pada materi Suhu, Kalor, dan Pemuaian untuk Peserta Didik Kelas VII SMP/MTs Fase D. E-LKPD ini dikembangkan untuk meningkatkan kemampuan berpikir kritis, literasi sains, dan keterampilan pemecahan masalah peserta didik melalui kegiatan eksperimen dan kolaborasi kelompok secara interaktif.',
                ]
            );

            $kegiatanItems = [
                [
                    'id' => 1,
                    'nomor' => 1,
                    'icon' => 'microscope',
                    'judul' => 'Pertemuan 1: Suhu dan Pengukurannya',
                    'bahan_ajar_label' => 'E-LKPD IPA Berbasis STEM-PBL • Kelas VII Fase D • Pertemuan 1',
                    'aktif' => true,
                ],
                [
                    'id' => 2,
                    'nomor' => 2,
                    'icon' => 'microscope',
                    'judul' => 'Pertemuan 2: Kalor, Perpindahan Kalor, Konduktor–Isolator, dan Pemuaian',
                    'bahan_ajar_label' => 'E-LKPD IPA Berbasis STEM-PBL • Kelas VII Fase D • Pertemuan 2',
                    'aktif' => true,
                ],
                [
                    'id' => 3,
                    'nomor' => 3,
                    'icon' => 'microscope',
                    'judul' => 'Pertemuan 3: Pemuaian Zat',
                    'bahan_ajar_label' => 'E-LKPD IPA Berbasis STEM-PBL • Kelas VII Fase D • Pertemuan 3',
                    'aktif' => true,
                ],
                [
                    'id' => 4,
                    'nomor' => 4,
                    'icon' => 'microscope',
                    'judul' => 'Pertemuan 4: Suhu, Kalor, dan Penerapannya dalam Kehidupan Sehari-hari',
                    'bahan_ajar_label' => 'E-LKPD IPA Berbasis STEM-PBL • Kelas VII Fase D • Pertemuan 4',
                    'aktif' => true,
                ],
            ];

            KegiatanPembelajaran::updateOrCreate(
                ['kelas_id' => $kelas->id],
                ['items' => $kegiatanItems]
            );

            $meetingsBlocks = [
                1 => $blocksPertemuan1,
                2 => $blocksPertemuan2,
                3 => $blocksPertemuan3,
                4 => $blocksPertemuan4,
            ];

            foreach ($meetingsBlocks as $nomor => $blocks) {
                $pengaturan = [
                    'jenisPengerjaan' => 'kelompok',
                    'warnaBackground' => '#fffbeb',
                    'warnaTeks' => '#451a03',
                    'ukuranTeks' => 14,
                    'jarakBaris' => 1.6,
                    'headSections' => ['mata_pelajaran', 'materi', 'kelas_label', 'tahapan_fase', 'tujuan_pembelajaran'],
                    'headSelectedItems' => [
                        'tujuan_pembelajaran' => [$nomor - 1],
                    ],
                    'headBgColor' => '#f0f9ff',
                    'headBorderColor' => '#0284c7',
                    'headTextColor' => '#0c4a6e',
                ];

                LkpdKonten::updateOrCreate(
                    [
                        'kelas_id' => $kelas->id,
                        'nomor' => $nomor,
                    ],
                    [
                        'blocks' => $blocks,
                        'pengaturan' => $pengaturan,
                        'aktif' => true,
                    ]
                );
            }

            $this->command?->info("Berhasil mengisi E-LKPD IPA STEM-PBL (Pertemuan 1-4) untuk Kelas ID {$kelas->id}.");
        }
    }

    private function blueHeader(string $badge, string $text): array
    {
        return [
            'type' => 'label',
            'text' => $text,
            'bgColor' => '#0284c7',
            'borderColor' => '#0369a1',
            'textColor' => '#ffffff',
            'rounded' => 999,
            'badgeText' => $badge,
            'badgeBgColor' => '#fef08a',
            'badgeBorderColor' => '#ca8a04',
            'badgeTextColor' => '#713f12',
            'badgeSize' => 34,
            'badgeFontSize' => 14,
            'badgeBold' => true,
            'image' => '',
            'imagePosition' => 'left',
            'imageSize' => 32,
        ];
    }

    private function amberHeader(string $badge, string $text): array
    {
        return [
            'type' => 'label',
            'text' => $text,
            'bgColor' => '#fef08a',
            'borderColor' => '#eab308',
            'textColor' => '#713f12',
            'rounded' => 999,
            'badgeText' => $badge,
            'badgeBgColor' => '#ffffff',
            'badgeBorderColor' => '#eab308',
            'badgeTextColor' => '#713f12',
            'badgeSize' => 32,
            'badgeFontSize' => 14,
            'badgeBold' => true,
            'image' => '',
            'imagePosition' => 'left',
            'imageSize' => 32,
        ];
    }

    private function paragraphBlock(string $html, string $align = 'left'): array
    {
        return [
            'type' => 'paragraph',
            'text' => $html,
            'align' => $align,
            'jarakBaris' => 1.6,
            'warnaTeks' => '#451a03',
        ];
    }

    private function shortInput(string $label): array
    {
        return [
            'type' => 'input_short_text',
            'label' => $label,
        ];
    }

    private function longInput(string $label): array
    {
        return [
            'type' => 'input_long_text',
            'label' => $label,
        ];
    }
}
