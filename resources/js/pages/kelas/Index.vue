<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { ArrowRight, BookOpen, Pencil, Plus, Search, Trash2 } from "@lucide/vue";
import { computed, ref } from "vue";

type Kelas = {
  id: number;
  nama: string;
  deskripsi: string | null;
};
defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: "Daftar Kelas",
      },
    ],
  },
});

const props = defineProps<{ kelas: Kelas[] }>();
const deletingId = ref<number | null>(null);
const searchQuery = ref("");

const filteredKelas = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) return props.kelas;
  return props.kelas.filter(
    (item) =>
      item.nama.toLowerCase().includes(q) ||
      (item.deskripsi ?? "").toLowerCase().includes(q)
  );
});

function hapusKelas(kelas: Kelas) {
  if (!window.confirm(`Hapus kelas "${kelas.nama}"?`)) return;

  deletingId.value = kelas.id;
  router.delete(`/kelas/${kelas.id}`, {
    onFinish: () => {
      deletingId.value = null;
    },
  });
}
</script>

<template>
  <Head title="Daftar Kelas" />
  <div
    class="min-h-screen flex-1 bg-amber-50/60 px-3 py-4 sm:px-5 sm:py-6 md:px-8 md:py-8 dark:bg-background"
  >
    <div class="mx-auto max-w-6xl space-y-6">
      <div
        class="flex flex-col justify-between gap-4 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end sm:p-6 dark:border-border dark:bg-card"
      >
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">
            Ruang kerja guru
          </p>
          <h1
            class="mt-1.5 text-2xl font-bold tracking-tight text-amber-950 sm:text-3xl dark:text-amber-50"
          >
            Daftar Kelas
          </h1>
          <p class="mt-1.5 text-sm text-amber-900/70 dark:text-amber-100/70">
            Kelola kelas dan siapkan modul serta LKPD interaktif untuk peserta didik.
          </p>
        </div>
        <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
          <div v-if="kelas.length > 0" class="relative">
            <Search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-amber-700/70" />
            <input
              v-model="searchQuery"
              type="search"
              placeholder="Cari kelas..."
              class="w-full rounded-lg border border-amber-200 bg-amber-50/50 py-2 pr-3 pl-9 text-sm text-amber-950 outline-none focus:border-amber-600 focus:bg-white sm:w-56 dark:border-border dark:bg-background dark:text-foreground"
            />
          </div>
          <Link
            href="/kelas/create"
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-amber-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 sm:w-fit dark:hover:bg-amber-700 dark:focus:ring-offset-background"
            ><Plus class="size-4" /> Tambah kelas</Link
          >
        </div>
      </div>

      <div
        v-if="kelas.length === 0"
        class="rounded-2xl border border-dashed border-amber-300 bg-white p-10 text-center text-amber-900/70 shadow-xs sm:p-14 dark:border-amber-900/60 dark:bg-card dark:text-amber-100/70"
      >
        <div
          class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300"
        >
          <BookOpen class="size-7" />
        </div>
        <p class="mt-4 text-lg font-bold text-amber-950 dark:text-amber-50">
          Belum ada kelas
        </p>
        <p class="mx-auto mt-1 max-w-md text-sm text-amber-900/70 dark:text-amber-100/70">
          Tambahkan kelas pertama Anda untuk mulai menyusun materi, evaluasi, dan LKPD interaktif.
        </p>
        <Link
          href="/kelas/create"
          class="mt-5 inline-flex items-center gap-2 rounded-lg bg-amber-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700"
        >
          <Plus class="size-4" /> Buat Kelas Pertama
        </Link>
      </div>

      <div
        v-else-if="filteredKelas.length === 0"
        class="rounded-2xl border border-amber-200 bg-white p-10 text-center text-sm text-amber-900/70 dark:border-border dark:bg-card"
      >
        Tidak ditemukan kelas dengan kata kunci "{{ searchQuery }}".
      </div>

      <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="item in filteredKelas"
          :key="item.id"
          class="group flex flex-col justify-between rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-amber-400 hover:shadow-md dark:border-amber-900/60 dark:bg-card"
        >
          <div>
            <div class="flex items-start justify-between gap-3">
              <span
                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-base font-black text-amber-800 transition group-hover:bg-amber-800 group-hover:text-white dark:bg-amber-950/60 dark:text-amber-300"
              >
                <BookOpen class="size-5" />
              </span>
              <div class="flex items-center gap-1.5">
                <Link
                  :href="`/kelas/${item.id}/edit`"
                  title="Edit kelas"
                  class="inline-flex size-8 items-center justify-center rounded-lg border border-amber-200 text-amber-800 transition hover:bg-amber-100 hover:text-amber-950 dark:border-amber-800 dark:text-amber-300 dark:hover:bg-amber-950/60"
                >
                  <Pencil class="size-3.5" />
                </Link>
                <button
                  type="button"
                  title="Hapus kelas"
                  class="inline-flex size-8 items-center justify-center rounded-lg border border-red-200 text-red-700 transition hover:bg-red-50 disabled:opacity-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/50"
                  :disabled="deletingId === item.id"
                  @click="hapusKelas(item)"
                >
                  <Trash2 class="size-3.5" />
                </button>
              </div>
            </div>

            <Link :href="`/kelas/${item.id}`" class="mt-4 block">
              <h2
                class="line-clamp-1 text-lg font-bold text-amber-950 transition group-hover:text-amber-800 dark:text-amber-50 dark:group-hover:text-amber-300"
              >
                {{ item.nama }}
              </h2>
              <p
                class="mt-1.5 line-clamp-2 min-h-[2.5rem] text-sm leading-relaxed text-amber-900/65 dark:text-amber-100/65"
              >
                {{ item.deskripsi || "Belum ada deskripsi kelas." }}
              </p>
            </Link>
          </div>

          <div class="mt-5 border-t border-amber-100 pt-3.5 dark:border-border">
            <Link
              :href="`/kelas/${item.id}`"
              class="inline-flex w-full items-center justify-between rounded-lg bg-amber-50/80 px-3.5 py-2 text-xs font-bold text-amber-900 transition group-hover:bg-amber-800 group-hover:text-white dark:bg-secondary dark:text-amber-200"
            >
              <span>Kelola Isi & LKPD</span>
              <ArrowRight class="size-4 transition-transform group-hover:translate-x-0.5" />
            </Link>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>
