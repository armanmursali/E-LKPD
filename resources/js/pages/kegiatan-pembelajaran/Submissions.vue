<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { Download, Users } from "@lucide/vue";

type Submission = {
  id: number;
  nama_peserta: string;
  tipe_peserta: string;
  nilai: number | null;
  created_at: string;
};
defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: "Partisipan",
      },
    ],
  },
});
defineProps<{
  kelas: { id: number; nama: string };
  nomor: number;
  submissions: Submission[];
}>();
</script>
<template>
  <Head title="Jawaban Peserta" />
  <div class="min-h-screen bg-amber-50/60 p-3 sm:p-4 md:p-8">
    <div class="mx-auto max-w-4xl">
      <Link
        :href="`/kelas/${kelas.id}/kegiatan-pembelajaran`"
        class="text-sm font-semibold text-amber-900 hover:underline"
        >Kembali ke kegiatan</Link
      >
      <div class="mt-6 flex flex-wrap items-end justify-between gap-4 sm:mt-8">
        <div>
          <p class="text-xs font-bold uppercase tracking-widest text-amber-700">
            Hasil pengerjaan
          </p>
          <h1 class="mt-2 text-2xl font-bold text-amber-950 sm:text-3xl">Jawaban Peserta</h1>
        </div>
        <div class="flex w-full items-center justify-between gap-3 sm:w-auto sm:justify-end">
          <Users class="size-8 text-amber-800 sm:size-9" /><a
            :href="`/kelas/${kelas.id}/kegiatan-pembelajaran/${nomor}/jawaban/export`"
            class="inline-flex items-center gap-2 rounded-md bg-amber-800 px-3 py-2 text-sm font-bold text-white hover:bg-amber-900 sm:px-4"
            ><Download class="size-4" /> Unduh Excel</a
          >
        </div>
      </div>
      <div
        class="mt-6 overflow-x-auto rounded-xl border border-amber-200 bg-white shadow-sm"
      >
        <div v-if="!submissions.length" class="p-12 text-center text-amber-900/70">
          Belum ada siswa atau kelompok yang mengirim jawaban.
        </div>
        <table v-else class="w-full min-w-[680px] text-left text-sm">
          <thead class="bg-amber-50 text-amber-900">
            <tr>
              <th class="px-5 py-3">Nama siswa/kelompok</th>
              <th class="px-5 py-3">Tipe</th>
              <th class="px-5 py-3">Nilai</th>
              <th class="px-5 py-3">Dikirim</th>
              <th class="px-5 py-3 text-right">Lihat</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-amber-100">
            <tr
              v-for="submission in submissions"
              :key="submission.id"
              class="hover:bg-amber-50/60"
            >
              <td class="px-5 py-4 font-semibold text-amber-950">
                {{ submission.nama_peserta }}
              </td>
              <td class="px-5 py-4 text-amber-900/70">{{ submission.tipe_peserta }}</td>
              <td
                class="px-5 py-4 font-semibold"
                :class="
                  submission.nilai === null ? 'text-amber-900/50' : 'text-amber-800'
                "
              >
                {{ submission.nilai === null ? "Belum dinilai" : submission.nilai }}
              </td>
              <td class="px-5 py-4 text-amber-900/70">
                {{ new Date(submission.created_at).toLocaleString("id-ID") }}
              </td>
              <td class="px-5 py-4 text-right">
                <Link
                  :href="`/kelas/${kelas.id}/kegiatan-pembelajaran/${nomor}/jawaban/${submission.id}`"
                  class="font-semibold text-amber-800 hover:underline"
                  >Periksa</Link
                >
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
