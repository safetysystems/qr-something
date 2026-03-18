<script setup>
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
    links: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: '',
    description: '',
    items: [
        {
            label: '',
            instructions: '',
            is_required: true,
        },
    ],
});

function addItem() {
    form.items.push({
        label: '',
        instructions: '',
        is_required: true,
    });
}

function removeItem(index) {
    if (form.items.length === 1) {
        return;
    }

    form.items.splice(index, 1);
}

function submit() {
    form.post(props.links.store);
}
</script>

<template>
    <Head :title="`Create Template for ${workplace.name}`" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Inspection templates</p>
            <h1 class="page-title mt-2">Create template</h1>
            <p class="page-subtitle mt-3">Build the checklist inspectors will answer for equipment in {{ workplace.name }}.</p>
        </div>

        <BaseButton
            :href="links.index"
            variant="secondary"
        >
            Back to templates
        </BaseButton>
    </section>

    <BaseCard class="mt-8">
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
                    Save template
                </BaseButton>
            </div>
        </form>
    </BaseCard>
</template>
