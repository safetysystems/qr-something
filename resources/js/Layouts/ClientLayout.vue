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
const currentRole = computed(() => {
    const role = authUser.value?.current_workplace?.role ?? 'workplace user';

    return role
        .split('_')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
});

const isDashboard = computed(() => page.component === 'Client/Dashboard');
const isEquipment = computed(() => page.component.startsWith('Client/Equipment/'));

const pageMeta = computed(() => {
    if (page.component === 'Client/Dashboard') {
        return {
            eyebrow: 'Workplace',
            title: 'Dashboard',
        };
    }

    if (page.component.startsWith('Client/Equipment/')) {
        return {
            eyebrow: 'Workplace',
            title: 'Equipment',
        };
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

function logout() {
    router.post(navigation.value.logout);
}

function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
}

function closeSidebar() {
    sidebarOpen.value = false;
}
</script>

<template>
    <div class="dashboard-shell client-shell">
        <div
            v-if="sidebarOpen"
            class="dashboard-overlay"
            @click="closeSidebar"
        ></div>

        <aside
            :class="[
                'dashboard-sidebar client-sidebar',
                sidebarOpen && 'dashboard-sidebar-open',
            ]"
        >
            <Link
                :href="navigation.dashboard"
                class="dashboard-brand dashboard-brand-link"
                title="Open the workplace dashboard"
                @click="closeSidebar"
            >
                <div class="dashboard-brand-mark client-brand-mark">WP</div>
                <div>
                    <p class="brand-kicker">Workplace Portal</p>
                    <p class="mt-1 text-sm text-muted">{{ currentWorkplace?.name || 'Client access' }}</p>
                </div>
            </Link>

            <div class="sidebar-context-card mt-6">
                <p class="sidebar-section-title">Current workplace</p>
                <p class="mt-2 text-sm font-semibold">{{ currentWorkplace?.name || 'Workplace portal' }}</p>
                <p class="mt-1 text-xs text-muted">
                    {{ currentWorkplace?.code || 'No workplace code set' }} · {{ currentRole }}
                </p>
            </div>

            <div class="sidebar-group mt-8">
                <p class="sidebar-section-title">Core</p>

                <nav class="mt-3 space-y-1">
                    <Link
                        :href="navigation.dashboard"
                        :class="['sidebar-link', isDashboard && 'sidebar-link-active']"
                        title="View workplace counts and recent activity"
                        @click="closeSidebar"
                    >
                        <span class="sidebar-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M4 13h6V4H4zM14 20h6v-9h-6zM14 10h6V4h-6zM4 20h6v-3H4z" />
                            </svg>
                        </span>
                        <span class="sidebar-link-copy">
                            <span class="sidebar-link-label">Dashboard</span>
                        </span>
                    </Link>

                    <Link
                        :href="navigation.equipment"
                        :class="['sidebar-link', isEquipment && 'sidebar-link-active']"
                        title="Create and browse workplace equipment records"
                        @click="closeSidebar"
                    >
                        <span class="sidebar-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M6 7h12M6 12h12M6 17h12M4 7h.01M4 12h.01M4 17h.01" />
                            </svg>
                        </span>
                        <span class="sidebar-link-copy">
                            <span class="sidebar-link-label">Equipment</span>
                        </span>
                    </Link>
                </nav>
            </div>

            <div class="sidebar-group mt-8">
                <p class="sidebar-section-title">External</p>

                <nav class="mt-3 space-y-1">
                    <Link
                        :href="navigation.home"
                        class="sidebar-link"
                        title="Open the public-facing website"
                        @click="closeSidebar"
                    >
                        <span class="sidebar-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M3 10.5 12 3l9 7.5M5 9.5V21h14V9.5" />
                            </svg>
                        </span>
                        <span class="sidebar-link-copy">
                            <span class="sidebar-link-label">Public Website</span>
                        </span>
                    </Link>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="sidebar-user-card">
                    <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Signed in as</p>
                    <p class="mt-3 text-sm font-semibold">{{ authUser?.name }}</p>
                    <p class="mt-1 text-xs text-muted">{{ authUser?.email }}</p>
                    <div class="sidebar-user-actions mt-4">
                        <span class="badge">{{ currentRole }}</span>
                        <BaseButton
                            variant="secondary"
                            class="w-full justify-center"
                            title="Sign out of the workplace portal"
                            @click="logout"
                        >
                            Sign out
                        </BaseButton>
                    </div>
                </div>
            </div>
        </aside>

        <div class="dashboard-main">
            <header class="dashboard-topbar">
                <div class="dashboard-topbar-inner">
                    <div class="flex items-start gap-3">
                        <button
                            type="button"
                            class="sidebar-toggle"
                            title="Open navigation"
                            @click="toggleSidebar"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </button>

                        <div>
                            <p class="brand-kicker">{{ pageMeta.eyebrow }}</p>
                            <h1 class="mt-1 text-xl font-semibold tracking-tight">{{ pageMeta.title }}</h1>
                            <p class="mt-1 text-sm text-muted">{{ currentWorkplace?.name || 'Workplace portal' }}</p>
                        </div>
                    </div>

                    <div class="dashboard-topbar-actions">
                        <BaseButton
                            v-for="action in topbarActions"
                            :key="action.label"
                            :href="action.href"
                            :variant="action.variant"
                            :title="action.title"
                        >
                            {{ action.label }}
                        </BaseButton>

                        <div class="topbar-user">
                            <div class="text-right">
                                <p class="text-sm font-semibold">{{ authUser?.name }}</p>
                                <p class="text-xs text-muted">{{ currentRole }}</p>
                            </div>
                            <div class="topbar-avatar client-topbar-avatar">
                                {{ currentWorkplace?.name?.slice(0, 1) || 'W' }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="dashboard-content">
                <div class="page-wrap">
                    <div
                        v-if="flashStatus"
                        class="status-banner mb-6"
                    >
                        {{ flashStatus }}
                    </div>

                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
