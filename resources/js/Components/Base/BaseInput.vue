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
    type: {
        type: String,
        default: 'text',
    },
    autocomplete: {
        type: String,
        default: null,
    },
    required: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
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
    <input
        :id="id"
        :name="name"
        :type="type"
        :value="modelValue"
        :autocomplete="autocomplete"
        :required="required"
        :autofocus="autofocus"
        :placeholder="placeholder"
        :disabled="disabled"
        class="input"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
