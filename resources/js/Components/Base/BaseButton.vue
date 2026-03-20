<script setup>
import { computed, useSlots } from 'vue';
import { Link } from '@inertiajs/vue3';

const slots = useSlots();

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
    },
    title: {
        type: String,
        default: null,
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

const resolvedTitle = computed(() => {
    if (props.title) {
        return props.title;
    }

    const nodes = slots.default?.() ?? [];
    const text = collectNodeText(nodes)
        .replace(/\s+/g, ' ')
        .trim();

    return text || null;
});

function collectNodeText(nodes) {
    return nodes
        .map((node) => {
            if (typeof node.children === 'string') {
                return node.children;
            }

            if (Array.isArray(node.children)) {
                return collectNodeText(node.children);
            }

            return '';
        })
        .join(' ');
}
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :class="classes"
        :title="resolvedTitle"
    >
        <slot />
    </Link>

    <button
        v-else
        :type="type"
        :class="classes"
        :disabled="disabled"
        :title="resolvedTitle"
    >
        <slot />
    </button>
</template>
