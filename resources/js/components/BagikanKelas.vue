<script setup lang="ts">
import { Check, Copy, QrCode, Share2, X } from '@lucide/vue';
import QRCode from 'qrcode';
import { computed, ref, watch } from 'vue';

const props = defineProps<{ token: string }>();
const isOpen = ref(false);
const copied = ref(false);
const qrCode = ref('');
const publicUrl = computed(() => `${window.location.origin}/kelas-publik/${props.token}`);

watch(isOpen, async (open) => {
    if (open) qrCode.value = await QRCode.toDataURL(publicUrl.value, { width: 280, margin: 2, color: { dark: '#6b472d', light: '#ffffff' } });
});

async function copyUrl() {
    await navigator.clipboard.writeText(publicUrl.value);
    copied.value = true;
    window.setTimeout(() => { copied.value = false; }, 1800);
}
</script>

<template>
    <button type="button" class="inline-flex flex-1 items-center justify-center gap-2 rounded-md border border-amber-700 bg-white px-3 py-2 text-sm font-semibold text-amber-900 shadow-sm transition hover:bg-amber-50 sm:flex-none sm:px-4 dark:border-amber-600 dark:bg-secondary dark:text-amber-50 dark:hover:bg-accent" @click="isOpen = true"><Share2 class="size-4" /> Bagikan kelas</button>
    <div v-if="isOpen" class="fixed inset-0 z-50 grid place-items-center bg-amber-950/50 p-3 sm:p-4" @click.self="isOpen = false">
        <section class="w-full max-w-md rounded-xl bg-white p-4 text-center shadow-2xl sm:p-6 dark:bg-card">
            <div class="flex items-start justify-between text-left"><div><p class="text-xs font-bold uppercase tracking-widest text-amber-700">Akses siswa</p><h2 class="text-xl font-bold text-amber-950 dark:text-amber-50">Bagikan kelas</h2></div><button type="button" title="Tutup" class="text-amber-900 dark:text-amber-100" @click="isOpen = false"><X class="size-5" /></button></div>
            <div class="mt-5 rounded-lg bg-amber-50 p-4 dark:bg-secondary"><img v-if="qrCode" :src="qrCode" alt="QR code kelas publik" class="mx-auto size-56" /><QrCode v-else class="mx-auto size-56 text-amber-800" /></div>
            <p class="mt-4 break-all rounded-md border bg-gray-50 px-3 py-2 text-xs text-gray-600 dark:border-border dark:bg-muted dark:text-muted-foreground">{{ publicUrl }}</p>
            <button type="button" class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-md bg-amber-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-900" @click="copyUrl"><Check v-if="copied" class="size-4" /><Copy v-else class="size-4" /> {{ copied ? 'Tautan tersalin' : 'Salin tautan' }}</button>
        </section>
    </div>
</template>
