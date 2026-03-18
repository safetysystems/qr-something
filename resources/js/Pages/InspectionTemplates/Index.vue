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
    workplace: {
        type: Object,
        required: true,
    },
    templates: {
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
    <Head :title="`${workplace.name} Templates`" />

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Inspection templates</p>
            <h1 class="page-title mt-2">{{ workplace.name }}</h1>
            <p class="page-subtitle mt-3">
                Manage the checklists available when equipment in this workplace is inspected.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <BaseButton
                :href="workplace.links.show"
                variant="secondary"
            >
                Back to workplace
            </BaseButton>
            <BaseButton :href="links.create">
                Create template
            </BaseButton>
        </div>
    </section>

    <section class="mt-8">
        <div
            v-if="templates.data.length === 0"
            class="empty-state"
        >
            <p class="text-lg font-semibold">No templates yet</p>
            <p class="page-subtitle mt-2">Create the first checklist before running inspections for this workplace.</p>
        </div>

        <div
            v-else
            class="table-shell bg-surface"
        >
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Template</th>
                        <th>Checklist items</th>
                        <th>Submitted inspections</th>
                        <th>Created</th>
                        <th class="w-32">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="template in templates.data"
                        :key="template.uuid"
                    >
                        <td>
                            <p class="font-semibold">{{ template.name }}</p>
                            <p class="mt-1 text-muted">{{ template.description || 'No description provided' }}</p>
                        </td>
                        <td>{{ template.items_count ?? 0 }}</td>
                        <td>{{ template.inspections_count ?? 0 }}</td>
                        <td>{{ formatDate(template.created_at) }}</td>
                        <td>
                            <Link
                                :href="template.links.show"
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
            :meta="templates.meta"
            :links="templates.links"
        />
    </section>
</template>
