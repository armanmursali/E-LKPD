import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { Component } from 'vue';

export type BreadcrumbItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
};

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    /** Font Awesome class or a Vue icon component. */
    icon?: string | Component;
    isActive?: boolean;
    items?: NavItem[];
};
