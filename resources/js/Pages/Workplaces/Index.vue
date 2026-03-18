<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BasePagination from '@/Components/Base/BasePagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDate } from '@/utils/formatters';

defineOptions({
    layout: AppLayout,
});

defineProps({
    workplaces: {
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
    <Head title="Workplaces" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Workplaces</p>
            <h1 class="page-title mt-2">Manage governed workplaces</h1>
            <p class="page-subtitle mt-3">
                Each workplace becomes the parent scope for equipment, permissions, and future inspections.
            </p>
        </div>

        <BaseButton :href="links.create">
            Create workplace
        </BaseButton>
    </section>

    <section class="mt-8">
        <div
            v-if="workplaces.data.length === 0"
            class="empty-state"
        >
            <p class="text-lg font-semibold">No workplaces found</p>
            <p class="page-subtitle mt-2">Create the first workplace to start the inspection registry.</p>
        </div>

        <div
            v-else
            class="table-shell bg-surface"
        >
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Workplace</th>
                        <th>Primary contact</th>
                        <th>Equipment</th>
                        <th>Created</th>
                        <th class="w-32">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="workplace in workplaces.data"
                        :key="workplace.uuid"
                    >
                        <td>
                            <p class="font-semibold">{{ workplace.name }}</p>
                            <p class="mt-1 text-muted">{{ workplace.code || 'No code assigned' }}</p>
                        </td>
                        <td>
                            <p>{{ workplace.primary_contact.name || 'Not set' }}</p>
                            <p class="mt-1 text-muted">
                                {{ workplace.primary_contact.email || workplace.primary_contact.phone || 'No contact details' }}
                            </p>
                        </td>
                        <td>{{ workplace.equipment_count ?? 0 }}</td>
                        <td>{{ formatDate(workplace.created_at) }}</td>
                        <td>
                            <Link
                                :href="workplace.links.show"
                                class="link-primary font-semibold"
                            >
                                Open
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <BasePagination
            :meta="workplaces.meta"
            :links="workplaces.links"
        />
    </section>
</template>
