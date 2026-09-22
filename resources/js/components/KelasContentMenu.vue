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
  { key: "pendahuluan", label: "Pendahuluan", icon: BookOpen },
  { key: "deskripsi-lkpd", label: "Deskripsi LKPD", icon: FileText },
  { key: "petunjuk-lkpd", label: "Petunjuk LKPD", icon: Info },
  { key: "identitas-mapel", label: "Identitas Mata Pelajaran", icon: IdCard },
  { key: "kegiatan-pembelajaran", label: "Kegiatan Pembelajaran", icon: Activity },
  { key: "evaluasi-pembelajaran", label: "Evaluasi Pembelajaran", icon: ClipboardCheck },
  { key: "daftar-pustaka", label: "Daftar Pustaka", icon: Library },
  { key: "profil-penulis", label: "Profil Penulis", icon: UserCircle },
  { key: "game", label: "Game", icon: Gamepad2 },
];
</script>

<template>
  <nav
    aria-label="Konten kelas"
    class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4"
  >
    <Link
      v-for="menu in menuItems"
      :key="menu.key"
      :href="
        publicToken
          ? `/kelas-publik/${publicToken}/${menu.key}`
          : `/kelas/${kelasId}/${menu.key}`
      "
      class="group flex min-h-28 flex-col items-center justify-center gap-3 rounded-lg border border-amber-200 bg-amber-50/60 p-3 text-center shadow-sm transition duration-200 ease-out hover:-translate-y-0.5 hover:border-amber-500 hover:bg-amber-100 hover:shadow-md active:translate-y-0.5 active:scale-[0.97] active:shadow-inner sm:min-h-32 sm:p-4 dark:border-amber-900/70 dark:bg-secondary dark:hover:border-amber-600 dark:hover:bg-accent"
      :class="publicToken ? 'touch-manipulation' : ''"
    >
      <span
        class="flex size-12 items-center justify-center rounded-full bg-amber-800 text-amber-100 transition duration-200 group-hover:bg-amber-700 group-active:scale-90 group-active:rotate-[-6deg]"
      >
        <component :is="menu.icon" class="size-6" />
      </span>
      <span
        class="text-sm font-semibold leading-snug text-amber-950 dark:text-amber-50"
        >{{ menu.label }}</span
      >
    </Link>
  </nav>
</template>
