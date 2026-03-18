<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDateTime } from '@/utils/formatters';
import { inspectionStatusClass, inspectionStatusLabel } from '@/utils/inspections';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    equipment: {
        type: Object,
        required: true,
    },
    inspectionTemplates: {
        type: Array,
        required: true,
    },
    recentInspections: {
        type: Array,
        required: true,
    },
});

function archiveEquipment() {
    if (!window.confirm('Archive this equipment record?')) {
        return;
    }

    router.delete(props.equipment.links.destroy);
}

function regenerateQrCode() {
    router.post(props.equipment.qr_code.links.regenerate, {}, {
        preserveScroll: true,
    });
}

function normalizeCollection(value) {
    if (Array.isArray(value)) {
        return value;
    }

    if (Array.isArray(value?.data)) {
        return value.data;
    }

    return [];
}
</script>

<template>
    <Head :title="equipment.name" />

    <section class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <p class="brand-kicker">Equipment</p>
                <span class="badge">{{ equipment.type }}</span>
            </div>
            <h1 class="page-title mt-2">{{ equipment.name }}</h1>
            <p class="page-subtitle mt-3">
                QR route: <span class="text-primary font-semibold">{{ equipment.qr_url }}</span>
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <BaseButton
                v-if="inspectionTemplates.length > 0"
                :href="equipment.links.create_inspection"
            >
                Start inspection
            </BaseButton>
            <BaseButton
                :href="equipment.links.edit"
                variant="secondary"
            >
                Edit equipment
            </BaseButton>
            <BaseButton
                variant="danger"
                @click="archiveEquipment"
            >
                Archive
            </BaseButton>
        </div>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[1fr,1.1fr]">
        <BaseCard>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Equipment details</h2>
                    <p class="page-subtitle mt-2">Core data linked to this asset record.</p>
                </div>
                <Link
                    v-if="equipment.links.workplace"
                    :href="equipment.links.workplace"
                    class="link-primary text-sm font-semibold"
                >
                    Back to workplace
                </Link>
            </div>

            <dl class="detail-list mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <dt>UUID</dt>
                    <dd>{{ equipment.uuid }}</dd>
                </div>
                <div>
                    <dt>Workplace</dt>
                    <dd>{{ equipment.workplace?.name || 'Not set' }}</dd>
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
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">QR code</h2>
                    <p class="page-subtitle mt-2">
                        Generated inside Laravel and stored by this platform for download and print workflows.
                    </p>
                </div>
                <span class="badge">SVG</span>
            </div>

            <div class="mt-6 space-y-4">
                <div class="qr-preview-frame">
                    <img
                        :src="`${equipment.qr_code.links.image}?v=${equipment.qr_code.generated_at || ''}`"
                        :alt="`QR code for ${equipment.name}`"
                        class="qr-preview-image"
                    >
                </div>

                <div class="card card-muted p-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-muted">Encoded URL</p>
                    <p class="mt-2 break-all font-medium">{{ equipment.qr_url }}</p>
                </div>

                <div class="card card-muted p-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-muted">Generated at</p>
                    <p class="mt-2 text-sm font-medium">
                        {{ equipment.qr_code.generated_at ? formatDateTime(equipment.qr_code.generated_at) : 'Not generated yet' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a
                        :href="equipment.qr_code.links.download"
                        class="btn btn-secondary"
                    >
                        Download QR
                    </a>
                    <BaseButton
                        variant="secondary"
                        @click="regenerateQrCode"
                    >
                        Regenerate QR
                    </BaseButton>
                </div>
            </div>
        </BaseCard>
    </section>

    <section class="mt-6">
        <BaseCard>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Inspection module</h2>
                    <p class="page-subtitle mt-2">
                        Run inspections against this equipment using workplace-scoped templates.
                    </p>
                </div>
                <span class="badge">{{ inspectionTemplates.length }} templates</span>
            </div>

            <div class="mt-6 space-y-4">
                <div class="card card-muted p-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-muted">Available templates</p>
                    <div
                        v-if="inspectionTemplates.length > 0"
                        class="mt-3 space-y-2"
                    >
                        <p
                            v-for="template in inspectionTemplates"
                            :key="template.uuid"
                            class="text-sm"
                        >
                            <span class="font-medium">{{ template.name }}</span>
                            <span class="text-muted"> · {{ template.items_count ?? normalizeCollection(template.items).length }} items</span>
                        </p>
                    </div>
                    <p
                        v-else
                        class="mt-2 text-sm leading-6 text-muted"
                    >
                        No templates have been created for this workplace yet.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <BaseButton
                        v-if="inspectionTemplates.length > 0"
                        :href="equipment.links.create_inspection"
                    >
                        Start inspection
                    </BaseButton>
                    <BaseButton
                        v-else-if="equipment.links.workplace"
                        :href="equipment.links.workplace"
                        variant="secondary"
                    >
                        Open workplace
                    </BaseButton>
                </div>
            </div>
        </BaseCard>
    </section>

    <section class="mt-6">
        <BaseCard>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Recent inspections</h2>
                    <p class="page-subtitle mt-2">Latest submitted results for this equipment record.</p>
                </div>
                <BaseButton
                    v-if="inspectionTemplates.length > 0"
                    :href="equipment.links.create_inspection"
                    variant="secondary"
                >
                    New inspection
                </BaseButton>
            </div>

            <div
                v-if="recentInspections.length === 0"
                class="empty-state mt-6"
            >
                <p class="text-lg font-semibold">No inspections recorded</p>
                <p class="page-subtitle mt-2">Submit the first checklist to start building inspection history for this asset.</p>
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
                            <Link
                                :href="inspection.links.show"
                                class="font-semibold link-primary"
                            >
                                {{ inspection.template?.name || 'Inspection' }}
                            </Link>
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
