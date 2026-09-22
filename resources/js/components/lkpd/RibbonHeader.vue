<script setup lang="ts">
withDefaults(defineProps<{
    letter?: string;
    title: string;
    topText?: string;
    showLetter?: boolean;
    titleColor?: string;
    titleBgColor?: string;
    titleBorderColor?: string;
    titleFontSize?: string | number;
    titleFontWeight?: string | number;
    topTextColor?: string;
    topTextBgColor?: string;
    topTextBorderColor?: string;
    topTextFontSize?: string | number;
    topTextFontWeight?: string | number;
    letterColor?: string;
    letterBgColor?: string;
    letterBorderColor?: string;
    letterFontSize?: string | number;
    letterFontWeight?: string | number;
}>(), {
    topText: '',
    showLetter: false,
    titleColor: '#ffffff',
    titleBgColor: '#9a4b13',
    titleBorderColor: '#78350f',
    titleFontSize: '0.875rem',
    titleFontWeight: '700',
    topTextColor: '#9a4b13',
    topTextBgColor: '#fff7ed',
    topTextBorderColor: '#f59e0b',
    topTextFontSize: '0.75rem',
    topTextFontWeight: '600',
    letterColor: '#78350f',
    letterBgColor: '#ffffff',
    letterBorderColor: '#78350f',
    letterFontSize: '0.875rem',
    letterFontWeight: '700',
});
</script>

<template>
    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-3">
            <div
                v-if="topText"
                class="inline-flex items-center rounded-md px-3 py-1 text-left"
                :style="{
                    backgroundColor: topTextBgColor,
                    border: `1px solid ${topTextBorderColor}`,
                    color: topTextColor,
                    fontSize: topTextFontSize,
                    fontWeight: topTextFontWeight,
                }"
            >
                {{ topText }}
            </div>

            <span
                v-if="showLetter && letter"
                class="flex size-8 shrink-0 items-center justify-center rounded-full border-2"
                :style="{
                    backgroundColor: letterBgColor,
                    borderColor: letterBorderColor,
                    color: letterColor,
                    fontSize: letterFontSize,
                    fontWeight: letterFontWeight,
                }"
            >
                {{ letter }}
            </span>
            <span
                class="flex-1 rounded-md px-4 py-1.5 uppercase tracking-wide"
                :style="{
                    backgroundColor: titleBgColor,
                    border: `1px solid ${titleBorderColor}`,
                    color: titleColor,
                    fontSize: titleFontSize,
                    fontWeight: titleFontWeight,
                }"
            >
                {{ title }}
            </span>
        </div>
    </div>
</template>
