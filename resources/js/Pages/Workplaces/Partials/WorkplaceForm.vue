<script setup>
import BaseFieldError from '@/Components/Base/BaseFieldError.vue';
import BaseInput from '@/Components/Base/BaseInput.vue';
import BaseLabel from '@/Components/Base/BaseLabel.vue';

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
            <BaseLabel for-id="name">Workplace name</BaseLabel>
            <BaseInput
                id="name"
                v-model="form.name"
                name="name"
                required
            />
            <BaseFieldError :message="form.errors.name" />
        </div>

        <div>
            <BaseLabel for-id="code">Workplace code</BaseLabel>
            <BaseInput
                id="code"
                v-model="form.code"
                name="code"
            />
            <BaseFieldError :message="form.errors.code" />
        </div>

        <div>
            <BaseLabel for-id="primary_contact_name">Primary contact name</BaseLabel>
            <BaseInput
                id="primary_contact_name"
                v-model="form.primary_contact_name"
                name="primary_contact_name"
            />
            <BaseFieldError :message="form.errors.primary_contact_name" />
        </div>

        <div>
            <BaseLabel for-id="primary_contact_email">Primary contact email</BaseLabel>
            <BaseInput
                id="primary_contact_email"
                v-model="form.primary_contact_email"
                name="primary_contact_email"
                type="email"
            />
            <BaseFieldError :message="form.errors.primary_contact_email" />
        </div>

        <div>
            <BaseLabel for-id="primary_contact_phone">Primary contact phone</BaseLabel>
            <BaseInput
                id="primary_contact_phone"
                v-model="form.primary_contact_phone"
                name="primary_contact_phone"
            />
            <BaseFieldError :message="form.errors.primary_contact_phone" />
        </div>

        <div>
            <BaseLabel for-id="country">Country</BaseLabel>
            <BaseInput
                id="country"
                v-model="form.country"
                name="country"
            />
            <BaseFieldError :message="form.errors.country" />
        </div>

        <div class="lg:col-span-2">
            <BaseLabel for-id="address_line_1">Address line 1</BaseLabel>
            <BaseInput
                id="address_line_1"
                v-model="form.address_line_1"
                name="address_line_1"
            />
            <BaseFieldError :message="form.errors.address_line_1" />
        </div>

        <div class="lg:col-span-2">
            <BaseLabel for-id="address_line_2">Address line 2</BaseLabel>
            <BaseInput
                id="address_line_2"
                v-model="form.address_line_2"
                name="address_line_2"
            />
            <BaseFieldError :message="form.errors.address_line_2" />
        </div>

        <div>
            <BaseLabel for-id="city">City</BaseLabel>
            <BaseInput
                id="city"
                v-model="form.city"
                name="city"
            />
            <BaseFieldError :message="form.errors.city" />
        </div>

        <div>
            <BaseLabel for-id="state">State / province</BaseLabel>
            <BaseInput
                id="state"
                v-model="form.state"
                name="state"
            />
            <BaseFieldError :message="form.errors.state" />
        </div>

        <div>
            <BaseLabel for-id="postal_code">Postal code</BaseLabel>
            <BaseInput
                id="postal_code"
                v-model="form.postal_code"
                name="postal_code"
            />
            <BaseFieldError :message="form.errors.postal_code" />
        </div>
    </div>
</template>
