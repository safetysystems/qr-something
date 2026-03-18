<template>
    <div class="guest-shell">
        <div class="guest-header">
            <a
                href="/"
                class="guest-brand"
            >
                <span class="dashboard-brand-mark">EI</span>
                <span>
                    <span class="guest-brand-title">Equipment Inspection</span>
                    <span class="guest-brand-subtitle">Secure client and admin access</span>
                </span>
            </a>
        </div>

        <main class="guest-wrap">
            <slot />
        </main>
    </div>
</template>
