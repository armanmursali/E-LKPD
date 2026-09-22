<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { Pencil, Plus, Trash2 } from "@lucide/vue";
import { ref } from "vue";

type Kelas = {
  id: number;
  nama: string;
  deskripsi: string | null;
};
defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: "Kelas",
      },
    ],
  },
});

defineProps<{ kelas: Kelas[] }>();
const deletingId = ref<number | null>(null);

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
  <Head title="Kelas" />
  <div
    class="min-h-screen flex-1 bg-amber-50/60 px-3 py-4 sm:px-4 sm:py-6 md:px-8 md:py-10"
  >
    <div class="mx-auto max-w-6xl">
      <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700">
            Ruang kerja guru
          </p>
          <h1
            class="mt-2 text-2xl font-bold tracking-tight text-amber-950 sm:text-3xl dark:text-amber-50"
          >
            Daftar Kelas
          </h1>
          <p class="mt-2 text-sm text-amber-900/70 dark:text-amber-100/70">
            Kelola kelas dan siapkan LKPD untuk peserta didik.
          </p>
        </div>
        <Link
          href="/kelas/create"
          class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-amber-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 sm:w-fit dark:hover:bg-amber-700 dark:focus:ring-offset-background"
          ><Plus class="size-4" /> Tambah kelas</Link
        >
      </div>

      <div
        class="mt-8 overflow-hidden rounded-xl border border-amber-200 bg-white shadow-sm dark:border-amber-900/60 dark:bg-card"
      >
        <div
          v-if="kelas.length === 0"
          class="p-8 text-center text-amber-900/70 sm:p-12 dark:text-amber-100/70"
        >
          <div
            class="mx-auto flex size-12 items-center justify-center rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300"
          >
            <Plus class="size-6" />
          </div>
          <p class="mt-3 font-semibold text-amber-950 dark:text-amber-50">
            Belum ada kelas
          </p>
          <p class="mt-1 text-sm text-amber-900/70 dark:text-amber-100/70">
            Tambahkan kelas pertama untuk mulai membuat LKPD.
          </p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[620px] text-left text-sm">
            <thead
              class="border-b border-amber-200 bg-amber-50 text-amber-900/70 dark:border-amber-900/60 dark:bg-secondary dark:text-amber-100/80"
            >
              <tr>
                <th class="px-5 py-3 font-semibold">Nama kelas</th>
                <th class="px-5 py-3 font-semibold">Deskripsi</th>
                <th class="px-5 py-3 text-right font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-amber-100">
              <tr
                v-for="item in kelas"
                :key="item.id"
                class="transition duration-200 hover:bg-amber-50/70 dark:hover:bg-accent"
              >
                <td class="px-5 py-4 font-semibold text-amber-950 dark:text-amber-50">
                  <Link
                    :href="`/kelas/${item.id}`"
                    class="transition-colors hover:text-amber-700 hover:underline dark:hover:text-amber-300"
                    >{{ item.nama }}</Link
                  >
                </td>
                <td class="max-w-lg px-5 py-4 text-amber-900/65 dark:text-amber-100/65">
                  {{ item.deskripsi || "Belum ada deskripsi." }}
                </td>
                <td class="px-5 py-4 text-right">
                  <div class="flex justify-end gap-2">
                    <Link
                      :href="`/kelas/${item.id}/edit`"
                      title="Edit kelas"
                      class="inline-flex size-8 items-center justify-center rounded-md border border-amber-300 text-amber-800 transition duration-200 hover:-translate-y-0.5 hover:bg-amber-100 hover:text-amber-950 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-950/60 dark:hover:text-amber-100"
                      ><Pencil class="size-4" /></Link
                    ><button
                      type="button"
                      title="Hapus kelas"
                      class="inline-flex size-8 items-center justify-center rounded-md border border-red-200 text-red-700 transition duration-200 hover:-translate-y-0.5 hover:bg-red-50 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/50 disabled:opacity-50"
                      :disabled="deletingId === item.id"
                      @click="hapusKelas(item)"
                    >
                      <Trash2 class="size-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
