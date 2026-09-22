<script setup lang="ts">
import { ClipboardList, Presentation, Search, SearchCheck, Users } from '@lucide/vue';
import type { Component } from 'vue';

export type ModelPembelajaranItem = {
    nomor: number;
    icon: string;
    teks: string;
};

defineProps<{
    items: ModelPembelajaranItem[];
}>();

const iconMap: Record<string, Component> = {
    search: Search,
    'clipboard-list': ClipboardList,
    users: Users,
    presentation: Presentation,
    'search-check': SearchCheck,
};
</script>

<template>
    <div class="grid gap-4 sm:grid-cols-2">
        <div v-for="item in items" :key="item.nomor" class="relative rounded-md border border-amber-800/60 bg-white p-4 text-center">
            <span class="absolute -top-2 -left-2 flex size-6 items-center justify-center rounded-full bg-amber-800 text-[10px] font-bold text-white">
                {{ String(item.nomor).padStart(2, '0') }}
            </span>
            <div class="flex items-center justify-center gap-3">
                <component :is="iconMap[item.icon] ?? Search" class="size-6 shrink-0 text-amber-800" />
                <p class="text-left text-sm font-semibold text-amber-900">{{ item.teks }}</p>
            </div>
        </div>
    </div>
</template>
