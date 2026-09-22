<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Check, Pencil, X } from "@lucide/vue";
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
      {
        title: "Preview Jawaban",
      },
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
  <div class="min-h-screen bg-amber-50/60 p-3 sm:p-4 md:p-8 dark:bg-background">
    <div class="mx-auto max-w-4xl">
      <Link
        :href="`/kelas/${kelas.id}/kegiatan-pembelajaran/${nomor}/jawaban`"
        class="text-sm font-semibold text-amber-900 hover:underline dark:text-amber-100"
        >Kembali ke daftar jawaban</Link
      >
      <main class="mt-6 rounded-xl bg-white p-4 shadow-xl sm:p-10 dark:bg-card">
        <LkpdSectionLabel :label="item?.judul ?? 'Preview LKPD'" />
        <div class="mb-8 rounded-md border border-amber-200 bg-amber-50 p-4 dark:border-border dark:bg-secondary">
          <p class="text-xs font-bold uppercase tracking-widest text-amber-700">
            Peserta
          </p>
          <h1 class="mt-1 text-xl font-bold text-amber-950 dark:text-amber-50">
            {{ submission.nama_peserta }}
          </h1>
          <p v-if="submission.nama_kelompok" class="text-sm text-amber-900 dark:text-amber-100">
            Kelompok: {{ submission.nama_kelompok }}
          </p>
          <p class="text-sm text-amber-900/70 dark:text-muted-foreground">{{ submission.tipe_peserta }}</p>
        </div>
        <div class="space-y-6">
          <div
            v-for="(block, index) in blocks"
            :key="index"
            class="rounded-md border border-amber-100 p-4 dark:border-border"
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
    class="fixed right-3 bottom-3 z-40 inline-flex max-w-[calc(100vw-1.5rem)] items-center gap-2 rounded-full bg-amber-800 px-4 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 sm:right-6 sm:bottom-6 sm:px-5"
    @click="showScoreForm = true"
  >
    <Pencil class="size-4" />
    {{ submission.nilai === null ? "Input nilai" : `Nilai: ${submission.nilai}` }}
  </button>
  <div
    v-if="showScoreForm"
    class="fixed inset-0 z-50 flex items-end justify-center bg-amber-950/30 p-4 sm:items-center"
    @click.self="showScoreForm = false"
  >
    <form
      class="w-full max-w-sm rounded-xl bg-white p-6 shadow-2xl dark:bg-card"
      @submit.prevent="saveScore"
    >
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="text-xs font-bold uppercase tracking-widest text-amber-700">
            Penilaian
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
        >Nilai (0-100)</label
      >
      <input
        id="nilai"
        v-model="scoreForm.nilai"
        type="number"
        min="0"
        max="100"
        required
        autofocus
        class="mt-2 w-full rounded-md border border-amber-200 bg-white px-3 py-2 text-lg font-semibold text-amber-950 outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-200 dark:border-border dark:bg-muted dark:text-foreground"
      />
      <p v-if="scoreForm.errors.nilai" class="mt-2 text-sm text-red-600">
        {{ scoreForm.errors.nilai }}
      </p>
      <button
        type="submit"
        :disabled="scoreForm.processing"
        class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-md bg-amber-800 px-4 py-3 font-bold text-white hover:bg-amber-900 disabled:cursor-not-allowed disabled:opacity-60"
      >
        <Check class="size-4" />
        {{ scoreForm.processing ? "Menyimpan..." : "Simpan nilai" }}
      </button>
    </form>
  </div>
</template>
