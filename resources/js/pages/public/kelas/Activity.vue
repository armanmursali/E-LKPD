<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { CheckCircle } from "@lucide/vue";
import { computed, ref } from "vue";
import DynamicHeadContent from "@/components/lkpd/DynamicHeadContent.vue";
import LkpdBlockPreview from "@/components/lkpd/LkpdBlockPreview.vue";
import LkpdSectionLabel from "@/components/lkpd/LkpdSectionLabel.vue";
import type { LkpdBlock, PengaturanLkpd } from "@/types/lkpd-block";

const props = defineProps<{
  kelas: { id: number; nama: string; public_token: string };
  kegiatan: { id: number; judul: string; bahan_ajar_label: string };
  blocks: LkpdBlock[];
  pengaturan: Partial<PengaturanLkpd>;
  identitasMapel: Record<string, unknown>;
}>();

function shuffle<T>(items: T[]): T[] {
  const result = [...items];

  for (let index = result.length - 1; index > 0; index -= 1) {
    const randomIndex = Math.floor(Math.random() * (index + 1));
    [result[index], result[randomIndex]] = [result[randomIndex], result[index]];
  }

  return result;
}

const publicBlocks = computed(() =>
  props.blocks.map((block) => {
    if (block.type !== "input_matching") return block;
    const shuffledRight = shuffle(
      block.pairs.map((pair) => ({
        kanan: pair.kanan,
        gambarKanan: pair.gambarKanan,
        lebarKanan: pair.lebarKanan,
        modeKanan: pair.modeKanan,
      }))
    );
    return {
      ...block,
      pairs: block.pairs.map((pair, index) => ({ ...pair, ...shuffledRight[index] })),
    };
  })
);
const submitted = ref(false);
const formElement = ref<HTMLFormElement | null>(null);
const participantName = ref("");
const participantGroupName = ref("");
const participantType = computed(() =>
  props.pengaturan.jenisPengerjaan === "kelompok" ? "Kelompok" : "Individu"
);
const hasIdentityBlock = computed(() =>
  publicBlocks.value.some((block) => block.type === "identity")
);
function submitAnswers() {
  if (
    !hasIdentityBlock.value ||
    !participantName.value.trim() ||
    (participantType.value === "Kelompok" && !participantGroupName.value.trim()) ||
    !formElement.value
  )
    return;
  const data = new FormData(formElement.value);
  data.set("nama_peserta", participantName.value);
  if (participantType.value === "Kelompok")
    data.set("nama_kelompok", participantGroupName.value);
  data.set("tipe_peserta", participantType.value);
  router.post(
    `/kelas-publik/${props.kelas.public_token}/kegiatan-pembelajaran/${props.kegiatan.id}/jawaban`,
    data,
    {
      preserveScroll: true,
      onSuccess: () => {
        submitted.value = true;
      },
    }
  );
}
</script>

<template>
  <Head :title="kegiatan.judul" />
  <div class="min-h-screen bg-amber-50/60 px-3 py-4 sm:px-4 sm:py-6 md:px-8 md:py-10 dark:bg-background">
    <div class="mx-auto max-w-4xl">
      <header class="flex justify-between">
        <Link
          :href="`/kelas-publik/${kelas.public_token}/kegiatan-pembelajaran`"
          class="font-extrabold tracking-wide text-amber-900 dark:text-amber-100"
          >E-LKPD WEB</Link
        ><Link
          :href="`/kelas-publik/${kelas.public_token}/kegiatan-pembelajaran`"
          class="text-sm font-semibold text-amber-900 hover:underline dark:text-amber-100"
          >Kembali</Link
        >
      </header>
      <main class="mt-6 rounded-lg bg-white p-4 shadow-xl sm:mt-8 sm:p-10 dark:bg-card">
        <LkpdSectionLabel :label="kegiatan.judul" />
        <DynamicHeadContent
          :identitas-mapel="identitasMapel"
          :head-sections="pengaturan.headSections"
          :head-selected-items="pengaturan.headSelectedItems"
          :head-style="pengaturan"
        />
        <form ref="formElement" @submit.prevent="submitAnswers">
          <div v-if="publicBlocks.length" class="space-y-5">
            <template v-for="(block, index) in publicBlocks" :key="index">
              <div
                v-if="block.type === 'identity'"
                class="space-y-2 border-b border-amber-300 pb-3 text-sm text-amber-950 dark:border-border dark:text-foreground"
              >
                <label class="block">
                  <span class="font-semibold">Nama Siswa</span>
                  <input
                    v-model="participantName"
                    required
                    name="nama_peserta"
                    class="mt-1 w-full rounded-md border border-amber-300 bg-white px-2 py-2 text-sm dark:border-border dark:bg-muted dark:text-foreground"
                    placeholder="Tulis nama"
                  />
                </label>
                <label v-if="participantType === 'Kelompok'" class="block">
                  <span class="font-semibold">Nama Kelompok</span>
                  <input
                    v-model="participantGroupName"
                    required
                    name="nama_kelompok"
                    class="mt-1 w-full rounded-md border border-amber-300 bg-white px-2 py-1 text-sm"
                    placeholder="Tulis nama kelompok"
                  />
                </label>
              </div>
              <LkpdBlockPreview
                v-else
                :block="block"
                :interactive="true"
                :answer-name="`jawaban.${index}`"
              />
            </template>
          </div>
          <p v-else class="py-12 text-center text-amber-900 dark:text-muted-foreground">
            Isi LKPD belum dibuat oleh guru.
          </p>
        </form>
        <div class="mt-8 border-t border-amber-200 pt-6 text-center dark:border-border">
          <button
            type="button"
            class="inline-flex items-center gap-2 rounded-md bg-amber-800 px-6 py-3 font-semibold text-white hover:bg-amber-900"
            @click="submitAnswers"
          >
            <CheckCircle class="size-5" />
            {{ submitted ? "Jawaban berhasil dikirim" : "Kirim Jawaban" }}
          </button>
          <p v-if="!hasIdentityBlock" class="mt-2 text-sm text-amber-700 dark:text-amber-300">
            Tambahkan blok Identitas Siswa di builder sebelum mengirim.
          </p>
          <p v-else-if="!participantName.trim()" class="mt-2 text-sm text-amber-700 dark:text-amber-300">
            Isi nama siswa sebelum mengirim.
          </p>
          <p
            v-else-if="participantType === 'Kelompok' && !participantGroupName.trim()"
            class="mt-2 text-sm text-amber-700 dark:text-amber-300"
          >
            Isi nama kelompok sebelum mengirim.
          </p>
          <p v-if="submitted" class="mt-2 text-sm text-amber-800 dark:text-amber-300">
            Jawaban Anda sudah diterima.
          </p>
        </div>
      </main>
    </div>
  </div>

  <div
    v-if="submitted"
    class="fixed inset-0 z-50 flex items-center justify-center bg-amber-950/45 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="submission-success-title"
  >
    <div class="w-full max-w-md rounded-xl bg-white p-6 text-center shadow-2xl sm:p-8">
      <CheckCircle class="mx-auto size-14 text-emerald-600" />
      <h2 id="submission-success-title" class="mt-4 text-2xl font-bold text-amber-950">
        Terima kasih!
      </h2>
      <p class="mt-2 text-sm leading-relaxed text-amber-900/75">
        Jawaban kamu sudah berhasil dikirim. Tetap semangat belajar!
      </p>
      <Link
        :href="`/kelas-publik/${kelas.public_token}`"
        class="mt-6 inline-flex w-full items-center justify-center rounded-md bg-amber-800 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-900"
        >Kembali ke Kelas</Link
      >
    </div>
  </div>
</template>
