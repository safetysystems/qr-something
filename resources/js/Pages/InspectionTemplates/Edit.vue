<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InspectionTemplateForm from '@/Pages/InspectionTemplates/Partials/InspectionTemplateForm.vue';

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

const form = useForm({
    name: props.template.name || '',
    description: props.template.description || '',
    items: normalizeCollection(props.template.items).map((item) => ({
        label: item.label || '',
        instructions: item.instructions || '',
        is_required: Boolean(item.is_required),
    })),
});

function addItem() {
    if (locked.value) {
        return;
    }

    form.items.push({
        label: '',
        instructions: '',
        is_required: true,
    });
}

function removeItem(index) {
    if (locked.value || form.items.length === 1) {
        return;
    }

    form.items.splice(index, 1);
}

function submit() {
    if (locked.value) {
        return;
    }

    form.put(props.template.links.update);
}
</script>

<template>
    <Head :title="`Edit ${template.name}`" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Inspection templates</p>
            <h1 class="page-title mt-2">Edit {{ template.name }}</h1>
            <p class="page-subtitle mt-3">Update this template before it is used in live inspections.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <BaseButton
                :href="template.links.show"
                variant="secondary"
            >
                Back to template
            </BaseButton>

            <BaseButton
                v-if="locked"
                :href="workplace.links.create_inspection_template"
            >
                Create new version
            </BaseButton>
        </div>
    </section>

    <BaseCard
        v-if="locked"
        class="mt-8"
    >
        <p class="text-lg font-semibold">This template is locked</p>
        <p class="page-subtitle mt-2">
            {{ template.inspections_count }} inspection{{ template.inspections_count === 1 ? '' : 's' }} already reference this checklist.
            Create a new template version instead of editing the original.
        </p>
    </BaseCard>

    <BaseCard
        v-else
        class="mt-8"
    >
        <form
            class="space-y-8"
            @submit.prevent="submit"
        >
            <InspectionTemplateForm
                :form="form"
                @add-item="addItem"
                @remove-item="removeItem"
            />

            <div class="flex justify-end">
                <BaseButton
                    type="submit"
                    :disabled="form.processing"
                >
                    Update template
                </BaseButton>
            </div>
        </form>
    </BaseCard>
</template>
