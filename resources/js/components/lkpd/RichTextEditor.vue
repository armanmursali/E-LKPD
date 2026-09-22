<script setup lang="ts">
import { Bold, Italic, List, Underline } from '@lucide/vue';
import { onMounted, ref, watch } from 'vue';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const editorRef = ref<HTMLDivElement | null>(null);
const selectedColor = ref('#451a03');
const savedRange = ref<Range | null>(null);

onMounted(() => {
    if (editorRef.value) editorRef.value.innerHTML = props.modelValue;
});

watch(
    () => props.modelValue,
    (value) => {
        if (editorRef.value && editorRef.value.innerHTML !== value) {
            editorRef.value.innerHTML = value;
        }
    },
);

function onInput() {
    emit('update:modelValue', editorRef.value?.innerHTML ?? '');
}

function saveSelection() {
    const selection = window.getSelection();
    if (!selection || !selection.rangeCount || !editorRef.value?.contains(selection.anchorNode)) return;
    savedRange.value = selection.getRangeAt(0).cloneRange();
}

function format(command: 'bold' | 'italic' | 'underline' | 'insertUnorderedList' | 'foreColor', value?: string) {
    if (savedRange.value) {
        const selection = window.getSelection();
        selection?.removeAllRanges();
        selection?.addRange(savedRange.value);
    }
    editorRef.value?.focus();
    document.execCommand(command, false, value);
    onInput();
    saveSelection();
}
</script>

<template>
    <div class="overflow-hidden rounded-md border border-amber-300">
        <div class="flex items-center gap-1 border-b border-amber-200 bg-amber-50 px-2 py-1">
            <button type="button" class="rounded p-1 hover:bg-amber-100" title="Tebal" @mousedown="saveSelection" @click.prevent="format('bold')">
                <Bold class="size-3.5 text-amber-900" />
            </button>
            <button type="button" class="rounded p-1 hover:bg-amber-100" title="Garis bawah" @mousedown="saveSelection" @click.prevent="format('underline')">
                <Underline class="size-3.5 text-amber-900" />
            </button>
            <button type="button" class="rounded p-1 hover:bg-amber-100" title="Miring" @mousedown="saveSelection" @click.prevent="format('italic')">
                <Italic class="size-3.5 text-amber-900" />
            </button>
            <button type="button" class="rounded p-1 hover:bg-amber-100" title="Daftar titik" @mousedown="saveSelection" @click.prevent="format('insertUnorderedList')">
                <List class="size-3.5 text-amber-900" />
            </button>
            <label class="flex cursor-pointer items-center gap-1 rounded p-1 hover:bg-amber-100" title="Warna teks">
                <span class="text-xs font-semibold text-amber-900">A</span>
                <input v-model="selectedColor" type="color" class="size-4 cursor-pointer rounded border-0 p-0" @mousedown="saveSelection" @change="format('foreColor', selectedColor)" />
            </label>
        </div>
        <div
            ref="editorRef"
            class="min-h-24 max-w-full break-words px-3 py-2 text-sm [overflow-wrap:anywhere] focus:outline-none [&:empty]:before:text-stone-400 [&:empty]:before:content-[attr(data-placeholder)] [&_div]:my-1 [&_p]:my-1 [&_ul]:list-disc [&_ul]:pl-5 [&_li]:my-0.5"
            contenteditable="true"
            :data-placeholder="placeholder ?? ''"
            @input="onInput"
        />
    </div>
</template>
