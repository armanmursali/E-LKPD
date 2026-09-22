<script setup lang="ts">
export type DaftarPustakaItem = {
    nomor: number;
    referensi: string;
};

defineProps<{
    items: DaftarPustakaItem[];
}>();
</script>

<template>
    <div v-if="items.length" class="space-y-4 text-center text-base leading-relaxed text-foreground sm:text-lg">
        <p v-for="item in items" :key="item.nomor" class="m-0">{{ item.referensi }}</p>
    </div>
    <p v-else class="py-12 text-center text-muted-foreground">Belum ada daftar pustaka.</p>
</template>