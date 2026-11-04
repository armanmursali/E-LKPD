<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-amber-50/60 p-6 md:p-10 dark:bg-background"
    >
        <div class="w-full max-w-md rounded-2xl border border-amber-200 bg-white p-6 shadow-xl sm:p-8 dark:border-border dark:bg-card">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col items-center gap-3">
                    <Link
                        :href="home()"
                        class="flex items-center gap-2.5 font-medium"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 p-1.5 shadow-xs dark:bg-amber-950/60"
                        >
                            <AppLogoIcon
                                class="size-8 object-contain"
                            />
                        </div>
                        <span class="text-sm font-black tracking-[0.16em] text-amber-950 dark:text-amber-100">E-LKPD WEB</span>
                    </Link>
                    <div class="space-y-1.5 text-center">
                        <h1 class="text-xl font-bold text-amber-950 dark:text-amber-50">{{ title }}</h1>
                        <p class="text-center text-sm text-amber-900/70 dark:text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
