<script setup>
import { Head } from '@inertiajs/vue3';
import BaseCard from '@/Components/Base/BaseCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout,
});

defineProps({
    summary: {
        type: Object,
        required: true,
    },
    phases: {
        type: Array,
        required: true,
    },
    features: {
        type: Array,
        required: true,
    },
    focus: {
        type: Array,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
});

function statusLabel(status) {
    if (status === 'done') {
        return 'Done';
    }

    if (status === 'in_progress') {
        return 'In progress';
    }

    return 'Planned';
}

function statusClass(status) {
    if (status === 'done') {
        return 'badge-success';
    }

    if (status === 'in_progress') {
        return 'badge-warning';
    }

    return 'badge';
}
</script>

<template>
    <Head title="Project Tracker" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Roadmap</p>
            <h2 class="page-title mt-2">{{ summary.title }}</h2>
            <p class="page-subtitle mt-3">{{ summary.description }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="stat-card p-4">
                <p class="stat-label">Done</p>
                <p class="mt-2 text-2xl font-semibold">{{ stats.done }}</p>
            </div>
            <div class="stat-card p-4">
                <p class="stat-label">In progress</p>
                <p class="mt-2 text-2xl font-semibold">{{ stats.in_progress }}</p>
            </div>
            <div class="stat-card p-4">
                <p class="stat-label">Planned</p>
                <p class="mt-2 text-2xl font-semibold">{{ stats.planned }}</p>
            </div>
            <div class="stat-card p-4">
                <p class="stat-label">Total</p>
                <p class="mt-2 text-2xl font-semibold">{{ stats.total }}</p>
            </div>
        </div>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[1.15fr,0.85fr]">
        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Feature checklist</h2>
                <p class="page-subtitle mt-2">Temporary progress list for the current build-out.</p>
            </div>

            <div class="mt-6 space-y-3">
                <div
                    v-for="feature in features"
                    :key="feature.label"
                    class="list-row first:pt-0"
                >
                    <div class="flex items-center justify-between gap-4">
                        <p class="font-medium">{{ feature.label }}</p>
                        <span :class="statusClass(feature.status)">
                            {{ statusLabel(feature.status) }}
                        </span>
                    </div>
                </div>
            </div>
        </BaseCard>

        <div class="space-y-6">
            <BaseCard>
                <div>
                    <h2 class="text-xl font-semibold">Current focus</h2>
                    <p class="page-subtitle mt-2">Use this to keep active work constrained.</p>
                </div>

                <div class="mt-6 space-y-3">
                    <div
                        v-for="item in focus"
                        :key="item"
                        class="list-row first:pt-0"
                    >
                        <p class="font-medium">{{ item }}</p>
                    </div>
                </div>
            </BaseCard>

            <BaseCard>
                <div>
                    <h2 class="text-xl font-semibold">Phases</h2>
                    <p class="page-subtitle mt-2">High-level roadmap for the next build stages.</p>
                </div>

                <div class="mt-6 space-y-5">
                    <div
                        v-for="phase in phases"
                        :key="phase.title"
                        class="rounded-2xl border p-4"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-base font-semibold">{{ phase.title }}</h3>
                            <span :class="statusClass(phase.status)">
                                {{ statusLabel(phase.status) }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-2">
                            <p
                                v-for="item in phase.items"
                                :key="item"
                                class="text-sm text-muted"
                            >
                                {{ item }}
                            </p>
                        </div>
                    </div>
                </div>
            </BaseCard>
        </div>
    </section>
</template>
