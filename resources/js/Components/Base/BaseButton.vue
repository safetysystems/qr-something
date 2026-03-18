<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
    },
    href: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const classes = computed(() => {
    const variants = {
        primary: 'btn btn-primary',
        secondary: 'btn btn-secondary',
        danger: 'btn btn-danger',
        ghost: 'btn btn-ghost',
    };

    return [
        variants[props.variant] || variants.primary,
        props.disabled ? 'cursor-not-allowed opacity-60' : '',
    ];
});
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :class="classes"
    >
        <slot />
    </Link>

    <button
        v-else
        :type="type"
        :class="classes"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>
