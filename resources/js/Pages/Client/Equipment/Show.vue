<script setup>
import { Head } from '@inertiajs/vue3';
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
    equipment: {
        type: Object,
        required: true,
    },
    recentInspections: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head :title="equipment.name" />

    <section>
        <div class="flex flex-wrap items-center gap-3">
            <p class="brand-kicker">Equipment</p>
            <span class="badge">{{ equipment.type }}</span>
        </div>
        <h1 class="page-title mt-2">{{ equipment.name }}</h1>
        <p class="page-subtitle mt-3">
            Read-only equipment view for {{ workplace.name }}.
        </p>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[1fr,1.05fr]">
        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Equipment details</h2>
                <p class="page-subtitle mt-2">Core equipment data available to the workplace team.</p>
            </div>

            <dl class="detail-list mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <dt>UUID</dt>
                    <dd>{{ equipment.uuid }}</dd>
                </div>
                <div>
                    <dt>Workplace</dt>
                    <dd>{{ workplace.name }}</dd>
                </div>
                <div>
                    <dt>Asset code</dt>
                    <dd>{{ equipment.asset_code || 'Not set' }}</dd>
                </div>
                <div>
                    <dt>Serial number</dt>
                    <dd>{{ equipment.serial_number || 'Not set' }}</dd>
                </div>
                <div>
                    <dt>Manufacturer</dt>
                    <dd>{{ equipment.manufacturer || 'Not set' }}</dd>
                </div>
                <div>
                    <dt>Model</dt>
                    <dd>{{ equipment.model || 'Not set' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt>Location label</dt>
                    <dd>{{ equipment.location_label || 'Not set' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt>Notes</dt>
                    <dd>{{ equipment.notes || 'No notes recorded.' }}</dd>
                </div>
                <div>
                    <dt>Created by</dt>
                    <dd>{{ equipment.created_by?.name || 'System' }}</dd>
                </div>
                <div>
                    <dt>Created at</dt>
                    <dd>{{ formatDateTime(equipment.created_at) }}</dd>
                </div>
            </dl>
        </BaseCard>

        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">QR route</h2>
                <p class="page-subtitle mt-2">This is the route currently encoded into the equipment QR code.</p>
            </div>

            <div class="mt-6 space-y-4">
                <div class="card card-muted p-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-muted">Encoded URL</p>
                    <p class="mt-2 break-all font-medium">{{ equipment.qr_url }}</p>
                </div>

                <div class="card card-muted p-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-muted">QR generated at</p>
                    <p class="mt-2 text-sm font-medium">
                        {{ equipment.qr_code.generated_at ? formatDateTime(equipment.qr_code.generated_at) : 'Not generated yet' }}
                    </p>
                </div>
            </div>
        </BaseCard>
    </section>

    <section class="mt-6">
        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Recent inspections</h2>
                <p class="page-subtitle mt-2">Latest inspections submitted against this equipment.</p>
            </div>

            <div
                v-if="recentInspections.length === 0"
                class="empty-state mt-6"
            >
                <p class="text-lg font-semibold">No inspections recorded</p>
                <p class="page-subtitle mt-2">Inspection activity for this equipment will appear here.</p>
            </div>

            <div
                v-else
                class="mt-6 space-y-4"
            >
                <div
                    v-for="inspection in recentInspections"
                    :key="inspection.uuid"
                    class="list-row first:pt-0"
                >
                    <div class="flex w-full items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold">{{ inspection.template?.name || 'Inspection' }}</p>
                            <p class="mt-1 text-sm text-muted">
                                {{ formatDateTime(inspection.inspected_at) }} by {{ inspection.inspector?.name || 'System Administrator' }}
                            </p>
                        </div>
                        <span :class="inspectionStatusClass(inspection.status)">
                            {{ inspectionStatusLabel(inspection.status) }}
                        </span>
                    </div>
                </div>
            </div>
        </BaseCard>
    </section>
</template>
