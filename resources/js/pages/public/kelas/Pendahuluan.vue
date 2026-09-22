<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import ParagraphContent from '@/components/lkpd/ParagraphContent.vue';
import PerkenalanContent from '@/components/lkpd/PerkenalanContent.vue';

defineProps<{
    kelas: { id: number; nama: string; public_token: string };
    pendahuluan: { sampul: Record<string, unknown>; kata_pengantar: string[] } | null;
}>();
</script>

<template>
    <Head title="Pendahuluan" />
    <div class="min-h-screen bg-amber-50/60 px-4 py-6 md:px-8 md:py-10"><div class="mx-auto max-w-4xl"><header class="flex justify-between"><Link :href="`/kelas-publik/${kelas.public_token}`" class="font-extrabold tracking-wide text-amber-900">E-LKPD WEB</Link><Link :href="`/kelas-publik/${kelas.public_token}`" class="text-sm font-semibold text-amber-900 hover:underline">Kembali</Link></header><main class="mt-8 rounded-lg bg-white p-6 shadow-xl sm:p-10"><LkpdSectionLabel label="Pendahuluan" /><div v-if="pendahuluan" class="space-y-10"><PerkenalanContent :sampul="pendahuluan.sampul as never" /><div><LkpdSectionLabel label="Kata Pengantar" /><ParagraphContent :paragraphs="pendahuluan.kata_pengantar" /></div></div><p v-else class="text-center text-amber-900">Pendahuluan belum tersedia.</p></main></div></div>
</template>
