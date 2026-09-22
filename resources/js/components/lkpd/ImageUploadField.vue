<script setup lang="ts">
import { ImagePlus, Loader2 } from '@lucide/vue';
import { ref } from 'vue';
import { uploadFile } from '@/lib/upload';

const props = defineProps<{
    modelValue: string;
    uploadUrl: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const uploading = ref(false);
const error = ref<string | null>(null);

async function onFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    uploading.value = true;
    error.value = null;

    try {
        const result = await uploadFile(props.uploadUrl, file);
        emit('update:modelValue', result.url);
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Gagal mengunggah gambar.';
    } finally {
        uploading.value = false;
        input.value = '';
    }
}
</script>

<template>
    <div class="space-y-2">
        <div v-if="modelValue" class="overflow-hidden rounded-md border border-amber-300">
            <img :src="modelValue" alt="" class="max-h-40 w-full object-cover" />
        </div>
        <label
            class="flex cursor-pointer items-center justify-center gap-2 rounded-md border border-dashed border-amber-400 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-800 hover:bg-amber-100"
        >
            <Loader2 v-if="uploading" class="size-4 animate-spin" />
            <ImagePlus v-else class="size-4" />
            {{ uploading ? 'Mengunggah...' : modelValue ? 'Ganti gambar dari perangkat' : 'Unggah gambar dari perangkat' }}
            <input type="file" accept="image/*" class="hidden" :disabled="uploading" @change="onFileChange" />
        </label>
        <p v-if="error" class="text-xs text-red-600">{{ error }}</p>
    </div>
</template>
