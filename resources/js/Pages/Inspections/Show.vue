<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDateTime } from '@/utils/formatters';
import { inspectionStatusClass, inspectionStatusLabel } from '@/utils/inspections';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    inspection: {
        type: Object,
        required: true,
    },
    equipment: {
        type: Object,
        required: true,
    },
});

function normalizeCollection(value) {
    if (Array.isArray(value)) {
        return value;
    }

    if (Array.isArray(value?.data)) {
        return value.data;
    }

    return [];
}

const inspectionResponses = computed(() => normalizeCollection(props.inspection.responses));
</script>

<template>
    <Head :title="`Inspection ${inspection.uuid}`" />

    <section class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <p class="brand-kicker">Inspection result</p>
                <span :class="inspectionStatusClass(inspection.status)">
                    {{ inspectionStatusLabel(inspection.status) }}
                </span>
            </div>
            <h1 class="page-title mt-2">{{ equipment.name }}</h1>
            <p class="page-subtitle mt-3">
                Inspection submitted on {{ formatDateTime(inspection.inspected_at) }}.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <BaseButton
                :href="equipment.links.show"
                variant="secondary"
            >
                Back to equipment
            </BaseButton>
            <BaseButton :href="equipment.links.create_inspection">
                New inspection
            </BaseButton>
        </div>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[0.95fr,1.35fr]">
        <BaseCard>
            <h2 class="text-xl font-semibold">Inspection summary</h2>

            <dl class="detail-list mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <dt>Template</dt>
                    <dd>{{ inspection.template?.name || 'Not set' }}</dd>
                </div>
                <div>
                    <dt>Inspector</dt>
                    <dd>{{ inspection.inspector?.name || 'System Administrator' }}</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd>{{ inspectionStatusLabel(inspection.status) }}</dd>
                </div>
                <div>
                    <dt>Inspection UUID</dt>
                    <dd>{{ inspection.uuid }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt>Notes</dt>
                    <dd>{{ inspection.notes || 'No summary notes recorded.' }}</dd>
                </div>
            </dl>
        </BaseCard>

        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Checklist responses</h2>
                <p class="page-subtitle mt-2">Each result is preserved against the template item used at inspection time.</p>
            </div>

            <div class="mt-6 space-y-4">
                <div
                    v-for="(response, index) in inspectionResponses"
                    :key="`${response.template_item?.id || index}-${response.response}`"
                    class="card card-muted p-5"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold">{{ response.template_item?.label || 'Checklist item' }}</p>
                            <p class="page-subtitle mt-2">
                                {{ response.template_item?.instructions || 'No additional instructions.' }}
                            </p>
                        </div>
                        <span :class="inspectionStatusClass(response.response)">
                            {{ inspectionStatusLabel(response.response) }}
                        </span>
                    </div>

                    <div class="mt-5">
                        <p class="text-sm font-medium">Inspector notes</p>
                        <p class="page-subtitle mt-2">{{ response.notes || 'No notes for this item.' }}</p>
                    </div>
                </div>
            </div>
        </BaseCard>
    </section>
</template>
