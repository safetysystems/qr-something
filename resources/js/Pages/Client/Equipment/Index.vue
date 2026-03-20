<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BasePagination from '@/Components/Base/BasePagination.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { formatDate } from '@/utils/formatters';

defineOptions({
    layout: ClientLayout,
});

defineProps({
    workplace: {
        type: Object,
        required: true,
    },
    equipment: {
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
    <Head :title="`${workplace.name} Equipment`" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Equipment</p>
            <h1 class="page-title mt-2">Equipment registry</h1>
            <p class="page-subtitle mt-3">
                Manage the equipment currently registered under {{ workplace.name }}.
            </p>
        </div>

        <BaseButton :href="links.create">
            Add equipment
        </BaseButton>
    </section>

    <section class="mt-8">
        <div
            v-if="equipment.data.length === 0"
            class="empty-state"
        >
            <p class="text-lg font-semibold">No equipment found</p>
            <p class="page-subtitle mt-2">Create the first equipment record for this workplace.</p>

            <div class="mt-6">
                <BaseButton :href="links.create">
                    Add equipment
                </BaseButton>
            </div>
        </div>

        <div
            v-else
            class="table-shell bg-surface"
        >
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Asset code</th>
                        <th>Location</th>
                        <th>Created</th>
                        <th class="w-28">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in equipment.data"
                        :key="item.uuid"
                    >
                        <td>
                            <p class="font-semibold">{{ item.name }}</p>
                            <p class="mt-1 text-muted">{{ item.manufacturer || 'Manufacturer not set' }}</p>
                        </td>
                        <td>{{ item.type }}</td>
                        <td>{{ item.asset_code || 'Not set' }}</td>
                        <td>{{ item.location_label || 'Not set' }}</td>
                        <td>{{ formatDate(item.created_at) }}</td>
                        <td>
                            <Link
                                :href="item.links.client_show"
                                class="link-primary font-semibold"
                            >
                                View
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <BasePagination
            :meta="equipment.meta"
            :links="equipment.links"
        />
    </section>
</template>
