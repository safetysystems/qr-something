<script setup>
defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String,
        default: null,
    },
    name: {
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
    <label class="checkbox-row">
        <input
            :id="id"
            :name="name"
            :checked="modelValue"
            :disabled="disabled"
            type="checkbox"
            class="checkbox-input"
            @change="$emit('update:modelValue', $event.target.checked)"
        >
        <span class="text-sm font-medium">
            <slot />
        </span>
    </label>
</template>
