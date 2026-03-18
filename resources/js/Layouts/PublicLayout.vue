<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import BaseButton from '@/Components/Base/BaseButton.vue';

const page = usePage();

const links = computed(() => page.props.links ?? {});
const authUser = computed(() => page.props.auth?.user ?? null);
const navigation = computed(() => page.props.navigation ?? {});

function logout() {
    router.post(navigation.value.logout);
}
</script>

<template>
    <div class="public-shell">
        <header class="public-header">
            <div class="public-header-inner">
                <Link
                    :href="links.home"
                    class="public-brand"
                >
                    <span class="public-brand-mark">EI</span>
                    <span class="public-brand-copy">
                        <span class="public-brand-title">Equipment Inspection</span>
                        <span class="public-brand-subtitle">Workplace compliance platform</span>
                    </span>
                </Link>

                <nav class="public-nav">
                    <template v-if="authUser">
                        <Link
                            :href="links.portal || navigation.dashboard"
                            class="public-nav-link"
                        >
                            Dashboard
                        </Link>

                        <BaseButton
                            variant="secondary"
                            @click="logout"
                        >
                            Sign out
                        </BaseButton>
                    </template>

                    <template v-else>
                        <Link
                            :href="links.login"
                            class="public-nav-link"
                        >
                            Sign in
                        </Link>

                        <BaseButton :href="links.register">
                            Create workplace account
                        </BaseButton>
                    </template>
                </nav>
            </div>
        </header>

        <main class="public-main">
            <slot />
        </main>
    </div>
</template>
