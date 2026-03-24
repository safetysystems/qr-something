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
    <div class="min-h-screen flex items-center justify-center p-0 md:p-8 bg-slate-100 font-['Inter']">
        <div class="w-full max-w-7xl bg-[#f8fafc] rounded-xl shadow-xl overflow-hidden flex min-h-[800px] relative">
            
            <div 
                v-if="sidebarOpen" 
                class="fixed inset-0 bg-slate-900/50 z-20 md:hidden"
                @click="closeSidebar"
            ></div>

            <aside 
                :class="[
                    'w-64 bg-white border-r border-slate-200 flex flex-col z-30 transition-transform duration-300 absolute inset-y-0 left-0 md:relative md:translate-x-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                ]"
            >
                <div class="h-20 flex items-center px-6">
                    <Link :href="navigation.dashboard" class="text-3xl font-bold text-[#2563eb] italic tracking-tighter">
                        WP
                    </Link>
                </div>

                <nav class="flex-1 px-4 py-4 space-y-2">
                    <Link
                        :href="navigation.dashboard"
                        @click="closeSidebar"
                        :class="[
                            'flex items-center space-x-3 px-4 py-3 rounded-lg font-medium transition-colors',
                            isDashboard ? 'bg-blue-50 text-[#2563eb]' : 'text-[#64748b] hover:bg-slate-50 hover:text-[#0f172a]'
                        ]"
                    >
                        <i class="fa-solid fa-border-all w-5 text-center"></i>
                        <span>Dashboard</span>
                    </Link>

                    <Link
                        :href="navigation.equipment"
                        @click="closeSidebar"
                        :class="[
                            'flex items-center space-x-3 px-4 py-3 rounded-lg font-medium transition-colors',
                            isEquipment ? 'bg-blue-50 text-[#2563eb]' : 'text-[#64748b] hover:bg-slate-50 hover:text-[#0f172a]'
                        ]"
                    >
                        <i class="fa-solid fa-desktop w-5 text-center"></i>
                        <span>Equipment</span>
                    </Link>

                    <Link
                        :href="navigation.home"
                        class="flex items-center space-x-3 px-4 py-3 text-[#64748b] hover:bg-slate-50 hover:text-[#0f172a] rounded-lg transition-colors"
                    >
                        <i class="fa-solid fa-globe w-5 text-center"></i>
                        <span>Public Website</span>
                    </Link>
                </nav>

                <div class="border-t border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-primary font-bold shrink-0">
                                {{ authUser?.name?.slice(0, 1) || 'U' }}
                            </div>
                            <div class="truncate">
                                <p class="font-medium text-sm text-[#0f172a] truncate">{{ authUser?.name }}</p>
                                <p class="text-xs text-[#64748b] truncate">{{ currentRole }}</p>
                            </div>
                        </div>
                        <button 
                            @click="logout" 
                            class="text-[#64748b] hover:text-red-600 transition-colors p-1"
                            title="Sign out"
                        >
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </div>
                </div>
            </aside>

            <main class="flex-1 flex flex-col p-6 md:p-8 overflow-y-auto">
                <header class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                    <div class="flex items-center gap-3">
                        <button @click="toggleSidebar" class="md:hidden text-slate-600 p-1">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-[#0f172a] mb-1">
                                {{ pageMeta.title }}
                            </h1>
                            <div class="text-sm text-[#64748b] flex items-center space-x-2">
                                <span>{{ pageMeta.eyebrow }}</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                <span>{{ pageMeta.title }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <BaseButton
                            v-for="action in topbarActions"
                            :key="action.label"
                            :href="action.href"
                            :variant="action.variant"
                            :title="action.title"
                            class="!px-5 !py-2.5 !rounded-lg !shadow-sm !font-medium"
                        >
                            {{ action.label }}
                        </BaseButton>

                        <BaseButton
                            v-for="action in equipmentActions"
                            :key="action.label"
                            :variant="action.variant"
                            :title="action.title"
                            class="!px-5 !py-2.5 !rounded-lg !shadow-sm !font-medium"
                            @click="action.onClick"
                        >
                            {{ action.label }}
                        </BaseButton>
                    </div>
                </header>

                <div 
                    v-if="flashStatus" 
                    class="mb-6 p-4 bg-blue-50 border border-blue-100 text-[#2563eb] rounded-xl flex items-center animate-fade-in"
                >
                    <i class="fa-solid fa-circle-info mr-3"></i>
                    <span class="text-sm font-medium">{{ flashStatus }}</span>
                </div>

                <div class="flex-1">
                    <slot />
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
</style>
