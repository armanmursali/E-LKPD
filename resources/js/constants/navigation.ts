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
        isActive: true,
        items: [
            {
                title: 'Daftar Kelas',
                href: '/kelas',
            },
            {
                title: 'Statistik Nilai',
                href: '/statistic',
            },
        ],
    },
];

export const footerNavItems: NavItem[] = [];
