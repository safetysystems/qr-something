<script setup>
defineProps({
    forId: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <label
        :for="forId"
        class="label"
    >
        <slot />
    </label>
</template>
