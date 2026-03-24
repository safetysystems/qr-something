<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';

const page = usePage();
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);

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
            eyebrow: 'Executive Admin',
            title: 'Dashboard',
            subtitle: 'Monitoring platform activity, workplace readiness, and compliance trends.',
        };
    }

    if (page.component.startsWith('Workplaces/')) {
        return {
            eyebrow: 'Management',
            title: 'Workplaces',
            subtitle: 'Manage workplace records, equipment inventories, and inspection setup.',
        };
    }

    if (page.component.startsWith('Equipment/')) {
        return {
            eyebrow: 'Management',
            title: 'Equipment',
            subtitle: 'Review asset details, QR codes, and inspection history.',
        };
    }

    if (page.component.startsWith('InspectionTemplates/')) {
        return {
            eyebrow: 'Operations',
            title: 'Inspection Templates',
            subtitle: 'Maintain reusable templates for workplace-specific inspection flows.',
        };
    }

    if (page.component.startsWith('Inspections/')) {
        return {
            eyebrow: 'Operations',
            title: 'Inspections',
            subtitle: 'Track inspection submissions and audit-ready results.',
        };
    }

    if (page.component === 'Scan/Index') {
        return {
            eyebrow: 'Tools',
            title: 'Scanner',
            subtitle: 'Scan QR codes from live camera or uploaded images.',
        };
    }

    if (page.component === 'ProjectTracker/Index') {
        return {
            eyebrow: 'Planning',
            title: 'Project Tracker',
            subtitle: 'Follow delivery phases, roadmap progress, and release readiness.',
        };
    }

    return {
        eyebrow: 'Executive Admin',
        title: 'Platform',
        subtitle: 'Manage the inspection system from a single control center.',
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

function toggleSidebarCollapsed() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
}

function closeSidebar() {
    sidebarOpen.value = false;
}
</script>

<template>
    <div class="dashboard-shell dashboard-shell-executive">
        <div
            v-if="sidebarOpen"
            class="dashboard-overlay"
            @click="closeSidebar"
        ></div>

        <aside
            :class="[
                'dashboard-sidebar executive-sidebar',
                sidebarOpen && 'dashboard-sidebar-open',
                sidebarCollapsed && 'executive-sidebar-collapsed',
            ]"
        >
            <button
                type="button"
                class="sidebar-toggle executive-collapse-toggle"
                :title="sidebarCollapsed ? 'Expand navigation' : 'Collapse navigation'"
                @click="toggleSidebarCollapsed"
            >
                <svg
                    v-if="sidebarCollapsed"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    class="h-5 w-5"
                >
                    <path d="M9 6l6 6-6 6" />
                </svg>
                <svg
                    v-else
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    class="h-5 w-5"
                >
                    <path d="M15 6l-6 6 6 6" />
                </svg>
            </button>

            <div class="executive-brand-row">
                <Link
                    :href="navigation.dashboard"
                    class="dashboard-brand dashboard-brand-link executive-brand"
                    title="Open the admin dashboard"
                    @click="closeSidebar"
                >
                    <div class="executive-brand-mark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                            <path d="M4 20h16M6 18V8l6-4 6 4v10M10 11h.01M14 11h.01" />
                        </svg>
                    </div>
                    <div class="executive-brand-copy">
                        <h2 class="executive-brand-title">Equipment Inspection</h2>
                        <p class="executive-brand-subtitle">Admin Suite</p>
                    </div>
                </Link>
            </div>

            <nav class="executive-nav">
                <Link
                    :href="navigation.dashboard"
                    :class="['executive-nav-link', isDashboard && 'executive-nav-link-active']"
                    title="View platform counts and recent activity"
                    @click="closeSidebar"
                >
                    <span class="executive-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                            <path d="M4 13h6V4H4zM14 20h6v-9h-6zM14 10h6V4h-6zM4 20h6v-3H4z" />
                        </svg>
                    </span>
                    <span class="executive-nav-label">Dashboard</span>
                </Link>

                <Link
                    :href="navigation.workplaces"
                    :class="['executive-nav-link', isWorkplaces && 'executive-nav-link-active']"
                    title="Manage workplaces, equipment, and inspection setup"
                    @click="closeSidebar"
                >
                    <span class="executive-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                            <path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h.01M15 10h.01M9 14h.01M15 14h.01" />
                        </svg>
                    </span>
                    <span class="executive-nav-label">Workplaces</span>
                </Link>

                <Link
                    :href="navigation.scanner"
                    :class="['executive-nav-link', isScanner && 'executive-nav-link-active']"
                    title="Scan QR codes from camera or uploaded images"
                    @click="closeSidebar"
                >
                    <span class="executive-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                            <path d="M4 7V5a1 1 0 0 1 1-1h2M20 7V5a1 1 0 0 0-1-1h-2M4 17v2a1 1 0 0 0 1 1h2M20 17v2a1 1 0 0 1-1 1h-2M7 12h10" />
                        </svg>
                    </span>
                    <span class="executive-nav-label">Scanner</span>
                </Link>

                <Link
                    :href="navigation.project_tracker"
                    :class="['executive-nav-link', isTracker && 'executive-nav-link-active']"
                    title="Review roadmap progress and active delivery phases"
                    @click="closeSidebar"
                >
                    <span class="executive-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                            <path d="M4 6h16M4 12h16M4 18h10" />
                        </svg>
                    </span>
                    <span class="executive-nav-label">Roadmap</span>
                </Link>
            </nav>

            <div class="executive-sidebar-cta">
                <BaseButton
                    :href="navigation.create_workplace"
                    variant="primary"
                    class="executive-cta-button w-full justify-center"
                    title="Create a new workplace account and profile"
                >
                    <span class="executive-cta-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4.5 w-4.5">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </span>
                    <span class="executive-cta-label">Add Workplace</span>
                </BaseButton>
            </div>

            <div class="executive-sidebar-footer">
                <Link
                    :href="navigation.home"
                    class="executive-footer-link"
                    title="Open the customer-facing public website"
                    @click="closeSidebar"
                >
                    <span class="executive-nav-icon executive-footer-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4.5 w-4.5">
                            <path d="M3 10.5 12 3l9 7.5M5 9.5V21h14V9.5" />
                        </svg>
                    </span>
                    <span class="executive-footer-label">Public Website</span>
                </Link>

                <button
                    type="button"
                    class="executive-footer-link"
                    title="Sign out of the administrator account"
                    @click="logout"
                >
                    <span class="executive-nav-icon executive-footer-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4.5 w-4.5">
                            <path d="M15 3h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-3" />
                            <path d="M10 17l5-5-5-5" />
                            <path d="M15 12H4" />
                        </svg>
                    </span>
                    <span class="executive-footer-label">Logout</span>
                </button>
            </div>
        </aside>

        <div
            :class="[
                'dashboard-main executive-main',
                sidebarCollapsed && 'executive-main-collapsed',
            ]"
        >
            <header class="dashboard-topbar executive-topbar">
                <div class="dashboard-topbar-inner executive-topbar-inner">
                    <div class="executive-topbar-start">
                        <button
                            type="button"
                            class="sidebar-toggle executive-sidebar-toggle"
                            title="Open navigation"
                            @click="toggleSidebar"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </button>

                        <div>
                            <p class="executive-eyebrow">{{ pageMeta.eyebrow }}</p>
                            <h1 class="executive-title">{{ pageMeta.title }}</h1>
                            <p class="executive-subtitle">{{ pageMeta.subtitle }}</p>
                        </div>
                    </div>

                    <div class="dashboard-topbar-actions executive-topbar-actions">
                        <BaseButton
                            v-for="action in topbarActions"
                            :key="action.label"
                            :href="action.href"
                            :variant="action.variant"
                            :title="action.title"
                        >
                            {{ action.label }}
                        </BaseButton>

                        <div class="executive-user-chip">
                            <div class="executive-user-copy">
                                <p class="text-sm font-semibold">{{ authUser?.name }}</p>
                                <p class="text-xs text-muted">Administrator</p>
                            </div>
                            <div class="executive-avatar">
                                {{ authUser?.name?.slice(0, 1) || 'A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="dashboard-content executive-content">
                <div class="page-wrap executive-page-wrap">
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
