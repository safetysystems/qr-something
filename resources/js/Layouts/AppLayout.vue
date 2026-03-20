<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';

const page = usePage();
const sidebarOpen = ref(false);

const authUser = computed(() => page.props.auth?.user ?? null);
const flashStatus = computed(() => page.props.flash?.status ?? null);
const navigation = computed(() => page.props.navigation ?? {});
const isDashboard = computed(() => page.component === 'Dashboard/Index');
const isScanner = computed(() => page.component === 'Scan/Index');
const isTracker = computed(() => page.component === 'ProjectTracker/Index');
const isWorkplaces = computed(() => (
    page.component.startsWith('Workplaces/')
    || page.component.startsWith('Equipment/')
    || page.component.startsWith('InspectionTemplates/')
    || page.component.startsWith('Inspections/')
));
const pageMeta = computed(() => {
    if (page.component === 'Dashboard/Index') {
        return {
            eyebrow: 'Overview',
            title: 'Dashboard',
        };
    }

    if (page.component.startsWith('Workplaces/')) {
        return {
            eyebrow: 'Management',
            title: 'Workplaces',
        };
    }

    if (page.component.startsWith('Equipment/')) {
        return {
            eyebrow: 'Management',
            title: 'Equipment',
        };
    }

    if (page.component.startsWith('InspectionTemplates/')) {
        return {
            eyebrow: 'Operations',
            title: 'Inspection Templates',
        };
    }

    if (page.component.startsWith('Inspections/')) {
        return {
            eyebrow: 'Operations',
            title: 'Inspections',
        };
    }

    if (page.component === 'Scan/Index') {
        return {
            eyebrow: 'Operations',
            title: 'Scanner',
        };
    }

    if (page.component === 'ProjectTracker/Index') {
        return {
            eyebrow: 'Planning',
            title: 'Project Tracker',
        };
    }

    return {
        eyebrow: 'Admin',
        title: 'Platform',
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
        label: 'Add Workplace',
        href: navigation.value.create_workplace,
        variant: 'primary',
        title: 'Create a new workplace account and profile',
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
    <div class="dashboard-shell">
        <div
            v-if="sidebarOpen"
            class="dashboard-overlay"
            @click="closeSidebar"
        ></div>

        <aside
            :class="[
                'dashboard-sidebar',
                sidebarOpen && 'dashboard-sidebar-open',
            ]"
        >
            <Link
                :href="navigation.dashboard"
                class="dashboard-brand dashboard-brand-link"
                title="Open the admin dashboard"
                @click="closeSidebar"
            >
                <div class="dashboard-brand-mark">EI</div>
                <div>
                    <p class="brand-kicker">Equipment Inspection</p>
                    <p class="mt-1 text-sm text-muted">Administration</p>
                </div>
            </Link>

            <div class="sidebar-context-card mt-6">
                <p class="sidebar-section-title">Signed in role</p>
                <p class="mt-2 text-sm font-semibold">Super Admin</p>
                <p class="mt-1 text-xs text-muted">Full platform access across workplaces, scanning, and planning.</p>
            </div>

            <div class="sidebar-group mt-8">
                <p class="sidebar-section-title">Core</p>

                <nav class="mt-3 space-y-1">
                    <Link
                        :href="navigation.dashboard"
                        :class="['sidebar-link', isDashboard && 'sidebar-link-active']"
                        title="View platform counts and recent activity"
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
                        :href="navigation.workplaces"
                        :class="['sidebar-link', isWorkplaces && 'sidebar-link-active']"
                        title="Manage workplaces, equipment, and inspection setup"
                        @click="closeSidebar"
                    >
                        <span class="sidebar-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h.01M15 10h.01M9 14h.01M15 14h.01" />
                            </svg>
                        </span>
                        <span class="sidebar-link-copy">
                            <span class="sidebar-link-label">Workplaces</span>
                        </span>
                    </Link>
                </nav>
            </div>

            <div class="sidebar-group mt-8">
                <p class="sidebar-section-title">Tools</p>

                <nav class="mt-3 space-y-1">

                    <Link
                        :href="navigation.scanner"
                        :class="['sidebar-link', isScanner && 'sidebar-link-active']"
                        title="Scan QR codes from camera or uploaded images"
                        @click="closeSidebar"
                    >
                        <span class="sidebar-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M4 7V5a1 1 0 0 1 1-1h2M20 7V5a1 1 0 0 0-1-1h-2M4 17v2a1 1 0 0 0 1 1h2M20 17v2a1 1 0 0 1-1 1h-2M7 12h10" />
                            </svg>
                        </span>
                        <span class="sidebar-link-copy">
                            <span class="sidebar-link-label">Scanner</span>
                        </span>
                    </Link>

                    <Link
                        :href="navigation.project_tracker"
                        :class="['sidebar-link', isTracker && 'sidebar-link-active']"
                        title="Review roadmap progress and active delivery phases"
                        @click="closeSidebar"
                    >
                        <span class="sidebar-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M4 6h16M4 12h16M4 18h10" />
                            </svg>
                        </span>
                        <span class="sidebar-link-copy">
                            <span class="sidebar-link-label">Roadmap</span>
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
                        title="Open the customer-facing public website"
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
                        <span class="badge">Super Admin</span>
                        <BaseButton
                            variant="secondary"
                            class="w-full justify-center"
                            title="Sign out of the administrator account"
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
                                <p class="text-xs text-muted">Administrator</p>
                            </div>
                            <div class="topbar-avatar">
                                {{ authUser?.name?.slice(0, 1) || 'A' }}
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
