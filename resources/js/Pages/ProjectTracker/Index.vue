<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    summary: {
        type: Object,
        required: true,
    },
    phases: {
        type: Array,
        required: true,
    },
    features: {
        type: Array,
        required: true,
    },
    focus: {
        type: Array,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
});

function statusLabel(status) {
    if (status === 'done') {
        return 'Done';
    }

    if (status === 'in_progress') {
        return 'In Progress';
    }

    return 'Planned';
}

function badgeClass(status) {
    if (status === 'done') {
        return 'roadmap-badge roadmap-badge-done';
    }

    if (status === 'in_progress') {
        return 'roadmap-badge roadmap-badge-progress';
    }

    return 'roadmap-badge roadmap-badge-planned';
}

function statCardClass(key) {
    if (key === 'done') {
        return 'roadmap-stat-card roadmap-stat-card-done';
    }

    if (key === 'in_progress') {
        return 'roadmap-stat-card roadmap-stat-card-progress';
    }

    if (key === 'planned') {
        return 'roadmap-stat-card roadmap-stat-card-planned';
    }

    return 'roadmap-stat-card roadmap-stat-card-total';
}

function featureCategory(label) {
    const value = label.toLowerCase();

    if (value.includes('qr') || value.includes('scan')) {
        return 'Field Workflow';
    }

    if (value.includes('inspection')) {
        return 'Inspection Ops';
    }

    if (value.includes('client') || value.includes('workplace user')) {
        return 'Client Portal';
    }

    if (value.includes('dashboard') || value.includes('auth') || value.includes('equipment') || value.includes('workplace')) {
        return 'Core Platform';
    }

    return 'Platform';
}

function featurePriority(status) {
    if (status === 'done') {
        return {
            label: 'Stable',
            class: 'roadmap-priority roadmap-priority-stable',
        };
    }

    if (status === 'in_progress') {
        return {
            label: 'Active',
            class: 'roadmap-priority roadmap-priority-active',
        };
    }

    return {
        label: 'Queued',
        class: 'roadmap-priority roadmap-priority-queued',
    };
}

function phaseAccentClass(index) {
    const classes = [
        'roadmap-phase-card roadmap-phase-card-primary',
        'roadmap-phase-card roadmap-phase-card-secondary',
        'roadmap-phase-card roadmap-phase-card-tertiary',
        'roadmap-phase-card roadmap-phase-card-danger',
    ];

    return classes[index % classes.length];
}

function phaseTagClass(index) {
    const classes = [
        'roadmap-phase-tag roadmap-phase-tag-primary',
        'roadmap-phase-tag roadmap-phase-tag-secondary',
        'roadmap-phase-tag roadmap-phase-tag-tertiary',
        'roadmap-phase-tag roadmap-phase-tag-danger',
    ];

    return classes[index % classes.length];
}

const totalFocusCompletion = Math.round(((props.stats.done + props.stats.in_progress * 0.6) / Math.max(props.stats.total, 1)) * 100);
</script>

<template>
    <Head title="Project Tracker" />

    <section class="roadmap-page">
        <section class="roadmap-hero">
            <div>
                <p class="roadmap-kicker">Strategic Roadmap</p>
                <h1 class="roadmap-title">Product Roadmap</h1>
                <p class="roadmap-copy">
                    {{ summary.description }}
                </p>
            </div>

            <div class="roadmap-hero-actions">
                <button
                    type="button"
                    class="roadmap-action roadmap-action-secondary"
                >
                    Export Snapshot
                </button>
                <button
                    type="button"
                    class="roadmap-action roadmap-action-primary"
                >
                    Review Schedule
                </button>
            </div>
        </section>

        <section class="roadmap-stats">
            <div :class="statCardClass('done')">
                <div>
                    <p class="roadmap-stat-label">Done</p>
                    <p class="roadmap-stat-value">{{ stats.done }}</p>
                </div>
                <div class="roadmap-stat-icon">✓</div>
            </div>

            <div :class="statCardClass('in_progress')">
                <div>
                    <p class="roadmap-stat-label">In Progress</p>
                    <p class="roadmap-stat-value">{{ stats.in_progress }}</p>
                </div>
                <div class="roadmap-stat-icon">~</div>
            </div>

            <div :class="statCardClass('planned')">
                <div>
                    <p class="roadmap-stat-label">Planned</p>
                    <p class="roadmap-stat-value">{{ stats.planned }}</p>
                </div>
                <div class="roadmap-stat-icon">+</div>
            </div>

            <div :class="statCardClass('total')">
                <div>
                    <p class="roadmap-stat-label">Total</p>
                    <p class="roadmap-stat-value">{{ stats.total }}</p>
                </div>
                <div class="roadmap-stat-icon">#</div>
            </div>
        </section>

        <section class="roadmap-grid">
            <div class="roadmap-main-column">
                <section class="roadmap-table-card">
                    <div class="roadmap-card-header roadmap-card-header-soft">
                        <div>
                            <h2 class="roadmap-section-title">Feature Checklist</h2>
                            <p class="roadmap-section-copy">Current feature delivery across the admin product surface.</p>
                        </div>
                        <span class="roadmap-pill">All Features</span>
                    </div>

                    <div class="roadmap-table-wrap">
                        <table class="roadmap-table">
                            <thead>
                                <tr>
                                    <th>Feature Name</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="feature in features"
                                    :key="feature.label"
                                >
                                    <td class="roadmap-feature-name">{{ feature.label }}</td>
                                    <td>{{ featureCategory(feature.label) }}</td>
                                    <td>
                                        <span :class="featurePriority(feature.status).class">
                                            {{ featurePriority(feature.status).label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span :class="badgeClass(feature.status)">
                                            {{ statusLabel(feature.status) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="roadmap-phase-section">
                    <div class="roadmap-phase-section-header">
                        <div>
                            <h2 class="roadmap-section-title">Execution Phases</h2>
                            <p class="roadmap-section-copy">A cleaner view of what each delivery phase contains and where it currently stands.</p>
                        </div>
                    </div>

                    <div class="roadmap-phase-grid">
                    <article
                        v-for="(phase, index) in phases"
                        :key="phase.title"
                        :class="phaseAccentClass(index)"
                    >
                        <div class="roadmap-phase-topline">
                            <span :class="phaseTagClass(index)">Phase {{ index + 1 }}</span>
                            <span :class="badgeClass(phase.status)">
                                {{ statusLabel(phase.status) }}
                            </span>
                        </div>

                        <div class="roadmap-phase-header">
                            <h3 class="roadmap-phase-title">{{ phase.title }}</h3>
                        </div>

                        <ul class="roadmap-phase-list">
                            <li
                                v-for="item in phase.items"
                                :key="item"
                            >
                                <span class="roadmap-phase-bullet"></span>
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </article>
                    </div>
                </section>
            </div>

            <aside class="roadmap-side-column">
                <section class="roadmap-focus-card">
                    <p class="roadmap-focus-label">Current Focus</p>

                    <div class="roadmap-focus-list">
                        <div
                            v-for="(item, index) in focus"
                            :key="item"
                            class="roadmap-focus-item"
                        >
                            <div :class="['roadmap-focus-bar', `roadmap-focus-bar-${index % 3}`]"></div>
                            <div>
                                <p class="roadmap-focus-title">Priority {{ index + 1 }}</p>
                                <p class="roadmap-focus-copy">{{ item }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="roadmap-progress-block">
                        <div class="roadmap-progress-copy">
                            <span>Sprint Completion</span>
                            <span>{{ totalFocusCompletion }}%</span>
                        </div>
                        <div class="roadmap-progress-track">
                            <div
                                class="roadmap-progress-fill"
                                :style="{ width: `${totalFocusCompletion}%` }"
                            ></div>
                        </div>
                    </div>
                </section>

                <section class="roadmap-quote-card">
                    <div class="roadmap-quote-overlay"></div>
                    <div class="roadmap-quote-content">
                        <p class="roadmap-quote-title">Architecting for the future of field safety.</p>
                        <p class="roadmap-quote-subtitle">Engineering Excellence</p>
                    </div>
                </section>
            </aside>
        </section>
    </section>
</template>
