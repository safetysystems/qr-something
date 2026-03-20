<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { formatDateTime } from '@/utils/formatters';
import { inspectionStatusClass, inspectionStatusLabel } from '@/utils/inspections';

defineOptions({
    layout: ClientLayout,
});

defineProps({
    workplace: {
        type: Object,
        required: true,
    },
    metrics: {
        type: Object,
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
    <Head :title="`${workplace.name} Dashboard`" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">{{ workplace.name }}</h2>
            <p class="page-subtitle mt-2">
                Workplace-level view of equipment and inspection activity for this client account.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <BaseButton
                :href="links.equipment"
                variant="secondary"
            >
                View equipment
            </BaseButton>
            <BaseButton :href="links.create_equipment">
                Add equipment
            </BaseButton>
        </div>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="stat-card">
            <p class="stat-label">Registered equipment</p>
            <p class="stat-value">{{ metrics.equipment }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Submitted inspections</p>
            <p class="stat-value">{{ metrics.inspections }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Available templates</p>
            <p class="stat-value">{{ metrics.templates }}</p>
        </div>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-2">
        <BaseCard>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Recent equipment</h2>
                    <p class="page-subtitle mt-1">Latest equipment records available inside this workplace.</p>
                </div>
                <Link
                    :href="links.equipment"
                    class="link-primary text-sm font-semibold"
                >
                    View all
                </Link>
            </div>

            <div class="mt-6">
                <div
                    v-for="item in recentEquipment"
                    :key="item.uuid"
                    class="list-row"
                >
                    <div class="flex w-full items-start justify-between gap-4">
                        <div>
                            <Link
                                :href="item.links.client_show"
                                class="font-semibold link-primary"
                            >
                                {{ item.name }}
                            </Link>
                            <p class="mt-1 text-sm text-muted">
                                {{ item.type }} · {{ item.location_label || 'Location not set' }}
                            </p>
                        </div>
                        <span class="badge">{{ item.asset_code || 'No asset code' }}</span>
                    </div>
                </div>

                <div
                    v-if="recentEquipment.length === 0"
                    class="empty-state"
                >
                    <p class="text-lg font-semibold">No equipment available</p>
                    <p class="page-subtitle mt-2">Equipment assigned to this workplace will appear here.</p>
                </div>
            </div>
        </BaseCard>

        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Recent inspections</h2>
                <p class="page-subtitle mt-1">Latest inspection activity within this workplace.</p>
            </div>

            <div class="mt-6">
                <div
                    v-for="inspection in recentInspections"
                    :key="inspection.uuid"
                    class="list-row"
                >
                    <div class="flex w-full items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold">{{ inspection.equipment?.name || 'Equipment' }}</p>
                            <p class="mt-1 text-sm text-muted">
                                {{ inspection.template?.name || 'Inspection' }} on {{ formatDateTime(inspection.inspected_at) }}
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
                    <p class="page-subtitle mt-2">Inspection activity for this workplace will appear here.</p>
                </div>
            </div>
        </BaseCard>
    </section>
</template>
