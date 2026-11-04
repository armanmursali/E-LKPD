<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, ArrowRight, CheckCircle2, Clock, Download, Users } from "@lucide/vue";

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
      { title: "Daftar Kelas", href: "/kelas" },
      { title: "Jawaban Peserta" },
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
  <div class="min-h-screen bg-amber-50/60 p-3 sm:p-5 md:p-8 dark:bg-background">
    <div class="mx-auto max-w-5xl space-y-6">
      <div>
        <Link
          :href="`/kelas/${kelas.id}/kegiatan-pembelajaran`"
          class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-amber-900 shadow-xs transition hover:border-amber-300 hover:bg-amber-50 dark:border-border dark:bg-card dark:text-amber-100"
        >
          <ArrowLeft class="size-3.5" />
          Kembali ke Kegiatan Pembelajaran
        </Link>
      </div>

      <div class="flex flex-wrap items-end justify-between gap-4 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6 dark:border-border dark:bg-card">
        <div>
          <p class="text-xs font-bold uppercase tracking-widest text-amber-700 dark:text-amber-400">
            Hasil pengerjaan · Kegiatan {{ nomor }}
          </p>
          <h1 class="mt-1.5 text-2xl font-bold text-amber-950 sm:text-3xl dark:text-amber-50">
            Jawaban Peserta Didik
          </h1>
          <p class="mt-1 text-sm text-amber-900/70 dark:text-muted-foreground">
            {{ submissions.length }} kiriman jawaban masuk pada kegiatan ini.
          </p>
        </div>
        <a
          :href="`/kelas/${kelas.id}/kegiatan-pembelajaran/${nomor}/jawaban/export`"
          class="inline-flex items-center gap-2 rounded-xl bg-amber-800 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700"
        >
          <Download class="size-4" /> Unduh Rekap Excel
        </a>
      </div>

      <div
        class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm dark:border-border dark:bg-card"
      >
        <div v-if="!submissions.length" class="p-12 text-center text-amber-900/70">
          <Users class="mx-auto size-12 text-amber-700/60" />
          <p class="mt-3 font-bold text-amber-950 dark:text-amber-50">Belum ada kiriman jawaban</p>
          <p class="mt-1 text-sm">Siswa atau kelompok yang mengirim jawaban LKPD akan muncul di sini.</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[680px] text-left text-sm">
            <thead class="border-b border-amber-200 bg-amber-50/80 text-xs uppercase tracking-wider text-amber-900/80 dark:border-border dark:bg-secondary dark:text-amber-200">
              <tr>
                <th class="px-5 py-3.5 font-bold">Nama Siswa / Kelompok</th>
                <th class="px-5 py-3.5 font-bold">Tipe</th>
                <th class="px-5 py-3.5 font-bold">Status & Nilai</th>
                <th class="px-5 py-3.5 font-bold">Waktu Dikirim</th>
                <th class="px-5 py-3.5 text-right font-bold">Tindakan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-amber-100 dark:divide-border">
              <tr
                v-for="submission in submissions"
                :key="submission.id"
                class="transition hover:bg-amber-50/60 dark:hover:bg-accent"
              >
                <td class="px-5 py-4 font-bold text-amber-950 dark:text-amber-50">
                  {{ submission.nama_peserta }}
                </td>
                <td class="px-5 py-4">
                  <span class="rounded-md bg-amber-100/70 px-2.5 py-1 text-xs font-semibold text-amber-900 dark:bg-secondary dark:text-amber-200">
                    {{ submission.tipe_peserta }}
                  </span>
                </td>
                <td class="px-5 py-4">
                  <span
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold"
                    :class="
                      submission.nilai === null
                        ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300'
                        : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300'
                    "
                  >
                    <Clock v-if="submission.nilai === null" class="size-3.5" />
                    <CheckCircle2 v-else class="size-3.5" />
                    {{ submission.nilai === null ? "Belum dinilai" : `Nilai: ${submission.nilai}` }}
                  </span>
                </td>
                <td class="px-5 py-4 text-xs text-amber-900/70 dark:text-muted-foreground">
                  {{ new Date(submission.created_at).toLocaleString("id-ID", { dateStyle: "medium", timeStyle: "short" }) }}
                </td>
                <td class="px-5 py-4 text-right">
                  <Link
                    :href="`/kelas/${kelas.id}/kegiatan-pembelajaran/${nomor}/jawaban/${submission.id}`"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-800 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-amber-700"
                  >
                    Periksa <ArrowRight class="size-3.5" />
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
