import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

export const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: 'fa-solid fa-home',
    },
    {
        title: 'Pembelajaran',
        href: '#',
        icon: 'fa-solid fa-layer-group',
        items: [
            {
                title: 'Kelas',
                href: '/kelas',
            },
            {
                title: 'Statistic',
                href: '/statistic',
            },
        ],
    },
];

export const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: 'fa-brands fa-github',
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: 'fa-solid fa-book-open',
    },
];
