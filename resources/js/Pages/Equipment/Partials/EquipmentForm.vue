<script setup>
import BaseFieldError from '@/Components/Base/BaseFieldError.vue';
import BaseInput from '@/Components/Base/BaseInput.vue';
import BaseLabel from '@/Components/Base/BaseLabel.vue';
import BaseTextarea from '@/Components/Base/BaseTextarea.vue';

defineProps({
    form: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <BaseLabel for-id="name">Equipment name</BaseLabel>
            <BaseInput
                id="name"
                v-model="form.name"
                name="name"
                required
            />
            <BaseFieldError :message="form.errors.name" />
        </div>

        <div>
            <BaseLabel for-id="type">Equipment type</BaseLabel>
            <BaseInput
                id="type"
                v-model="form.type"
                name="type"
                required
            />
            <BaseFieldError :message="form.errors.type" />
        </div>

        <div>
            <BaseLabel for-id="asset_code">Asset code</BaseLabel>
            <BaseInput
                id="asset_code"
                v-model="form.asset_code"
                name="asset_code"
            />
            <BaseFieldError :message="form.errors.asset_code" />
        </div>

        <div>
            <BaseLabel for-id="serial_number">Serial number</BaseLabel>
            <BaseInput
                id="serial_number"
                v-model="form.serial_number"
                name="serial_number"
            />
            <BaseFieldError :message="form.errors.serial_number" />
        </div>

        <div>
            <BaseLabel for-id="manufacturer">Manufacturer</BaseLabel>
            <BaseInput
                id="manufacturer"
                v-model="form.manufacturer"
                name="manufacturer"
            />
            <BaseFieldError :message="form.errors.manufacturer" />
        </div>

        <div>
            <BaseLabel for-id="model">Model</BaseLabel>
            <BaseInput
                id="model"
                v-model="form.model"
                name="model"
            />
            <BaseFieldError :message="form.errors.model" />
        </div>

        <div class="lg:col-span-2">
            <BaseLabel for-id="location_label">Location label</BaseLabel>
            <BaseInput
                id="location_label"
                v-model="form.location_label"
                name="location_label"
            />
            <BaseFieldError :message="form.errors.location_label" />
        </div>

        <div class="lg:col-span-2">
            <BaseLabel for-id="notes">Notes</BaseLabel>
            <BaseTextarea
                id="notes"
                v-model="form.notes"
                name="notes"
            />
            <BaseFieldError :message="form.errors.notes" />
        </div>
    </div>
</template>
