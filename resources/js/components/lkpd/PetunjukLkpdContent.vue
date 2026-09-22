<script setup lang="ts">
import { BookOpen, IdCard, ListChecks, MessageCircleQuestion, PencilLine, Target } from '@lucide/vue';
import type { Component } from 'vue';

export type PetunjukItem = {
    nomor: number;
    icon: string;
    teks: string;
};

defineProps<{
    items: PetunjukItem[];
}>();

const iconMap: Record<string, Component> = {
    'id-card': IdCard,
    'book-open': BookOpen,
    target: Target,
    'list-checks': ListChecks,
    'pencil-line': PencilLine,
    'message-question': MessageCircleQuestion,
};
</script>

<template>
    <div class="grid gap-4 sm:grid-cols-2">
        <div v-for="item in items" :key="item.nomor" class="relative flex items-stretch gap-0">
            <span class="absolute -top-2 -left-2 z-10 flex size-6 items-center justify-center rounded-full bg-amber-800 text-xs font-bold text-white">
                {{ item.nomor }}
            </span>
            <span class="flex w-16 shrink-0 items-center justify-center rounded-l-md border border-amber-800 bg-white">
                <component :is="iconMap[item.icon] ?? IdCard" class="size-7 text-amber-800" />
            </span>
            <span class="flex flex-1 items-center rounded-r-md bg-amber-800 px-3 py-3 text-xs font-medium text-white sm:text-sm">
                {{ item.teks }}
            </span>
        </div>
    </div>
</template>
