<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { ArrowLeft, CheckCircle, Send, UserRound } from "@lucide/vue";
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
  <div class="min-h-screen bg-amber-50/60 px-3 py-4 sm:px-5 sm:py-6 md:px-8 md:py-10 dark:bg-background">
    <div class="mx-auto max-w-4xl space-y-5">
      <header class="flex items-center justify-between gap-3">
        <Link
          :href="`/kelas-publik/${kelas.public_token}`"
          class="inline-flex items-center gap-2 font-black tracking-wider text-amber-950 dark:text-amber-100"
        >
          <span class="flex size-8 items-center justify-center rounded-lg bg-amber-800 text-sm text-white">E</span>
          <span>E-LKPD WEB</span>
        </Link>
        <Link
          :href="`/kelas-publik/${kelas.public_token}/kegiatan-pembelajaran`"
          class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-bold text-amber-900 shadow-xs transition hover:bg-amber-50 dark:border-border dark:bg-card dark:text-amber-100"
        >
          <ArrowLeft class="size-3.5" />
          Kembali ke Daftar Kegiatan
        </Link>
      </header>

      <main class="rounded-2xl border border-amber-200 bg-white p-4 shadow-xl sm:p-10 dark:border-border dark:bg-card">
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
                class="rounded-xl border border-amber-300 bg-amber-50/70 p-4 text-sm text-amber-950 shadow-2xs dark:border-border dark:bg-secondary dark:text-foreground"
              >
                <div class="mb-3 flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-amber-800">
                  <UserRound class="size-4" />
                  <span>Identitas Pengerjaan ({{ participantType }})</span>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                  <label class="block" :class="participantType !== 'Kelompok' ? 'sm:col-span-2' : ''">
                    <span class="text-xs font-bold">Nama Lengkap Siswa <span class="text-red-600">*</span></span>
                    <input
                      v-model="participantName"
                      required
                      name="nama_peserta"
                      class="mt-1 w-full rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-500/20 dark:border-border dark:bg-muted dark:text-foreground"
                      placeholder="Masukkan nama lengkapmu..."
                    />
                  </label>
                  <label v-if="participantType === 'Kelompok'" class="block">
                    <span class="text-xs font-bold">Nama Kelompok <span class="text-red-600">*</span></span>
                    <input
                      v-model="participantGroupName"
                      required
                      name="nama_kelompok"
                      class="mt-1 w-full rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-500/20"
                      placeholder="Contoh: Kelompok 1"
                    />
                  </label>
                </div>
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
            class="inline-flex items-center gap-2 rounded-xl bg-amber-800 px-7 py-3.5 font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-amber-700"
            @click="submitAnswers"
          >
            <CheckCircle v-if="submitted" class="size-5" />
            <Send v-else class="size-4" />
            {{ submitted ? "Jawaban berhasil dikirim" : "Kirim Jawaban LKPD" }}
          </button>
          <p v-if="!hasIdentityBlock" class="mt-2.5 text-xs font-medium text-amber-700 dark:text-amber-300">
            Tambahkan blok Identitas Siswa di builder sebelum mengirim.
          </p>
          <p v-else-if="!participantName.trim()" class="mt-2.5 text-xs font-medium text-amber-700 dark:text-amber-300">
            Pastikan nama siswa sudah diisi sebelum mengirim jawaban.
          </p>
          <p
            v-else-if="participantType === 'Kelompok' && !participantGroupName.trim()"
            class="mt-2.5 text-xs font-medium text-amber-700 dark:text-amber-300"
          >
            Pastikan nama kelompok sudah diisi sebelum mengirim jawaban.
          </p>
          <p v-if="submitted" class="mt-2.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            Jawaban Anda sudah diterima.
          </p>
        </div>
      </main>
    </div>
  </div>

  <div
    v-if="submitted"
    class="fixed inset-0 z-50 flex items-center justify-center bg-amber-950/50 p-4 backdrop-blur-xs"
    role="dialog"
    aria-modal="true"
    aria-labelledby="submission-success-title"
  >
    <div class="w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl sm:p-8">
      <CheckCircle class="mx-auto size-16 text-emerald-600" />
      <h2 id="submission-success-title" class="mt-4 text-2xl font-bold text-amber-950">
        Terima kasih!
      </h2>
      <p class="mt-2 text-sm leading-relaxed text-amber-900/75">
        Jawaban kamu sudah berhasil dikirim ke guru. Tetap semangat belajar!
      </p>
      <Link
        :href="`/kelas-publik/${kelas.public_token}`"
        class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-amber-800 px-5 py-3 text-sm font-bold text-white hover:bg-amber-900"
        >Kembali ke Beranda Kelas</Link
      >
    </div>
  </div>
</template>
