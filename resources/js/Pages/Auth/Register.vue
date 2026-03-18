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
    name: '',
    email: '',
    workplace_name: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/register');
}
</script>

<template>
    <Head title="Create Workplace Account" />

    <BaseCard class="auth-panel">
        <p class="brand-kicker">Create Workplace Account</p>
        <h1 class="page-title mt-4">Set up your client portal</h1>
        <p class="page-subtitle mt-3">
            This creates the first workplace owner account and provisions the initial workplace automatically.
        </p>

        <form
            class="mt-8 space-y-5"
            @submit.prevent="submit"
        >
            <div>
                <BaseLabel for-id="name">Full name</BaseLabel>
                <BaseInput
                    id="name"
                    v-model="form.name"
                    name="name"
                    type="text"
                    autocomplete="name"
                    required
                    autofocus
                />
                <BaseFieldError :message="form.errors.name" />
            </div>

            <div>
                <BaseLabel for-id="email">Work email</BaseLabel>
                <BaseInput
                    id="email"
                    v-model="form.email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                />
                <BaseFieldError :message="form.errors.email" />
            </div>

            <div>
                <BaseLabel for-id="workplace_name">Workplace name</BaseLabel>
                <BaseInput
                    id="workplace_name"
                    v-model="form.workplace_name"
                    name="workplace_name"
                    type="text"
                    autocomplete="organization"
                    required
                />
                <BaseFieldError :message="form.errors.workplace_name" />
            </div>

            <div>
                <BaseLabel for-id="password">Password</BaseLabel>
                <BaseInput
                    id="password"
                    v-model="form.password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                />
                <BaseFieldError :message="form.errors.password" />
            </div>

            <div>
                <BaseLabel for-id="password_confirmation">Confirm password</BaseLabel>
                <BaseInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                />
            </div>

            <BaseButton
                type="submit"
                class="w-full justify-center"
                :disabled="form.processing"
            >
                Create workplace account
            </BaseButton>
        </form>

        <div class="auth-links mt-6">
            <Link
                :href="links.login"
                class="link-primary text-sm font-medium"
            >
                Already have an account? Sign in
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
