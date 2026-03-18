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
    workplace: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.workplace.name || '',
    code: props.workplace.code || '',
    primary_contact_name: props.workplace.primary_contact?.name || '',
    primary_contact_email: props.workplace.primary_contact?.email || '',
    primary_contact_phone: props.workplace.primary_contact?.phone || '',
    address_line_1: props.workplace.address?.line_1 || '',
    address_line_2: props.workplace.address?.line_2 || '',
    city: props.workplace.address?.city || '',
    state: props.workplace.address?.state || '',
    postal_code: props.workplace.address?.postal_code || '',
    country: props.workplace.address?.country || '',
});

function submit() {
    form.put(props.workplace.links.update);
}
</script>

<template>
    <Head :title="`Edit ${workplace.name}`" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Workplaces</p>
            <h1 class="page-title mt-2">Edit {{ workplace.name }}</h1>
            <p class="page-subtitle mt-3">Update the operational details that define this workplace.</p>
        </div>

        <BaseButton
            :href="workplace.links.show"
            variant="secondary"
        >
            Back to workplace
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
                    Update workplace
                </BaseButton>
            </div>
        </form>
    </BaseCard>
</template>
