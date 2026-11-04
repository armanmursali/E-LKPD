<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ArrowLeft, Award, Check, Pencil, UserCheck, X } from "@lucide/vue";
import { ref } from "vue";
import LkpdBlockPreview from "@/components/lkpd/LkpdBlockPreview.vue";
import LkpdSectionLabel from "@/components/lkpd/LkpdSectionLabel.vue";
import type { LkpdBlock } from "@/types/lkpd-block";

type Submission = {
  id: number;
  nama_peserta: string;
  nama_kelompok?: string | null;
  tipe_peserta: string;
  jawaban: Record<string, unknown>;
  nilai: number | null;
  created_at: string;
};
const props = defineProps<{
  kelas: { id: number; nama: string };
  nomor: number;
  submission: Submission;
  blocks: LkpdBlock[];
  item: { judul: string } | null;
}>();
const showScoreForm = ref(false);
const scoreForm = useForm({ nilai: props.submission.nilai ?? "" });
defineOptions({
  layout: {
    breadcrumbs: [
      { title: "Daftar Kelas", href: "/kelas" },
      { title: "Preview Jawaban" },
    ],
  },
});
function saveScore() {
  scoreForm.put(
    `/kelas/${props.kelas.id}/kegiatan-pembelajaran/${props.nomor}/jawaban/${props.submission.id}/nilai`,
    {
      preserveScroll: true,
      onSuccess: () => {
        showScoreForm.value = false;
      },
    }
  );
}
</script>
<template>
  <Head title="Preview Jawaban" />
  <div class="min-h-screen bg-amber-50/60 p-3 sm:p-5 md:p-8 dark:bg-background">
    <div class="mx-auto max-w-4xl space-y-5 pb-16">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <Link
          :href="`/kelas/${kelas.id}/kegiatan-pembelajaran/${nomor}/jawaban`"
          class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-amber-900 shadow-xs transition hover:border-amber-300 hover:bg-amber-50 dark:border-border dark:bg-card dark:text-amber-100"
        >
          <ArrowLeft class="size-3.5" />
          Kembali ke daftar jawaban
        </Link>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-full bg-amber-800 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-700"
          @click="showScoreForm = true"
        >
          <Award class="size-4" />
          {{ submission.nilai === null ? "Beri Nilai Sekarang" : `Nilai Saat Ini: ${submission.nilai}` }}
        </button>
      </div>

      <main class="rounded-2xl border border-amber-200 bg-white p-5 shadow-md sm:p-10 dark:border-border dark:bg-card">
        <LkpdSectionLabel :label="item?.judul ?? 'Preview LKPD'" />
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-amber-200 bg-amber-50/80 p-4 sm:p-5 dark:border-border dark:bg-secondary">
          <div class="flex items-center gap-3.5">
            <span class="flex size-12 items-center justify-center rounded-xl bg-amber-800 text-white">
              <UserCheck class="size-6" />
            </span>
            <div>
              <p class="text-xs font-bold uppercase tracking-widest text-amber-700">
                Identitas Peserta ({{ submission.tipe_peserta }})
              </p>
              <h1 class="mt-0.5 text-xl font-bold text-amber-950 dark:text-amber-50">
                {{ submission.nama_peserta }}
              </h1>
              <p v-if="submission.nama_kelompok" class="text-sm font-medium text-amber-900 dark:text-amber-100">
                Kelompok: {{ submission.nama_kelompok }}
              </p>
            </div>
          </div>
          <div class="text-right">
            <span
              class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-extrabold"
              :class="
                submission.nilai === null
                  ? 'bg-amber-200/80 text-amber-900'
                  : 'bg-emerald-100 text-emerald-800'
              "
            >
              {{ submission.nilai === null ? "Belum Dinilai" : `Nilai: ${submission.nilai} / 100` }}
            </span>
          </div>
        </div>
        <div class="space-y-6">
          <div
            v-for="(block, index) in blocks"
            :key="index"
            class="rounded-xl border border-amber-100 p-4 dark:border-border"
          >
            <LkpdBlockPreview
              :block="block"
              :answer-name="`jawaban.${index}`"
              :answers="submission.jawaban"
            />
          </div>
        </div>
      </main>
    </div>
  </div>
  <button
    type="button"
    class="fixed right-4 bottom-4 z-40 inline-flex items-center gap-2 rounded-full bg-amber-800 px-5 py-3 text-sm font-bold text-white shadow-xl transition hover:-translate-y-0.5 hover:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 sm:right-6 sm:bottom-6"
    @click="showScoreForm = true"
  >
    <Pencil class="size-4" />
    {{ submission.nilai === null ? "Input nilai" : `Nilai: ${submission.nilai}` }}
  </button>
  <div
    v-if="showScoreForm"
    class="fixed inset-0 z-50 flex items-end justify-center bg-amber-950/40 p-4 backdrop-blur-xs sm:items-center"
    @click.self="showScoreForm = false"
  >
    <form
      class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl dark:bg-card"
      @submit.prevent="saveScore"
    >
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="text-xs font-bold uppercase tracking-widest text-amber-700">
            Penilaian Tugas
          </p>
          <h2 class="mt-1 text-xl font-bold text-amber-950 dark:text-amber-50">
            Nilai {{ submission.nama_peserta }}
          </h2>
        </div>
        <button
          type="button"
          class="rounded-md p-1 text-amber-900/60 hover:bg-amber-50 dark:text-muted-foreground dark:hover:bg-accent"
          @click="showScoreForm = false"
        >
          <X class="size-5" />
        </button>
      </div>
      <label class="mt-6 block text-sm font-semibold text-amber-950 dark:text-amber-50" for="nilai"
        >Masukkan Nilai (0–100)</label
      >
      <input
        id="nilai"
        v-model="scoreForm.nilai"
        type="number"
        min="0"
        max="100"
        required
        autofocus
        class="mt-2 w-full rounded-xl border border-amber-300 bg-white px-3.5 py-2.5 text-lg font-bold text-amber-950 outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-200 dark:border-border dark:bg-muted dark:text-foreground"
      />
      <p v-if="scoreForm.errors.nilai" class="mt-2 text-sm text-red-600">
        {{ scoreForm.errors.nilai }}
      </p>
      <button
        type="submit"
        :disabled="scoreForm.processing"
        class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-800 px-4 py-3 font-bold text-white hover:bg-amber-900 disabled:cursor-not-allowed disabled:opacity-60"
      >
        <Check class="size-4" />
        {{ scoreForm.processing ? "Menyimpan..." : "Simpan nilai" }}
      </button>
    </form>
  </div>
</template>
