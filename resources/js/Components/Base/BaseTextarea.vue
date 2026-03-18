<script setup>
defineProps({
    modelValue: {
        type: String,
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
    placeholder: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <textarea
        :id="id"
        :name="name"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        class="textarea"
        @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>
</template>
