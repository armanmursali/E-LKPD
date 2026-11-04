<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import ParagraphContent from '@/components/lkpd/ParagraphContent.vue';

defineProps<{ kelas: { nama: string; public_token: string }; paragraf: string[] }>();
</script>

<template>
    <Head title="Deskripsi LKPD" />
    <div class="min-h-screen bg-amber-50/60 px-4 py-6 md:px-8 md:py-10">
        <div class="mx-auto max-w-4xl space-y-6">
            <header class="flex items-center justify-between gap-3">
                <Link :href="`/kelas-publik/${kelas.public_token}`" class="inline-flex items-center gap-2 font-black tracking-wider text-amber-950">
                    <span class="flex size-8 items-center justify-center rounded-lg bg-amber-800 text-sm text-white">E</span>
                    <span>E-LKPD WEB</span>
                </Link>
                <Link
                    :href="`/kelas-publik/${kelas.public_token}`"
                    class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-bold text-amber-900 shadow-xs transition hover:bg-amber-50"
                >
                    <ArrowLeft class="size-3.5" />
                    Kembali ke Kelas
                </Link>
            </header>
            <main class="rounded-2xl border border-amber-200 bg-white p-6 shadow-xl sm:p-10">
                <LkpdSectionLabel label="Deskripsi LKPD" />
                <ParagraphContent :paragraphs="paragraf" />
            </main>
        </div>
    </div>
</template>
