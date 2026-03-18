<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    meta: {
        type: Object,
        required: true,
    },
    links: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div
        v-if="meta.last_page > 1"
        class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <p class="text-sm text-muted">
            Page {{ meta.current_page }} of {{ meta.last_page }}
        </p>

        <div class="flex items-center gap-3">
            <Link
                v-if="links.prev"
                :href="links.prev"
                class="btn btn-secondary"
            >
                Previous
            </Link>
            <span
                v-else
                class="btn btn-secondary opacity-50"
            >
                Previous
            </span>

            <Link
                v-if="links.next"
                :href="links.next"
                class="btn btn-secondary"
            >
                Next
            </Link>
            <span
                v-else
                class="btn btn-secondary opacity-50"
            >
                Next
            </span>
        </div>
    </div>
</template>
