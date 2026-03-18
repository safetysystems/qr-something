<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    id: {
        type: String,
        default: null,
    },
    name: {
        type: String,
        default: null,
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <select
        :id="id"
        :name="name"
        :value="modelValue"
        :required="required"
        :disabled="disabled"
        class="select-input"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <slot />
    </select>
</template>
