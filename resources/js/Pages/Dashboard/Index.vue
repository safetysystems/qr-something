<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDateTime } from '@/utils/formatters';
import { inspectionStatusClass, inspectionStatusLabel } from '@/utils/inspections';

defineOptions({
    layout: AppLayout,
});

defineProps({
    metrics: {
        type: Object,
        required: true,
    },
    recentWorkplaces: {
        type: Array,
        required: true,
    },
    recentEquipment: {
        type: Array,
        required: true,
    },
    recentInspections: {
        type: Array,
        required: true,
    },
    links: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Platform snapshot</h2>
            <p class="page-subtitle mt-2">
                A concise view of the core registry and recent activity.
            </p>
        </div>

        <BaseButton :href="links.create_workplace">
            Add workplace
        </BaseButton>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="stat-card">
            <p class="stat-label">Active workplaces</p>
            <p class="stat-value">{{ metrics.workplaces }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Registered equipment</p>
            <p class="stat-value">{{ metrics.equipment }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Submitted inspections</p>
            <p class="stat-value">{{ metrics.inspections }}</p>
        </div>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-3">
        <BaseCard>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Recent workplaces</h2>
                    <p class="page-subtitle mt-1">Newest environments in the registry.</p>
                </div>
                <Link
                    :href="links.workplaces"
                    class="link-primary text-sm font-semibold"
                >View all</Link>
            </div>

            <div class="mt-6">
                <div
                    v-for="workplace in recentWorkplaces"
                    :key="workplace.uuid"
                    class="list-row"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <Link
                                :href="workplace.links.show"
                                class="font-semibold link-primary"
                            >
                                {{ workplace.name }}
                            </Link>
                            <p class="mt-1 text-sm text-muted">
                                {{ workplace.equipment_count ?? 0 }} equipment linked
                            </p>
                        </div>
                        <span
                            v-if="workplace.code"
                            class="badge"
                        >
                            {{ workplace.code }}
                        </span>
                    </div>
                </div>

                <div
                    v-if="recentWorkplaces.length === 0"
                    class="empty-state"
                >
                    <p class="text-lg font-semibold">No workplaces yet</p>
                    <p class="page-subtitle mt-2">Create the first workplace to start structuring the platform.</p>
                </div>
            </div>
        </BaseCard>

        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Recent equipment</h2>
                <p class="page-subtitle mt-1">Latest asset records created in the system.</p>
            </div>

            <div class="mt-6">
                <div
                    v-for="item in recentEquipment"
                    :key="item.uuid"
                    class="list-row"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <Link
                                :href="item.links.show"
                                class="font-semibold link-primary"
                            >
                                {{ item.name }}
                            </Link>
                            <p class="mt-1 text-sm text-muted">
                                {{ item.type }} at {{ item.workplace?.name }}
                            </p>
                        </div>
                        <span class="badge">{{ item.uuid }}</span>
                    </div>
                </div>

                <div
                    v-if="recentEquipment.length === 0"
                    class="empty-state"
                >
                    <p class="text-lg font-semibold">No equipment yet</p>
                    <p class="page-subtitle mt-2">Add equipment inside a workplace to start building your registry.</p>
                </div>
            </div>
        </BaseCard>

        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Recent inspections</h2>
                <p class="page-subtitle mt-1">Latest checklist submissions across all workplaces.</p>
            </div>

            <div class="mt-6">
                <div
                    v-for="inspection in recentInspections"
                    :key="inspection.uuid"
                    class="list-row"
                >
                    <div class="flex w-full items-start justify-between gap-4">
                        <div>
                            <Link
                                :href="inspection.links.show"
                                class="font-semibold link-primary"
                            >
                                {{ inspection.equipment?.name || 'Equipment' }}
                            </Link>
                            <p class="mt-1 text-sm text-muted">
                                {{ inspection.template?.name || 'Template' }} on {{ formatDateTime(inspection.inspected_at) }}
                            </p>
                        </div>
                        <span :class="inspectionStatusClass(inspection.status)">
                            {{ inspectionStatusLabel(inspection.status) }}
                        </span>
                    </div>
                </div>

                <div
                    v-if="recentInspections.length === 0"
                    class="empty-state"
                >
                    <p class="text-lg font-semibold">No inspections yet</p>
                    <p class="page-subtitle mt-2">Submit the first checklist to start building inspection history.</p>
                </div>
            </div>
        </BaseCard>
    </section>
</template>
