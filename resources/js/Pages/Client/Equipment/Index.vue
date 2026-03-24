<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
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

function deleteEquipment(item) {
    if (!item?.links?.client_destroy) {
        return;
    }

    if (!window.confirm(`Archive ${item.name}?`)) {
        return;
    }

    router.delete(item.links.client_destroy);
}
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
            class="space-y-4"
        >
            <div class="space-y-4 md:hidden">
                <article
                    v-for="item in equipment.data"
                    :key="`${item.uuid}-mobile`"
                    class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-900">{{ item.name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ item.manufacturer || 'Manufacturer not set' }}</p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <Link
                                :href="item.links.client_show"
                                class="table-action-icon table-action-icon-view"
                                title="View equipment"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="table-action-svg">
                                    <path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                    <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                                <span class="sr-only">View equipment</span>
                            </Link>
                            <Link
                                :href="item.links.client_edit"
                                class="table-action-icon table-action-icon-edit"
                                title="Edit equipment"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="table-action-svg">
                                    <path d="M16.86 3.49a2.12 2.12 0 0 1 3 3L9 17.35l-4.5 1.15 1.15-4.5L16.86 3.49Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                    <path d="M14.5 5.85 18.15 9.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                                </svg>
                                <span class="sr-only">Edit equipment</span>
                            </Link>
                            <button
                                type="button"
                                class="table-action-icon table-action-icon-delete"
                                title="Delete equipment"
                                @click="deleteEquipment(item)"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="table-action-svg">
                                    <path d="M4.5 7.5h15" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                                    <path d="M9.5 3.75h5a1 1 0 0 1 1 1V7.5h-7V4.75a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8" />
                                    <path d="M6.5 7.5 7.3 19a1.5 1.5 0 0 0 1.5 1.4h6.4a1.5 1.5 0 0 0 1.5-1.4l.8-11.5" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8" />
                                    <path d="M10 10.5v6M14 10.5v6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                                </svg>
                                <span class="sr-only">Delete equipment</span>
                            </button>
                        </div>
                    </div>

                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-slate-500">Type</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ item.type }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Created</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ formatDate(item.created_at) }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Asset code</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ item.asset_code || 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Location</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ item.location_label || 'Not set' }}</dd>
                        </div>
                    </dl>
                </article>
            </div>

            <div class="table-shell hidden overflow-x-auto bg-surface md:block">
                <table class="data-table min-w-[760px]">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Asset code</th>
                            <th>Location</th>
                            <th>Created</th>
                            <th class="w-48">Actions</th>
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
                                <div class="flex flex-wrap items-center gap-2">
                                    <Link
                                        :href="item.links.client_show"
                                        class="table-action-icon table-action-icon-view"
                                        title="View equipment"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true" class="table-action-svg">
                                            <path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                            <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8" />
                                        </svg>
                                        <span class="sr-only">View equipment</span>
                                    </Link>
                                    <Link
                                        :href="item.links.client_edit"
                                        class="table-action-icon table-action-icon-edit"
                                        title="Edit equipment"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true" class="table-action-svg">
                                            <path d="M16.86 3.49a2.12 2.12 0 0 1 3 3L9 17.35l-4.5 1.15 1.15-4.5L16.86 3.49Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                            <path d="M14.5 5.85 18.15 9.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                                        </svg>
                                        <span class="sr-only">Edit equipment</span>
                                    </Link>
                                    <button
                                        type="button"
                                        class="table-action-icon table-action-icon-delete"
                                        title="Delete equipment"
                                        @click="deleteEquipment(item)"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true" class="table-action-svg">
                                            <path d="M4.5 7.5h15" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                                            <path d="M9.5 3.75h5a1 1 0 0 1 1 1V7.5h-7V4.75a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8" />
                                            <path d="M6.5 7.5 7.3 19a1.5 1.5 0 0 0 1.5 1.4h6.4a1.5 1.5 0 0 0 1.5-1.4l.8-11.5" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8" />
                                            <path d="M10 10.5v6M14 10.5v6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                                        </svg>
                                        <span class="sr-only">Delete equipment</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <BasePagination
            :meta="equipment.meta"
            :links="equipment.links"
        />
    </section>
</template>

<style scoped>
.table-action-icon {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.625rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.table-action-svg {
    width: 1rem;
    height: 1rem;
}

.table-action-icon:hover {
    background-color: #e2e8f0;
}

.table-action-icon-view {
    color: #2563eb;
}

.table-action-icon-edit {
    color: #334155;
}

.table-action-icon-delete {
    color: #dc2626;
}
</style>
