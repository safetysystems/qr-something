<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import EquipmentForm from '@/Pages/Equipment/Partials/EquipmentForm.vue';

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
    name: '',
    type: '',
    asset_code: '',
    serial_number: '',
    manufacturer: '',
    model: '',
    location_label: '',
    notes: '',
});

function submit() {
    form.post(props.workplace.links.store_equipment);
}
</script>

<template>
    <Head :title="`Add Equipment to ${workplace.name}`" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Equipment</p>
            <h1 class="page-title mt-2">Add equipment to {{ workplace.name }}</h1>
            <p class="page-subtitle mt-3">This record becomes the QR-addressable asset page for future inspections.</p>
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
            <EquipmentForm :form="form" />

            <div class="flex justify-end">
                <BaseButton
                    type="submit"
                    :disabled="form.processing"
                >
                    Save equipment
                </BaseButton>
            </div>
        </form>
    </BaseCard>
</template>
