<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';

const page = usePage();
const sidebarOpen = ref(false);

const authUser = computed(() => page.props.auth?.user ?? null);
const currentWorkplace = computed(() => authUser.value?.current_workplace ?? null);
const flashStatus = computed(() => page.props.flash?.status ?? null);
const navigation = computed(() => page.props.navigation ?? {});
const currentEquipment = computed(() => page.props.equipment ?? null);

const currentRole = computed(() => {
    const role = authUser.value?.current_workplace?.role ?? 'workplace user';
    return role
        .split('_')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
});

const isDashboard = computed(() => page.component === 'Client/Dashboard');
const isEquipment = computed(() => page.component.startsWith('Client/Equipment/'));
const isEquipmentShow = computed(() => page.component === 'Client/Equipment/Show');
const isEquipmentEdit = computed(() => page.component === 'Client/Equipment/Edit');

const pageMeta = computed(() => {
    if (page.component === 'Client/Dashboard') {
        return { eyebrow: 'Workplace', title: 'Dashboard' };
    }
    if (page.component.startsWith('Client/Equipment/')) {
        return { eyebrow: 'Workplace', title: 'Equipment' };
    }
    return {
        eyebrow: 'Workplace',
        title: currentWorkplace.value?.name || 'Portal',
    };
});

const topbarActions = computed(() => [
    {
        label: 'Public Website',
        href: navigation.value.home,
        variant: 'secondary',
        title: 'Open the public-facing website',
    },
    {
        label: 'Add Equipment',
        href: navigation.value.create_equipment,
        variant: 'primary',
        title: 'Create a new equipment record for this workplace',
    },
]);

const equipmentActions = computed(() => {
    if (!isEquipment.value || !currentEquipment.value?.links) {
        return [];
    }

    const actions = [];

    if (isEquipmentShow.value && currentEquipment.value.links.client_edit) {
        actions.push({
            label: 'Edit Equipment',
            variant: 'secondary',
            onClick: editEquipment,
            title: 'Edit this equipment record',
        });
    }

    if ((isEquipmentShow.value || isEquipmentEdit.value) && currentEquipment.value.links.client_destroy) {
        actions.push({
            label: 'Delete Equipment',
            variant: 'danger',
            onClick: deleteEquipment,
            title: 'Archive this equipment record',
        });
    }

    return actions;
});

function logout() {
    router.post(navigation.value.logout);
}

function editEquipment() {
    const editUrl = currentEquipment.value?.links?.client_edit;

    if (editUrl) {
        router.visit(editUrl);
    }
}

function deleteEquipment() {
    const destroyUrl = currentEquipment.value?.links?.client_destroy;

    if (!destroyUrl) {
        return;
    }

    if (!window.confirm('Archive this equipment record?')) {
        return;
    }

    router.delete(destroyUrl);
}

function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
}

function closeSidebar() {
    sidebarOpen.value = false;
}
</script>

<template>
    <div class="min-h-screen bg-slate-100 font-['Inter'] md:flex md:items-center md:justify-center md:p-6 xl:p-8">
        <div class="relative flex min-h-screen w-full overflow-hidden bg-[#f8fafc] md:min-h-[720px] md:max-w-7xl md:rounded-[28px] md:shadow-xl">
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-20 bg-slate-900/50 md:hidden"
                @click="closeSidebar"
            ></div>

            <aside
                :class="[
                    'absolute inset-y-0 left-0 z-30 flex w-[88vw] max-w-72 flex-col border-r border-slate-200 bg-white transition-transform duration-300 md:relative md:w-64 md:max-w-none md:translate-x-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                ]"
            >
                <div class="flex h-20 items-center px-5 md:px-6">
                    <Link :href="navigation.dashboard" class="text-3xl font-bold italic tracking-tighter text-[#2563eb]">
                        WP
                    </Link>
                </div>

                <nav class="flex-1 space-y-2 px-4 py-4">
                    <Link
                        :href="navigation.dashboard"
                        @click="closeSidebar"
                        :class="[
                            'flex items-center space-x-3 rounded-lg px-4 py-3 font-medium transition-colors',
                            isDashboard ? 'bg-blue-50 text-[#2563eb]' : 'text-[#64748b] hover:bg-slate-50 hover:text-[#0f172a]'
                        ]"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 shrink-0">
                            <path d="M4 13h6V4H4zM14 20h6v-9h-6zM14 10h6V4h-6zM4 20h6v-3H4z" fill="none" stroke="currentColor" stroke-width="1.8" />
                        </svg>
                        <span>Dashboard</span>
                    </Link>

                    <Link
                        :href="navigation.equipment"
                        @click="closeSidebar"
                        :class="[
                            'flex items-center space-x-3 rounded-lg px-4 py-3 font-medium transition-colors',
                            isEquipment ? 'bg-blue-50 text-[#2563eb]' : 'text-[#64748b] hover:bg-slate-50 hover:text-[#0f172a]'
                        ]"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 shrink-0">
                            <path d="M4 5.5h16v10H4zM9 19h6M12 15.5V19" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                        </svg>
                        <span>Equipment</span>
                    </Link>

                    <Link
                        :href="navigation.home"
                        class="flex items-center space-x-3 rounded-lg px-4 py-3 text-[#64748b] transition-colors hover:bg-slate-50 hover:text-[#0f172a]"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 shrink-0">
                            <path d="M12 3.5a8.5 8.5 0 1 0 0 17a8.5 8.5 0 1 0 0-17ZM3.8 12h16.4M12 3.8c2.1 2.2 3.3 5.1 3.3 8.2S14.1 18 12 20.2M12 3.8C9.9 6 8.7 8.9 8.7 12s1.2 6 3.3 8.2" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                        </svg>
                        <span>Public Website</span>
                    </Link>
                </nav>

                <div class="border-t border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 font-bold text-primary">
                                {{ authUser?.name?.slice(0, 1) || 'U' }}
                            </div>
                            <div class="truncate">
                                <p class="truncate text-sm font-medium text-[#0f172a]">{{ authUser?.name }}</p>
                                <p class="truncate text-xs text-[#64748b]">{{ currentRole }}</p>
                            </div>
                        </div>
                        <button
                            @click="logout"
                            class="rounded-lg p-2 text-[#64748b] transition-colors hover:bg-slate-100 hover:text-red-600"
                            title="Sign out"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5">
                                <path d="M10 17v1.5A1.5 1.5 0 0 1 8.5 20h-3A1.5 1.5 0 0 1 4 18.5v-13A1.5 1.5 0 0 1 5.5 4h3A1.5 1.5 0 0 1 10 5.5V7" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                <path d="M14 16l4-4-4-4M9 12h9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </aside>

            <main class="flex-1 overflow-y-auto">
                <header class="sticky top-0 z-10 border-b border-slate-200/80 bg-[#f8fafc]/95 backdrop-blur">
                    <div class="flex flex-col gap-4 px-4 py-4 sm:px-6 md:px-8">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <button
                                    @click="toggleSidebar"
                                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm md:hidden"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5">
                                        <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                                    </svg>
                                    <span class="sr-only">Open navigation</span>
                                </button>

                                <div class="min-w-0">
                                    <h1 class="mb-1 text-2xl font-bold text-[#0f172a] md:text-3xl">
                                        {{ pageMeta.title }}
                                    </h1>
                                    <div class="flex flex-wrap items-center gap-2 text-sm text-[#64748b]">
                                        <span>{{ pageMeta.eyebrow }}</span>
                                        <svg viewBox="0 0 20 20" aria-hidden="true" class="h-3 w-3 shrink-0">
                                            <path d="m7 4 6 6-6 6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                        </svg>
                                        <span class="truncate">{{ pageMeta.title }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="hidden min-w-0 items-center gap-3 sm:flex">
                                <div class="truncate text-right">
                                    <p class="truncate text-sm font-semibold text-[#0f172a]">{{ authUser?.name }}</p>
                                    <p class="truncate text-xs text-[#64748b]">{{ currentRole }}</p>
                                </div>
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 font-bold text-primary">
                                    {{ authUser?.name?.slice(0, 1) || 'U' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <BaseButton
                                v-for="action in topbarActions"
                                :key="action.label"
                                :href="action.href"
                                :variant="action.variant"
                                :title="action.title"
                                class="!rounded-xl !px-4 !py-2.5 text-sm"
                            >
                                {{ action.label }}
                            </BaseButton>

                            <BaseButton
                                v-for="action in equipmentActions"
                                :key="action.label"
                                :variant="action.variant"
                                :title="action.title"
                                class="!rounded-xl !px-4 !py-2.5 text-sm"
                                @click="action.onClick"
                            >
                                {{ action.label }}
                            </BaseButton>
                        </div>
                    </div>
                </header>

                <div class="px-4 py-5 sm:px-6 md:px-8 md:py-8">
                    <div
                        v-if="flashStatus"
                        class="mb-6 flex items-center rounded-xl border border-blue-100 bg-blue-50 p-4 text-[#2563eb] animate-fade-in"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="mr-3 h-5 w-5 shrink-0">
                            <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8" />
                            <path d="M12 10v5M12 7.5h.01" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
                        </svg>
                        <span class="text-sm font-medium">{{ flashStatus }}</span>
                    </div>

                    <div class="flex-1">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Tailwind handles most, but specific transitions and fonts are defined here */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Ensure BaseButton matches the style of the snippet if classes differ */
:deep(.btn-primary) {
    background-color: #2563eb !important;
}

:deep(.btn-secondary) {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    color: #2563eb !important;
}

@media (max-width: 639px) {
    :deep(.btn) {
        width: 100%;
        justify-content: center;
    }
}
</style>
