<template>
    <div :class="['ep-layout', isDark ? 'dark' : '']">
        <component :is="'style'">
            @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

            *, *::before, *::after { box-sizing: border-box; }

            html, body { margin: 0; padding: 0; }

            .ep-layout {
                --crimson: #C0170F;
                --orange:  #F05A00;
                --amber:   #F9B233;
                --navy:    #1D5C96;
                --bg:      #F7F5F2;
                --bg-card: #FFFFFF;
                --border:  #E8E2DA;
                --text:    #1A1410;
                --text-2:  #6B6560;
                --text-3:  #9E9890;
                min-height: 100vh;
                background: var(--bg);
                transition: background .3s;
            }

            .ep-layout.dark {
                --bg:      #0F0D0C;
                --bg-card: #1A1714;
                --border:  #2E2925;
                --text:    #F0EDE8;
                --text-2:  #A09890;
                --text-3:  #605850;
            }

            /* ── Sidebar offset: push main content right on desktop ── */
            .ep-main {
                margin-left: 256px;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                transition: margin-left .3s cubic-bezier(.4,0,.2,1);
            }

            /* ── Header bar ── */
            .ep-header-bar {
                position: sticky;
                top: 0;
                z-index: 30;
                background: var(--bg-card);
                border-bottom: 1px solid var(--border);
                box-shadow: 0 1px 8px rgba(0,0,0,.06);
                transition: background .3s, border-color .3s;
            }

            .ep-layout.dark .ep-header-bar {
                box-shadow: 0 1px 8px rgba(0,0,0,.3);
            }

            /* ── Page content ── */
            .ep-content {
                flex: 1;
                overflow-x: hidden;
            }

            /* ── Scrollbar ── */
            body {
                scrollbar-width: thin;
                scrollbar-color: #D1C9C0 transparent;
            }
            .ep-layout.dark body,
            .ep-layout.dark {
                scrollbar-color: #2E2925 transparent;
            }
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #D1C9C0; border-radius: 3px; }
            .ep-layout.dark ::-webkit-scrollbar-thumb { background: #2E2925; }

            /* ── Mobile: full width, header gets hamburger ── */
            @media (max-width: 1023px) {
                .ep-main { margin-left: 0 !important; }
            }
        </component>

        <!-- Sidebar Navigation -->
        <Navigation
            :showMobile="showMobileMenu"
            :isDark="isDark"
            @close="showMobileMenu = false"
        />

        <!-- Main area -->
        <div class="ep-main">

            <!-- Top Header -->
            <div class="ep-header-bar">
                <Header
                    :isDark="isDark"
                    @toggle-mobile-menu="showMobileMenu = !showMobileMenu"
                />
            </div>

            <!-- Breadcrumbs slot -->
            <slot name="breadcrumbs" />

            <!-- Page content -->
            <main class="ep-content">
                <slot />
            </main>

            <!-- Footer slot -->
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import Navigation from '@/Layouts/Navigation.vue'
import Header     from '@/Layouts/Header.vue'

const isDark         = ref(false)
const showMobileMenu = ref(false)

// ── Sync dark mode from Header toggle ──────────────
const syncDark = (e) => {
    isDark.value = e.detail
}

const handleStorage = (e) => {
    if (e.key === 'ep-dark') isDark.value = e.newValue === 'true'
}

const handleResize = () => {
    if (window.innerWidth >= 1024) showMobileMenu.value = false
}

onMounted(() => {
    // Read persisted preference
    isDark.value = localStorage.getItem('ep-dark') === 'true'

    window.addEventListener('ep-dark-toggle', syncDark)
    window.addEventListener('storage', handleStorage)
    window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
    window.removeEventListener('ep-dark-toggle', syncDark)
    window.removeEventListener('storage', handleStorage)
    window.removeEventListener('resize', handleResize)
})
</script>