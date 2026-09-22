<script setup lang="ts">
defineProps<{
    jenisPengerjaan: 'individu' | 'kelompok';
}>();
</script>

<template>
    <div class="space-y-2 border-b border-amber-300 pb-3 text-sm text-amber-950">
        <label class="block">
            <span class="font-semibold">Nama Siswa</span>
            <input type="text" disabled class="mt-1 w-full rounded-md border border-amber-300 bg-white px-2 py-1 text-sm" />
        </label>
        <label v-if="jenisPengerjaan === 'kelompok'" class="block">
            <span class="font-semibold">Nama Kelompok</span>
            <input type="text" disabled class="mt-1 w-full rounded-md border border-amber-300 bg-white px-2 py-1 text-sm" />
        </label>
    </div>
</template>
