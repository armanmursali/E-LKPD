<script setup lang="ts">
defineProps<{
    paragraf: string;
    gambar: string | null;
}>();
</script>

<template>
    <div class="flex flex-col items-center gap-8 md:flex-row md:items-start">
        <div v-if="gambar" class="w-full shrink-0 md:w-56">
            <img :src="gambar" alt="Foto profil penulis" class="mx-auto aspect-square w-full max-w-56 rounded-lg object-cover shadow-md" />
        </div>
        <div class="min-w-0 flex-1 space-y-4 text-justify text-base leading-relaxed text-foreground sm:text-lg">
            <p v-for="(teks, index) in paragraf.split(/\n\s*\n/).filter(Boolean)" :key="index" class="m-0">{{ teks }}</p>
            <p v-if="!paragraf.trim()" class="text-center text-muted-foreground">Belum ada biografi penulis.</p>
        </div>
    </div>
</template>
