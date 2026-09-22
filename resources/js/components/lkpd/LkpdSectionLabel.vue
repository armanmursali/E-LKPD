<script setup lang="ts">
defineProps<{
    label: string;
}>();
</script>

<template>
    <div class="mb-8 flex items-center justify-center gap-3 sm:gap-4">
        <span class="flex size-9 items-center justify-center rounded-md bg-amber-800 sm:size-10">
            <span class="size-3.5 rounded-sm bg-amber-300 sm:size-4" />
        </span>
        <span class="rounded-md border-2 border-dashed border-amber-400 bg-amber-800 px-6 py-2 text-xs font-bold tracking-wider text-white uppercase sm:px-8 sm:text-sm dark:border-amber-500 dark:bg-amber-700">
            {{ label }}
        </span>
        <span class="flex size-9 items-center justify-center rounded-md bg-amber-800 sm:size-10">
            <span class="size-3.5 rounded-sm bg-amber-300 sm:size-4" />
        </span>
    </div>
</template>
