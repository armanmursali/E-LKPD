<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import {
  ArrowRight,
  BarChart3,
  BookOpen,
  CheckCircle2,
  ClipboardList,
  Plus,
  Users,
} from "@lucide/vue";
import { dashboard } from "@/routes";

type DashboardClass = {
  id: number;
  nama: string;
  deskripsi: string | null;
  submissionsCount: number;
};
type RecentSubmission = {
  id: number;
  kelasId: number;
  kelasNama: string;
  namaPeserta: string;
  kegiatanNomor: number;
  nilai: number | null;
  createdAt: string;
};

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: "Dashboard",
        href: dashboard(),
      },
    ],
  },
});

defineProps<{
  summary: {
    classes: number;
    submissions: number;
    graded: number;
    average: number | null;
  };
  classes: DashboardClass[];
  recentSubmissions: RecentSubmission[];
}>();

function formatDate(value: string) {
  return new Date(value).toLocaleString("id-ID", {
    dateStyle: "medium",
    timeStyle: "short",
  });
}
</script>

<template>
  <Head title="Dashboard" />

  <div class="min-h-full bg-amber-50/60 p-3 sm:p-5 lg:p-8 dark:bg-background">
    <div class="mx-auto max-w-7xl space-y-6">
      <section
        class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-7 dark:border-border dark:bg-card"
      >
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p
              class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400"
            >
              Ruang kerja guru
            </p>
            <h1
              class="mt-2 text-2xl font-bold tracking-tight text-amber-950 sm:text-3xl dark:text-amber-50"
            >
              Ringkasan pembelajaran
            </h1>
            <p
              class="mt-2 max-w-2xl text-sm leading-relaxed text-amber-900/70 dark:text-muted-foreground"
            >
              Kelola kelas, pantau jawaban peserta, dan lihat perkembangan nilai dari satu
              tempat.
            </p>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link
              href="/kelas/create"
              class="inline-flex flex-1 items-center justify-center gap-2 rounded-md bg-amber-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-amber-700 hover:shadow-md sm:flex-none"
              ><Plus class="size-4" /> Tambah kelas</Link
            ><Link
              href="/statistic"
              class="inline-flex flex-1 items-center justify-center gap-2 rounded-md border border-amber-300 px-4 py-2.5 text-sm font-semibold text-amber-800 transition hover:bg-amber-50 sm:flex-none dark:border-amber-700 dark:text-amber-300 dark:hover:bg-accent"
              ><BarChart3 class="size-4" /> Statistik</Link
            >
          </div>
        </div>
      </section>
      <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div
          class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm dark:border-border dark:bg-card"
        >
          <BookOpen class="size-6 text-amber-700 dark:text-amber-400" />
          <p class="mt-4 text-sm text-amber-900/70 dark:text-muted-foreground">
            Total kelas
          </p>
          <p class="mt-1 text-3xl font-bold text-amber-950 dark:text-amber-50">
            {{ summary.classes }}
          </p>
        </div>
        <div
          class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm dark:border-border dark:bg-card"
        >
          <ClipboardList class="size-6 text-amber-700 dark:text-amber-400" />
          <p class="mt-4 text-sm text-amber-900/70 dark:text-muted-foreground">
            Total jawaban
          </p>
          <p class="mt-1 text-3xl font-bold text-amber-950 dark:text-amber-50">
            {{ summary.submissions }}
          </p>
        </div>
        <div
          class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm dark:border-border dark:bg-card"
        >
          <CheckCircle2 class="size-6 text-amber-700 dark:text-amber-400" />
          <p class="mt-4 text-sm text-amber-900/70 dark:text-muted-foreground">
            Sudah dinilai
          </p>
          <p class="mt-1 text-3xl font-bold text-amber-950 dark:text-amber-50">
            {{ summary.graded }}
          </p>
        </div>
        <div
          class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm dark:border-border dark:bg-card"
        >
          <Users class="size-6 text-amber-700 dark:text-amber-400" />
          <p class="mt-4 text-sm text-amber-900/70 dark:text-muted-foreground">
            Rata-rata nilai
          </p>
          <p class="mt-1 text-3xl font-bold text-amber-950 dark:text-amber-50">
            {{ summary.average ?? "-" }}
          </p>
        </div>
      </section>
      <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <section
          class="rounded-xl border border-amber-200 bg-white shadow-sm dark:border-border dark:bg-card"
        >
          <div
            class="flex items-center justify-between gap-3 border-b border-amber-100 p-5 dark:border-border sm:p-6"
          >
            <div>
              <h2 class="text-lg font-bold text-amber-950 dark:text-amber-50">
                Kelas saya
              </h2>
              <p class="mt-1 text-sm text-amber-900/60 dark:text-muted-foreground">
                Akses cepat ke ruang kerja setiap kelas.
              </p>
            </div>
            <Link
              href="/kelas"
              class="text-sm font-semibold text-amber-800 hover:underline dark:text-amber-300"
              >Lihat semua</Link
            >
          </div>
          <div
            v-if="!classes.length"
            class="p-10 text-center text-sm text-muted-foreground"
          >
            Belum ada kelas. Tambahkan kelas pertama untuk mulai membuat LKPD.
          </div>
          <div v-else class="divide-y divide-amber-100 dark:divide-border">
            <Link
              v-for="item in classes"
              :key="item.id"
              :href="`/kelas/${item.id}`"
              class="flex items-center justify-between gap-4 p-5 transition hover:bg-amber-50/70 sm:px-6 dark:hover:bg-accent"
              ><span class="min-w-0"
                ><span
                  class="block truncate font-semibold text-amber-950 dark:text-amber-50"
                  >{{ item.nama }}</span
                ><span
                  class="mt-1 block truncate text-sm text-amber-900/60 dark:text-muted-foreground"
                  >{{ item.deskripsi || "Belum ada deskripsi." }}</span
                ></span
              ><span
                class="flex shrink-0 items-center gap-2 text-xs font-semibold text-amber-800 dark:text-amber-300"
                >{{ item.submissionsCount }} jawaban <ArrowRight class="size-4" /></span
            ></Link>
          </div>
        </section>
        <section
          class="rounded-xl border border-amber-200 bg-white shadow-sm dark:border-border dark:bg-card"
        >
          <div class="border-b border-amber-100 p-5 dark:border-border sm:p-6">
            <h2 class="text-lg font-bold text-amber-950 dark:text-amber-50">
              Aktivitas terbaru
            </h2>
            <p class="mt-1 text-sm text-amber-900/60 dark:text-muted-foreground">
              Jawaban peserta yang baru masuk.
            </p>
          </div>
          <div
            v-if="!recentSubmissions.length"
            class="p-10 text-center text-sm text-muted-foreground"
          >
            Belum ada jawaban terbaru.
          </div>
          <div v-else class="divide-y divide-amber-100 dark:divide-border">
            <Link
              v-for="submission in recentSubmissions"
              :key="submission.id"
              :href="`/kelas/${submission.kelasId}/kegiatan-pembelajaran/${submission.kegiatanNomor}/jawaban/${submission.id}`"
              class="block p-5 transition hover:bg-amber-50/70 dark:hover:bg-accent"
              ><div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <p class="truncate font-semibold text-amber-950 dark:text-amber-50">
                    {{ submission.namaPeserta }}
                  </p>
                  <p
                    class="mt-1 truncate text-xs text-amber-900/60 dark:text-muted-foreground"
                  >
                    {{ submission.kelasNama }} · Tugas {{ submission.kegiatanNomor }}
                  </p>
                </div>
                <span
                  class="shrink-0 rounded-full px-2 py-1 text-xs font-semibold"
                  :class="
                    submission.nilai === null
                      ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300'
                      : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300'
                  "
                  >{{
                    submission.nilai === null
                      ? "Belum dinilai"
                      : `Nilai ${submission.nilai}`
                  }}</span
                >
              </div>
              <p class="mt-3 text-xs text-muted-foreground">
                {{ formatDate(submission.createdAt) }}
              </p></Link
            >
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
