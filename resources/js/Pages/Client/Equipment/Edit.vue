<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import EquipmentForm from '@/Pages/Equipment/Partials/EquipmentForm.vue';

defineOptions({
    layout: ClientLayout,
});

const props = defineProps({
    equipment: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.equipment.name || '',
    type: props.equipment.type || '',
    asset_code: props.equipment.asset_code || '',
    serial_number: props.equipment.serial_number || '',
    manufacturer: props.equipment.manufacturer || '',
    model: props.equipment.model || '',
    location_label: props.equipment.location_label || '',
    notes: props.equipment.notes || '',
});

function submit() {
    form.put(props.equipment.links.client_update);
}
</script>

<template>
    <Head :title="`Edit ${equipment.name}`" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Equipment</p>
            <h1 class="page-title mt-2">Edit {{ equipment.name }}</h1>
            <p class="page-subtitle mt-3">
                Update the workplace equipment record and keep its details accurate.
            </p>
        </div>

        <BaseButton
            :href="equipment.links.client_show"
            variant="secondary"
        >
            Back to equipment
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
                    Update equipment
                </BaseButton>
            </div>
        </form>
    </BaseCard>
</template>
