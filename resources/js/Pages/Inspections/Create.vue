<script setup>
import { computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import BaseFieldError from '@/Components/Base/BaseFieldError.vue';
import BaseInput from '@/Components/Base/BaseInput.vue';
import BaseLabel from '@/Components/Base/BaseLabel.vue';
import BaseSelect from '@/Components/Base/BaseSelect.vue';
import BaseTextarea from '@/Components/Base/BaseTextarea.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    equipment: {
        type: Object,
        required: true,
    },
    templates: {
        type: Array,
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

const form = useForm({
    inspection_template_uuid: props.templates[0]?.uuid ?? '',
    inspected_at: toDateTimeLocal(new Date()),
    notes: '',
    responses: [],
});

const selectedTemplate = computed(() => (
    props.templates.find((template) => template.uuid === form.inspection_template_uuid) ?? null
));

const selectedTemplateItems = computed(() => normalizeCollection(selectedTemplate.value?.items));

watch(selectedTemplate, (template) => {
    const existingResponses = new Map(
        form.responses.map((response) => [response.inspection_template_item_id, response]),
    );

    form.responses = template
        ? normalizeCollection(template.items).map((item) => ({
            inspection_template_item_id: item.id,
            response: existingResponses.get(item.id)?.response || 'pass',
            notes: existingResponses.get(item.id)?.notes || '',
        }))
        : [];
}, { immediate: true });

function responseError(index, field) {
    return form.errors[`responses.${index}.${field}`];
}

function submit() {
    form.post(props.equipment.links.store_inspection);
}

function toDateTimeLocal(value) {
    const date = new Date(value);
    const offset = date.getTimezoneOffset();
    const normalized = new Date(date.getTime() - (offset * 60 * 1000));

    return normalized.toISOString().slice(0, 16);
}
</script>

<template>
    <Head :title="`Inspect ${equipment.name}`" />

    <section class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <p class="brand-kicker">Inspection run</p>
            <h1 class="page-title mt-2">{{ equipment.name }}</h1>
            <p class="page-subtitle mt-3">
                Submit a checklist result for this equipment record.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <BaseButton
                :href="equipment.links.show"
                variant="secondary"
            >
                Back to equipment
            </BaseButton>
        </div>
    </section>

    <BaseCard
        v-if="templates.length === 0"
        class="mt-8"
    >
        <p class="text-lg font-semibold">No templates available</p>
        <p class="page-subtitle mt-2">
            Create an inspection template in the workplace first, then return here to submit the inspection.
        </p>
        <div class="mt-5">
            <BaseButton
                v-if="equipment.links.workplace"
                :href="equipment.links.workplace"
                variant="secondary"
            >
                Open workplace
            </BaseButton>
        </div>
    </BaseCard>

    <form
        v-else
        class="mt-8 space-y-6"
        @submit.prevent="submit"
    >
        <BaseCard>
            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <BaseLabel for-id="inspection_template_uuid">Inspection template</BaseLabel>
                    <BaseSelect
                        id="inspection_template_uuid"
                        v-model="form.inspection_template_uuid"
                        name="inspection_template_uuid"
                        required
                    >
                        <option
                            v-for="template in templates"
                            :key="template.uuid"
                            :value="template.uuid"
                        >
                            {{ template.name }} ({{ template.items_count ?? normalizeCollection(template.items).length }} items)
                        </option>
                    </BaseSelect>
                    <BaseFieldError :message="form.errors.inspection_template_uuid" />
                </div>

                <div>
                    <BaseLabel for-id="inspected_at">Inspected at</BaseLabel>
                    <BaseInput
                        id="inspected_at"
                        v-model="form.inspected_at"
                        name="inspected_at"
                        type="datetime-local"
                        required
                    />
                    <BaseFieldError :message="form.errors.inspected_at" />
                </div>

                <div class="lg:col-span-2">
                    <BaseLabel for-id="notes">Inspection notes</BaseLabel>
                    <BaseTextarea
                        id="notes"
                        v-model="form.notes"
                        name="notes"
                        placeholder="Optional summary for the inspection."
                    />
                    <BaseFieldError :message="form.errors.notes" />
                </div>
            </div>
        </BaseCard>

        <BaseCard v-if="selectedTemplate">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">{{ selectedTemplate.name }}</h2>
                    <p class="page-subtitle mt-2">
                        {{ selectedTemplate.description || 'Answer each checklist item and submit the inspection result.' }}
                    </p>
                </div>
                <span class="badge">{{ selectedTemplateItems.length }} items</span>
            </div>

            <BaseFieldError
                :message="form.errors.responses"
                class="mt-4"
            />

            <div class="mt-6 space-y-4">
                <div
                    v-for="(item, index) in selectedTemplateItems"
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

                    <div class="mt-5 grid gap-5 lg:grid-cols-[220px,1fr]">
                        <div>
                            <BaseLabel :for-id="`response_${item.id}`">Result</BaseLabel>
                            <BaseSelect
                                :id="`response_${item.id}`"
                                v-model="form.responses[index].response"
                                :name="`responses[${index}][response]`"
                                required
                            >
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                                <option value="not_applicable">Not applicable</option>
                            </BaseSelect>
                            <BaseFieldError :message="responseError(index, 'response')" />
                        </div>

                        <div>
                            <BaseLabel :for-id="`response_notes_${item.id}`">Notes</BaseLabel>
                            <BaseTextarea
                                :id="`response_notes_${item.id}`"
                                v-model="form.responses[index].notes"
                                :name="`responses[${index}][notes]`"
                                placeholder="Optional context for this checklist item."
                            />
                            <BaseFieldError :message="responseError(index, 'notes')" />
                        </div>
                    </div>
                </div>
            </div>
        </BaseCard>

        <div class="flex justify-end">
            <BaseButton
                type="submit"
                :disabled="form.processing"
            >
                Submit inspection
            </BaseButton>
        </div>
    </form>
</template>
