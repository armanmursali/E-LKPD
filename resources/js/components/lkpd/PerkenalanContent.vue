<script setup lang="ts">
defineProps<{
    sampul: {
        judul: string;
        mapel: string;
        jenjang: string;
        kelas_label: string;
        fase: string;
        kurikulum: string;
        penulis: string;
        pembimbing: string[];
        validator_media: string;
        validator_materi: string;
    };
}>();
</script>

<template>
    <div class="text-center">
        <h1 class="text-lg font-bold text-amber-900 sm:text-xl">{{ sampul.judul }}</h1>

        <p class="mt-6 text-sm font-semibold text-amber-800 sm:text-base">Untuk {{ sampul.mapel }} {{ sampul.jenjang }}</p>
        <div class="mt-2 flex items-center justify-center overflow-hidden rounded-md border border-amber-800 text-xs font-bold sm:text-sm">
            <span class="px-3 py-1 text-amber-900 uppercase">{{ sampul.kelas_label }}</span>
            <span class="bg-amber-800 px-3 py-1 text-white uppercase">{{ sampul.fase }}</span>
        </div>
        <p class="mt-3 text-sm font-semibold text-amber-800 sm:text-base">{{ sampul.kurikulum }}</p>

        <div class="relative mx-auto mt-8 max-w-lg border border-amber-800/70 px-6 py-6 sm:mt-10 sm:px-10 sm:py-8">
            <span class="absolute -top-2 -left-2 size-4 rounded-full bg-amber-800" />
            <span class="absolute -top-2 -right-2 size-4 rounded-full bg-amber-800" />
            <span class="absolute -bottom-2 -left-2 size-4 rounded-full bg-amber-800" />
            <span class="absolute -right-2 -bottom-2 size-4 rounded-full bg-amber-800" />

            <dl class="space-y-2 text-left text-xs sm:text-sm">
                <div class="flex gap-2">
                    <dt class="w-36 shrink-0 font-bold text-amber-900 sm:w-40">Penulis</dt>
                    <dd class="text-amber-900">: {{ sampul.penulis }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-36 shrink-0 font-bold text-amber-900 sm:w-40">Pembimbing</dt>
                    <dd class="text-amber-900">
                        <span class="mr-1">:</span>
                        <span class="inline-flex flex-col gap-1">
                            <span v-for="(pembimbing, index) in sampul.pembimbing" :key="index">{{ pembimbing }}</span>
                        </span>
                    </dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-36 shrink-0 font-bold text-amber-900 sm:w-40">Validator Ahli Media</dt>
                    <dd class="text-amber-900">: {{ sampul.validator_media }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-36 shrink-0 font-bold text-amber-900 sm:w-40">Validator Ahli Materi</dt>
                    <dd class="text-amber-900">: {{ sampul.validator_materi }}</dd>
                </div>
            </dl>
        </div>
    </div>
</template>
