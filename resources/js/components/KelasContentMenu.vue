<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import {
  Activity,
  BookOpen,
  ClipboardCheck,
  FileText,
  Gamepad2,
  IdCard,
  Info,
  Library,
  UserCircle,
} from "@lucide/vue";

withDefaults(
  defineProps<{
    kelasId: number;
    showManagement?: boolean;
    publicToken?: string;
  }>(),
  {
    showManagement: false,
    publicToken: undefined,
  }
);

const menuItems = [
  { key: "pendahuluan", label: "Pendahuluan", subtitle: "Sampul & Pengantar", icon: BookOpen },
  { key: "deskripsi-lkpd", label: "Deskripsi LKPD", subtitle: "Gambaran Umum", icon: FileText },
  { key: "petunjuk-lkpd", label: "Petunjuk LKPD", subtitle: "Panduan Belajar", icon: Info },
  { key: "identitas-mapel", label: "Identitas Mata Pelajaran", subtitle: "CP, TP & Model", icon: IdCard },
  { key: "kegiatan-pembelajaran", label: "Kegiatan Pembelajaran", subtitle: "Lembar Kerja Siswa", icon: Activity },
  { key: "evaluasi-pembelajaran", label: "Evaluasi Pembelajaran", subtitle: "Pre-test & Post-test", icon: ClipboardCheck },
  { key: "daftar-pustaka", label: "Daftar Pustaka", subtitle: "Sumber Referensi", icon: Library },
  { key: "profil-penulis", label: "Profil Penulis", subtitle: "Biodata Penyusun", icon: UserCircle },
  { key: "game", label: "Game", subtitle: "Kuis Interaktif", icon: Gamepad2 },
];
</script>

<template>
  <nav
    aria-label="Konten kelas"
    class="grid grid-cols-2 gap-3.5 sm:grid-cols-3 sm:gap-4 lg:grid-cols-3"
  >
    <Link
      v-for="(menu, index) in menuItems"
      :key="menu.key"
      :href="
        publicToken
          ? `/kelas-publik/${publicToken}/${menu.key}`
          : `/kelas/${kelasId}/${menu.key}`
      "
      class="group relative flex min-h-32 flex-col items-center justify-center gap-2.5 overflow-hidden rounded-2xl border border-amber-200/90 bg-gradient-to-b from-amber-50/50 to-amber-50/90 p-4 text-center shadow-xs transition duration-200 ease-out hover:-translate-y-1 hover:border-amber-500 hover:from-white hover:to-amber-100/70 hover:shadow-md active:translate-y-0 active:scale-[0.98] sm:min-h-36 sm:p-5 dark:border-amber-900/70 dark:from-secondary dark:to-secondary dark:hover:border-amber-600 dark:hover:bg-accent"
      :class="publicToken ? 'touch-manipulation' : ''"
    >
      <span
        class="absolute top-2.5 left-3 text-[11px] font-black tracking-wider text-amber-800/35 transition group-hover:text-amber-800/60 dark:text-amber-300/30"
      >
        0{{ index + 1 }}
      </span>
      <span
        class="flex size-12 items-center justify-center rounded-2xl bg-amber-800 text-amber-50 shadow-sm transition duration-200 group-hover:scale-105 group-hover:bg-amber-700 group-active:scale-95"
      >
        <component :is="menu.icon" class="size-6" />
      </span>
      <div>
        <span
          class="block text-sm font-bold leading-snug text-amber-950 dark:text-amber-50"
          >{{ menu.label }}</span
        >
        <span
          class="mt-0.5 block text-[11px] font-medium text-amber-900/60 dark:text-amber-200/60"
          >{{ menu.subtitle }}</span
        >
      </div>
    </Link>
  </nav>
</template>
