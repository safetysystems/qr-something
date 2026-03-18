<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import BasePagination from '@/Components/Base/BasePagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { compactAddress, formatDate } from '@/utils/formatters';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    workplace: {
        type: Object,
        required: true,
    },
    equipment: {
        type: Object,
        required: true,
    },
    inspectionTemplates: {
        type: Array,
        required: true,
    },
    templateLinks: {
        type: Object,
        required: true,
    },
});

function archiveWorkplace() {
    if (!window.confirm('Archive this workplace and all of its equipment?')) {
        return;
    }

    router.delete(props.workplace.links.destroy);
}
</script>

<template>
    <Head :title="workplace.name" />

    <section class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <p class="brand-kicker">Workplace</p>
                <span
                    v-if="workplace.code"
                    class="badge"
                >
                    {{ workplace.code }}
                </span>
            </div>
            <h1 class="page-title mt-2">{{ workplace.name }}</h1>
            <p class="page-subtitle mt-3">
                UUID {{ workplace.uuid }}. This is the tenant anchor for equipment and future inspections.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <BaseButton
                :href="templateLinks.create"
                variant="secondary"
            >
                Create template
            </BaseButton>
            <BaseButton :href="workplace.links.create_equipment">
                Add equipment
            </BaseButton>
            <BaseButton
                :href="workplace.links.edit"
                variant="secondary"
            >
                Edit workplace
            </BaseButton>
            <BaseButton
                variant="danger"
                @click="archiveWorkplace"
            >
                Archive
            </BaseButton>
        </div>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[1.1fr,1.4fr]">
        <BaseCard>
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold">Workplace details</h2>
                <span class="badge">{{ workplace.equipment_count ?? 0 }} equipment</span>
            </div>

            <dl class="detail-list mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <dt>Primary contact</dt>
                    <dd>{{ workplace.primary_contact.name || 'Not set' }}</dd>
                </div>
                <div>
                    <dt>Contact email</dt>
                    <dd>{{ workplace.primary_contact.email || 'Not set' }}</dd>
                </div>
                <div>
                    <dt>Contact phone</dt>
                    <dd>{{ workplace.primary_contact.phone || 'Not set' }}</dd>
                </div>
                <div>
                    <dt>Country</dt>
                    <dd>{{ workplace.address.country || 'Not set' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt>Address</dt>
                    <dd>{{ compactAddress(workplace.address) }}</dd>
                </div>
            </dl>
        </BaseCard>

        <BaseCard>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Equipment registry</h2>
                    <p class="page-subtitle mt-2">Assets directly governed by this workplace.</p>
                </div>
                <BaseButton
                    :href="workplace.links.create_equipment"
                    variant="secondary"
                >
                    Add equipment
                </BaseButton>
            </div>

            <div
                v-if="equipment.data.length === 0"
                class="empty-state mt-6"
            >
                <p class="text-lg font-semibold">No equipment registered</p>
                <p class="page-subtitle mt-2">Add the first asset to activate this workplace.</p>
            </div>

            <template v-else>
                <div class="table-shell bg-surface mt-6">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Asset code</th>
                                <th>Location</th>
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
                                <td>
                                    <Link
                                        :href="item.links.show"
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
            </template>
        </BaseCard>
    </section>

    <section class="mt-6">
        <BaseCard>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Inspection templates</h2>
                    <p class="page-subtitle mt-2">Reusable checklists available to equipment in this workplace.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <BaseButton
                        :href="templateLinks.index"
                        variant="secondary"
                    >
                        Manage templates
                    </BaseButton>
                    <BaseButton :href="templateLinks.create">
                        Create template
                    </BaseButton>
                </div>
            </div>

            <div
                v-if="inspectionTemplates.length === 0"
                class="empty-state mt-6"
            >
                <p class="text-lg font-semibold">No inspection templates yet</p>
                <p class="page-subtitle mt-2">Create the first checklist before inspectors start submitting results.</p>
            </div>

            <div
                v-else
                class="mt-6 space-y-4"
            >
                <div
                    v-for="template in inspectionTemplates"
                    :key="template.uuid"
                    class="list-row first:pt-0"
                >
                    <div class="flex w-full items-start justify-between gap-4">
                        <div>
                            <Link
                                :href="template.links.show"
                                class="font-semibold link-primary"
                            >
                                {{ template.name }}
                            </Link>
                            <p class="mt-1 text-sm text-muted">
                                {{ template.items_count ?? 0 }} items,
                                {{ template.inspections_count ?? 0 }} inspections,
                                created {{ formatDate(template.created_at) }}
                            </p>
                        </div>
                        <span class="badge">Checklist</span>
                    </div>
                </div>
            </div>
        </BaseCard>
    </section>
</template>
