<script setup lang="ts">
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import { SidebarTrigger } from "@/components/ui/sidebar";
import { router, usePage } from "@inertiajs/vue3";
import { Bell, X } from "@lucide/vue";
import { computed, onMounted, onUnmounted, ref } from "vue";
import type { BreadcrumbItem } from "@/types";

withDefaults(
  defineProps<{
    breadcrumbs?: BreadcrumbItem[];
  }>(),
  {
    breadcrumbs: () => [],
  }
);

type NotificationItem = {
  id: string;
  title: string;
  message: string;
  url: string;
  read: boolean;
  createdAt: string | null;
};

const page = usePage();
const notifications = computed(
  () =>
    (page.props.notifications ?? { unreadCount: 0, items: [] }) as {
      unreadCount: number;
      items: NotificationItem[];
    }
);
const notificationOpen = ref(false);
let notificationPoll: ReturnType<typeof setInterval> | undefined;

function openNotification(notification: NotificationItem) {
  notificationOpen.value = false;

  if (!notification.read) {
    router.post(
      `/notifications/${notification.id}/read`,
      {},
      {
        preserveScroll: true,
        onSuccess: () => router.visit(notification.url),
      }
    );
    return;
  }

  router.visit(notification.url);
}

function formatNotificationDate(value: string | null) {
  return value
    ? new Date(value).toLocaleString("id-ID", { dateStyle: "short", timeStyle: "short" })
    : "";
}

onMounted(() => {
  notificationPoll = setInterval(() => {
    router.reload({ only: ["notifications"] });
  }, 15000);
});

onUnmounted(() => {
  if (notificationPoll) clearInterval(notificationPoll);
});
</script>

<template>
  <header
    class="border-sidebar-border/70 flex h-16 shrink-0 items-center gap-2 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
  >
    <div class="flex items-center gap-2">
      <SidebarTrigger class="-ml-1" />
      <template v-if="breadcrumbs && breadcrumbs.length > 0">
        <Breadcrumbs :breadcrumbs="breadcrumbs" />
      </template>
    </div>
    <div class="relative ml-auto">
      <button
        type="button"
        aria-label="Buka notifikasi"
        class="relative flex size-9 items-center justify-center rounded-md text-foreground transition hover:bg-muted"
        @click="notificationOpen = true"
      >
        <Bell class="size-5" />
        <span
          v-if="notifications.unreadCount"
          class="absolute -top-1 -right-1 flex min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-5 text-white"
        >
          {{ notifications.unreadCount > 99 ? "99+" : notifications.unreadCount }}
        </span>
      </button>
    </div>
  </header>
  <div v-if="notificationOpen" class="fixed inset-0 z-50 bg-black/25">
    <aside
      class="absolute inset-y-0 right-0 flex h-full w-full flex-col bg-background shadow-2xl sm:w-96 sm:border-l"
    >
      <div class="flex h-16 shrink-0 items-center justify-between border-b px-5">
        <div>
          <h2 class="font-bold text-foreground">Notifikasi</h2>
          <p class="text-xs text-muted-foreground">
            {{ notifications.unreadCount }} belum dibaca
          </p>
        </div>
        <button
          type="button"
          aria-label="Tutup notifikasi"
          class="flex size-9 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground"
          @click="notificationOpen = false"
        >
          <X class="size-5" />
        </button>
      </div>
      <div
        v-if="!notifications.items.length"
        class="flex flex-1 items-center justify-center px-6 text-center text-sm text-muted-foreground"
      >
        Belum ada notifikasi jawaban siswa.
      </div>
      <div v-else class="flex-1 overflow-y-auto">
        <button
          v-for="notification in notifications.items"
          :key="notification.id"
          type="button"
          class="flex w-full gap-3 border-b px-5 py-4 text-left transition hover:bg-muted/60"
          :class="notification.read ? 'bg-background' : 'bg-amber-50/70'"
          @click="openNotification(notification)"
        >
          <span
            class="mt-1 size-2 shrink-0 rounded-full"
            :class="notification.read ? 'bg-transparent' : 'bg-red-600'"
          />
          <span class="min-w-0">
            <span class="block text-sm font-bold text-foreground">{{
              notification.title
            }}</span>
            <span class="mt-1 block text-sm text-muted-foreground">{{
              notification.message
            }}</span>
            <span class="mt-2 block text-xs text-muted-foreground">{{
              formatNotificationDate(notification.createdAt)
            }}</span>
          </span>
        </button>
      </div>
    </aside>
  </div>
</template>
