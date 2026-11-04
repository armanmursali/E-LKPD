<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import PasskeyVerify from '@/components/PasskeyVerify.vue';

defineOptions({
    layout: {
        title: 'Masuk ke Akun Guru',
        description: 'Masukkan email dan kata sandi Anda untuk melanjutkan',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <PasskeyVerify />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            <a
                href="/auth/google"
                class="inline-flex w-full items-center justify-center gap-2.5 rounded-lg border border-amber-300 bg-amber-50/60 px-4 py-2.5 text-sm font-bold text-amber-950 transition hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100"
            >
                <span class="flex size-5 items-center justify-center rounded bg-amber-800 text-xs font-black text-white">G</span>
                Masuk dengan Google
            </a>

            <div class="relative flex items-center justify-center">
                <span class="h-px flex-1 bg-amber-200 dark:bg-border" />
                <span class="px-3 text-xs font-medium text-amber-900/60 dark:text-muted-foreground">atau dengan email</span>
                <span class="h-px flex-1 bg-amber-200 dark:bg-border" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Alamat Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="nama@sekolah.sch.id"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Kata Sandi</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm text-amber-800 dark:text-amber-300"
                        :tabindex="5"
                    >
                        Lupa kata sandi?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Ingat saya</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 w-full bg-amber-800 text-white hover:bg-amber-700"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Masuk
            </Button>
        </div>

        <div class="text-muted-foreground text-center text-sm">
            Belum punya akun?
            <TextLink :href="register()" :tabindex="5" class="font-semibold text-amber-800 dark:text-amber-300">Daftar sekarang</TextLink>
        </div>
    </Form>
</template>
