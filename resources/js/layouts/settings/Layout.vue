<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profil',
        href: editProfile(),
    },
    {
        title: 'Keamanan',
        href: editSecurity(),
    },
    {
        title: 'Tampilan',
        href: editAppearance(),
    },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <div class="min-h-full bg-amber-50/60 px-4 py-6 sm:px-6 lg:px-8 dark:bg-background">
        <div class="mx-auto max-w-5xl space-y-6">
            <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6 dark:border-border dark:bg-card">
                <Heading
                    title="Pengaturan Akun"
                    description="Kelola profil, kata sandi, keamanan, dan tampilan ruang kerja Anda"
                />
            </div>

            <div class="flex flex-col gap-6 lg:flex-row">
                <aside class="w-full lg:w-56 shrink-0">
                    <nav
                        class="flex flex-row gap-1 overflow-x-auto rounded-xl border border-amber-200 bg-white p-2 shadow-sm lg:flex-col dark:border-border dark:bg-card"
                        aria-label="Pengaturan"
                    >
                        <Button
                            v-for="item in sidebarNavItems"
                            :key="toUrl(item.href)"
                            variant="ghost"
                            :class="[
                                'w-full justify-start font-semibold',
                                {
                                    'bg-amber-100 text-amber-950 hover:bg-amber-100 dark:bg-accent dark:text-amber-50':
                                        isCurrentOrParentUrl(item.href),
                                },
                            ]"
                            as-child
                        >
                            <Link :href="item.href">
                                <component :is="item.icon" class="h-4 w-4" />
                                {{ item.title }}
                            </Link>
                        </Button>
                    </nav>
                </aside>

                <Separator class="hidden" />

                <div class="flex-1 rounded-2xl border border-amber-200 bg-white p-6 shadow-sm sm:p-8 dark:border-border dark:bg-card">
                    <section class="max-w-xl space-y-10">
                        <slot />
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
