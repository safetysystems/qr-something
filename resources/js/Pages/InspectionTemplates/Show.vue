<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDateTime } from '@/utils/formatters';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    workplace: {
        type: Object,
        required: true,
    },
    template: {
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

const locked = computed(() => (props.template.inspections_count ?? 0) > 0);
const templateItems = computed(() => normalizeCollection(props.template.items));

function archiveTemplate() {
    if (!window.confirm('Archive this inspection template?')) {
        return;
    }

    router.delete(props.template.links.destroy);
}
</script>

<template>
    <Head :title="template.name" />

    <section class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <p class="brand-kicker">Inspection template</p>
                <span class="badge">{{ template.items_count ?? templateItems.length }} items</span>
                <span
                    v-if="locked"
                    class="badge"
                >
                    {{ template.inspections_count }} inspections
                </span>
            </div>
            <h1 class="page-title mt-2">{{ template.name }}</h1>
            <p class="page-subtitle mt-3">
                {{ template.description || 'No description provided for this checklist.' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <BaseButton
                :href="workplace.links.inspection_templates"
                variant="secondary"
            >
                Back to templates
            </BaseButton>
            <BaseButton
                v-if="!locked"
                :href="template.links.edit"
                variant="secondary"
            >
                Edit template
            </BaseButton>
            <BaseButton
                v-if="locked"
                :href="workplace.links.create_inspection_template"
            >
                Create new version
            </BaseButton>
            <BaseButton
                variant="danger"
                @click="archiveTemplate"
            >
                Archive
            </BaseButton>
        </div>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[0.95fr,1.35fr]">
        <BaseCard>
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold">Template details</h2>
                <span
                    :class="locked ? 'status-pill status-pill-neutral' : 'status-pill status-pill-passed'"
                >
                    {{ locked ? 'Locked after use' : 'Editable' }}
                </span>
            </div>

            <dl class="detail-list mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <dt>Workplace</dt>
                    <dd>{{ workplace.name }}</dd>
                </div>
                <div>
                    <dt>Checklist items</dt>
                    <dd>{{ template.items_count ?? templateItems.length }}</dd>
                </div>
                <div>
                    <dt>Submitted inspections</dt>
                    <dd>{{ template.inspections_count ?? 0 }}</dd>
                </div>
                <div>
                    <dt>Created by</dt>
                    <dd>{{ template.created_by?.name || 'System Administrator' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt>Created at</dt>
                    <dd>{{ formatDateTime(template.created_at) }}</dd>
                </div>
            </dl>
        </BaseCard>

        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Checklist definition</h2>
                <p class="page-subtitle mt-2">Inspectors will answer each item when running this template on equipment.</p>
            </div>

            <div class="mt-6 space-y-4">
                <div
                    v-for="item in templateItems"
                    :key="item.id"
                    class="card card-muted p-5"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold">{{ item.label }}</p>
                            <p class="page-subtitle mt-2">{{ item.instructions || 'No additional instructions.' }}</p>
                        </div>
                        <span
                            :class="item.is_required ? 'status-pill status-pill-passed' : 'status-pill status-pill-neutral'"
                        >
                            {{ item.is_required ? 'Required' : 'Optional' }}
                        </span>
                    </div>
                </div>
            </div>
        </BaseCard>
    </section>
</template>
