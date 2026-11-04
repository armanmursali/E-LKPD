<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Buat Akun Baru',
        description: 'Lengkapi data di bawah ini untuk mulai membuat LKPD interaktif',
    },
});
</script>

<template>
    <Head title="Daftar Akun" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            <a
                href="/auth/google"
                class="inline-flex w-full items-center justify-center gap-2.5 rounded-lg border border-amber-300 bg-amber-50/60 px-4 py-2.5 text-sm font-bold text-amber-950 transition hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100"
            >
                <span class="flex size-5 items-center justify-center rounded bg-amber-800 text-xs font-black text-white">G</span>
                Daftar dengan Google
            </a>

            <div class="relative flex items-center justify-center">
                <span class="h-px flex-1 bg-amber-200 dark:bg-border" />
                <span class="px-3 text-xs font-medium text-amber-900/60 dark:text-muted-foreground">atau isi formulir</span>
                <span class="h-px flex-1 bg-amber-200 dark:bg-border" />
            </div>

            <div class="grid gap-2">
                <Label for="name">Nama Lengkap</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Nama lengkap guru"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Alamat Email</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="nama@sekolah.sch.id"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Kata Sandi</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Buat kata sandi"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Ulangi kata sandi"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full bg-amber-800 text-white hover:bg-amber-700"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Buat Akun
            </Button>
        </div>

        <div class="text-muted-foreground text-center text-sm">
            Sudah punya akun?
            <TextLink
                :href="login()"
                class="font-semibold text-amber-800 underline underline-offset-4 dark:text-amber-300"
                :tabindex="6"
                >Masuk</TextLink
            >
        </div>
    </Form>
</template>
