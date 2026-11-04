<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Brain, ExternalLink, Gamepad2, Pencil, Plus, Puzzle, Rocket, Trophy, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';

const iconMap = { 'gamepad-2': Gamepad2, puzzle: Puzzle, trophy: Trophy, brain: Brain, rocket: Rocket };
const iconLabels: Record<keyof typeof iconMap, string> = {
    'gamepad-2': 'Gamepad',
    puzzle: 'Puzzle',
    trophy: 'Trophy / Kuis',
    brain: 'Asah Otak',
    rocket: 'Tantangan Roket',
};
type Game = { id: number; nama: string; deskripsi: string | null; wordwall_url: string; ikon: keyof typeof iconMap };
const props = defineProps<{ kelas: { id: number; nama: string }; games: Game[]; ikonPilihan: (keyof typeof iconMap)[] }>();
const open = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({ nama: '', deskripsi: '', wordwall_url: '', ikon: 'gamepad-2' as keyof typeof iconMap });

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Daftar Kelas', href: '/kelas' },
            { title: 'Game' },
        ],
    },
});
function startAdd() { open.value = true; editingId.value = null; form.reset(); form.clearErrors(); }
function startEdit(game: Game) { open.value = true; editingId.value = game.id; form.defaults({ nama: game.nama, deskripsi: game.deskripsi ?? '', wordwall_url: game.wordwall_url, ikon: game.ikon }); form.reset(); }
function close() { open.value = false; editingId.value = null; form.reset(); form.clearErrors(); }
function submit() { const options = { onSuccess: close }; editingId.value ? form.put(`/kelas/${props.kelas.id}/game/${editingId.value}`, options) : form.post(`/kelas/${props.kelas.id}/game`, options); }
function remove(game: Game) { if (window.confirm(`Hapus game "${game.nama}"?`)) form.delete(`/kelas/${props.kelas.id}/game/${game.id}`); }
</script>

<template>
    <Head title="Game" />
    <div class="min-h-screen bg-amber-50/60 px-4 py-6 sm:px-6 md:px-8 dark:bg-background">
        <div class="mx-auto max-w-6xl space-y-6">
            <div>
                <Link
                    :href="`/kelas/${kelas.id}`"
                    class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-amber-900 shadow-xs transition hover:border-amber-300 hover:bg-amber-50 dark:border-border dark:bg-card dark:text-amber-100"
                >
                    <ArrowLeft class="size-3.5" />
                    Kembali ke {{ kelas.nama }}
                </Link>
            </div>

            <div class="flex flex-col justify-between gap-4 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end sm:p-6 dark:border-border dark:bg-card">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Aktivitas interaktif</p>
                    <h1 class="mt-1.5 text-2xl font-bold text-amber-950 sm:text-3xl dark:text-amber-50">Game {{ kelas.nama }}</h1>
                    <p class="mt-1 text-sm text-amber-900/70 dark:text-muted-foreground">Kelola permainan interaktif Wordwall untuk siswa.</p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-800 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700"
                    @click="startAdd"
                >
                    <Plus class="size-4" /> Tambah game
                </button>
            </div>

            <div v-if="games.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="game in games"
                    :key="game.id"
                    class="group flex flex-col justify-between rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-amber-400 hover:shadow-md dark:border-border dark:bg-card"
                >
                    <div>
                        <div class="flex items-start justify-between">
                            <span class="flex size-12 items-center justify-center rounded-2xl border border-amber-200 bg-amber-100 text-amber-800 shadow-inner transition group-hover:scale-105">
                                <component :is="iconMap[game.ikon] ?? Gamepad2" class="size-6" />
                            </span>
                            <div class="flex gap-1">
                                <button type="button" title="Edit game" class="rounded-lg border border-amber-200 p-2 text-amber-800 hover:bg-amber-50" @click="startEdit(game)">
                                    <Pencil class="size-3.5" />
                                </button>
                                <button type="button" title="Hapus game" class="rounded-lg border border-red-200 p-2 text-red-700 hover:bg-red-50" @click="remove(game)">
                                    <Trash2 class="size-3.5" />
                                </button>
                            </div>
                        </div>
                        <h2 class="mt-4 text-base font-bold text-amber-950 dark:text-amber-50">{{ game.nama }}</h2>
                        <p class="mt-1.5 min-h-[2.5rem] line-clamp-2 text-xs leading-relaxed text-amber-900/70 dark:text-muted-foreground">
                            {{ game.deskripsi || 'Game interaktif Wordwall untuk kelas ini.' }}
                        </p>
                    </div>
                    <a
                        :href="game.wordwall_url"
                        target="_blank"
                        rel="noreferrer"
                        class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-amber-800 px-3.5 py-2.5 text-xs font-bold text-white transition hover:bg-amber-700"
                    >
                        <span>Buka Wordwall</span>
                        <ExternalLink class="size-3.5" />
                    </a>
                </article>
            </div>
            <div v-else class="rounded-2xl border-2 border-dashed border-amber-300 bg-white/80 p-12 text-center text-amber-900 dark:border-border dark:bg-card dark:text-amber-100">
                <Gamepad2 class="mx-auto size-12 text-amber-700/70" />
                <p class="mt-3 font-bold">Belum ada game</p>
                <p class="mt-1 text-sm text-amber-900/70">Tambahkan game Wordwall pertama untuk meningkatkan semangat belajar siswa.</p>
            </div>
        </div>

        <div v-if="open" class="fixed inset-0 z-50 grid place-items-center bg-amber-950/50 p-4 backdrop-blur-xs" @click.self="close">
            <section class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-amber-700">{{ editingId ? 'Edit game' : 'Game baru' }}</p>
                        <h2 class="mt-0.5 text-xl font-bold text-amber-950 dark:text-amber-50">Permainan Wordwall</h2>
                    </div>
                    <button type="button" title="Tutup" class="rounded-lg p-1 hover:bg-amber-50" @click="close">
                        <X class="size-5 text-amber-900" />
                    </button>
                </div>
                <form class="mt-5 space-y-4" @submit.prevent="submit">
                    <label class="grid gap-1.5 text-sm font-semibold text-amber-950">
                        Nama game
                        <input v-model="form.nama" required class="rounded-lg border border-amber-300 px-3 py-2 text-sm font-normal outline-none focus:border-amber-600" placeholder="Contoh: Kuis Ekosistem" />
                        <small v-if="form.errors.nama" class="text-red-600">{{ form.errors.nama }}</small>
                    </label>
                    <label class="grid gap-1.5 text-sm font-semibold text-amber-950">
                        Deskripsi
                        <textarea v-model="form.deskripsi" rows="3" class="rounded-lg border border-amber-300 px-3 py-2 text-sm font-normal outline-none focus:border-amber-600" placeholder="Deskripsi singkat game"></textarea>
                        <small v-if="form.errors.deskripsi" class="text-red-600">{{ form.errors.deskripsi }}</small>
                    </label>
                    <label class="grid gap-1.5 text-sm font-semibold text-amber-950">
                        Link Wordwall
                        <input v-model="form.wordwall_url" required type="url" class="rounded-lg border border-amber-300 px-3 py-2 text-sm font-normal outline-none focus:border-amber-600" placeholder="https://wordwall.net/..." />
                        <small v-if="form.errors.wordwall_url" class="text-red-600">{{ form.errors.wordwall_url }}</small>
                    </label>
                    <div class="grid gap-1.5 text-sm font-semibold text-amber-950">
                        <span>Pilih Ikon Game</span>
                        <div class="grid grid-cols-5 gap-2">
                            <button
                                v-for="ikon in ikonPilihan"
                                :key="ikon"
                                type="button"
                                class="flex flex-col items-center gap-1 rounded-xl border p-2.5 text-xs transition"
                                :class="form.ikon === ikon ? 'border-amber-800 bg-amber-100 text-amber-950 ring-2 ring-amber-800/20' : 'border-amber-200 text-amber-900/70 hover:bg-amber-50'"
                                @click="form.ikon = ikon"
                            >
                                <component :is="iconMap[ikon] ?? Gamepad2" class="size-5" />
                                <span class="truncate text-[10px]">{{ iconLabels[ikon] ?? ikon }}</span>
                            </button>
                        </div>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full rounded-xl bg-amber-800 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-amber-700 disabled:opacity-60">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan game' }}
                    </button>
                </form>
            </section>
        </div>
    </div>
</template>
