<script setup lang="ts">
defineProps<{
    paragraphs: string[];
}>();
</script>

<template>
    <div class="space-y-4 text-justify text-sm leading-relaxed text-amber-950 sm:text-base">
        <p v-for="(paragraf, index) in paragraphs" :key="index">{{ paragraf }}</p>
    </div>
</template>
