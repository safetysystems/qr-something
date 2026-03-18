<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import WorkplaceForm from '@/Pages/Workplaces/Partials/WorkplaceForm.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    links: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: '',
    code: '',
    primary_contact_name: '',
    primary_contact_email: '',
    primary_contact_phone: '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    state: '',
    postal_code: '',
    country: '',
});

function submit() {
    form.post(props.links.store);
}
</script>

<template>
    <Head title="Create Workplace" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Workplaces</p>
            <h1 class="page-title mt-2">Create workplace</h1>
            <p class="page-subtitle mt-3">Register a governed environment before attaching equipment records.</p>
        </div>

        <BaseButton
            :href="links.index"
            variant="secondary"
        >
            Back to workplaces
        </BaseButton>
    </section>

    <BaseCard class="mt-8">
        <form
            class="space-y-8"
            @submit.prevent="submit"
        >
            <WorkplaceForm :form="form" />

            <div class="flex justify-end">
                <BaseButton
                    type="submit"
                    :disabled="form.processing"
                >
                    Save workplace
                </BaseButton>
            </div>
        </form>
    </BaseCard>
</template>
