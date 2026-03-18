<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

defineProps({
    links: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user ?? null);
const dashboardLabel = computed(() => authUser.value?.is_super_admin ? 'Open admin dashboard' : 'Open workplace dashboard');
</script>

<template>
    <Head title="Equipment Inspection" />

    <section class="public-hero">
        <div>
            <p class="brand-kicker">Public Website</p>
            <h1 class="public-hero-title mt-4">Set up your workplace inspection portal without waiting on manual onboarding.</h1>
            <p class="public-hero-copy mt-5">
                Register your company account, create your workplace, and start managing equipment from a dedicated client dashboard built separately from the platform admin surface.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <template v-if="authUser">
                    <BaseButton :href="links.portal">
                        {{ dashboardLabel }}
                    </BaseButton>
                </template>
                <template v-else>
                    <BaseButton :href="links.register">
                        Create workplace account
                    </BaseButton>
                    <BaseButton
                        :href="links.login"
                        variant="secondary"
                    >
                        Sign in
                    </BaseButton>
                </template>
            </div>

            <div class="public-bullet-list mt-10">
                <p>Separate workplace dashboard for clients</p>
                <p>Equipment registry and inspection history</p>
                <p>QR-ready asset structure for field workflows</p>
            </div>
        </div>

        <BaseCard class="public-hero-panel">
            <div class="public-hero-panel-section">
                <p class="stat-label">Client onboarding</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight">Self-serve setup</h2>
                <p class="page-subtitle mt-3">
                    New accounts create both the user and the first workplace in one flow, then land directly in the client portal.
                </p>
            </div>

            <div class="public-stack mt-8">
                <div class="public-step-card">
                    <p class="public-step-number">01</p>
                    <h3 class="mt-3 text-base font-semibold">Register the account</h3>
                    <p class="page-subtitle mt-2">Create the first workplace owner account with a strong password.</p>
                </div>

                <div class="public-step-card">
                    <p class="public-step-number">02</p>
                    <h3 class="mt-3 text-base font-semibold">Provision the workplace</h3>
                    <p class="page-subtitle mt-2">The system creates the initial workplace profile and ownership link automatically.</p>
                </div>

                <div class="public-step-card">
                    <p class="public-step-number">03</p>
                    <h3 class="mt-3 text-base font-semibold">Enter the client dashboard</h3>
                    <p class="page-subtitle mt-2">Workplace users land in a separate dashboard from the super admin area.</p>
                </div>
            </div>
        </BaseCard>
    </section>

    <section class="public-section">
        <div class="public-section-heading">
            <p class="brand-kicker">Core Features</p>
            <h2 class="page-title mt-3">Built for workplace operations, not a generic asset list.</h2>
        </div>

        <div class="public-feature-grid mt-8">
            <BaseCard>
                <h3 class="text-lg font-semibold">Workplace account setup</h3>
                <p class="page-subtitle mt-3">
                    Public registration creates the owner account and the first workplace in one transaction.
                </p>
            </BaseCard>

            <BaseCard>
                <h3 class="text-lg font-semibold">Client-specific dashboard</h3>
                <p class="page-subtitle mt-3">
                    Workplace users see a dedicated portal instead of the super admin interface.
                </p>
            </BaseCard>

            <BaseCard>
                <h3 class="text-lg font-semibold">Inspection-ready registry</h3>
                <p class="page-subtitle mt-3">
                    Equipment, inspections, and QR workflows are structured to support field usage as the product grows.
                </p>
            </BaseCard>
        </div>
    </section>
</template>
