<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import BaseFieldError from '@/Components/Base/BaseFieldError.vue';
import BaseInput from '@/Components/Base/BaseInput.vue';
import BaseLabel from '@/Components/Base/BaseLabel.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineOptions({
    layout: GuestLayout,
});

defineProps({
    links: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login');
}
</script>

<template>
    <Head title="Sign In" />

    <BaseCard class="auth-panel">
        <p class="brand-kicker">Equipment Inspection</p>
        <h1 class="page-title mt-4">Secure platform sign in</h1>
        <p class="page-subtitle mt-3">
            Sign in as a platform administrator or workplace client. Sessions are server-side and rate limited.
        </p>

        <form
            class="mt-8 space-y-5"
            @submit.prevent="submit"
        >
            <div>
                <BaseLabel for-id="email">Email address</BaseLabel>
                <BaseInput
                    id="email"
                    v-model="form.email"
                    name="email"
                    type="email"
                    autocomplete="username"
                    required
                    autofocus
                />
                <BaseFieldError :message="form.errors.email" />
            </div>

            <div>
                <BaseLabel for-id="password">Password</BaseLabel>
                <BaseInput
                    id="password"
                    v-model="form.password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                />
                <BaseFieldError :message="form.errors.password" />
            </div>

            <label class="flex items-center gap-3 text-sm text-muted">
                <input
                    v-model="form.remember"
                    type="checkbox"
                    class="h-4 w-4 rounded border-default"
                >
                Keep this device signed in
            </label>

            <BaseButton
                type="submit"
                class="w-full justify-center"
                :disabled="form.processing"
            >
                Sign in
            </BaseButton>
        </form>

        <div class="auth-links mt-6">
            <Link
                :href="links.register"
                class="link-primary text-sm font-medium"
            >
                Need a workplace account? Register
            </Link>
            <Link
                :href="links.home"
                class="text-sm text-muted"
            >
                Back to website
            </Link>
        </div>
    </BaseCard>
</template>
