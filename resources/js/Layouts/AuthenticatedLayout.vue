<template>
    <div class="flex h-screen bg-gray-50 font-roboto">
        <!-- Mobile Overlay -->
        <transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showMobileMenu"
                @click="showMobileMenu = false"
                class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
            ></div>
        </transition>

        <!-- Sidebar Navigation -->
        <Navigation :show-mobile="showMobileMenu" @close="showMobileMenu = false" />

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Header -->
            <Header @toggle-mobile-menu="showMobileMenu = !showMobileMenu" />

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container px-4 sm:px-6 py-6 mx-auto max-w-12xl">

                    <div v-if="$slots.breadcrumbs" class="mb-4">
                        <slot name="breadcrumbs" />
                    </div>

                    <!-- Page Header -->
                    <div v-if="$slots.header" class="mb-6">
                        <slot name="header" />
                    </div>

                    <!-- Main Content -->
                    <div class="transition-all duration-300">
                        <slot />
                    </div>
                </div>
            </main>

            <!-- Footer (optional) -->
            <footer v-if="$slots.footer" class="bg-white border-t border-gray-200 py-4 px-6">
                <slot name="footer" />
            </footer>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Header from '@/Layouts/Header.vue';
import Navigation from '@/Layouts/Navigation.vue';

// Mobile menu state
const showMobileMenu = ref(false);

// Close mobile menu on window resize to desktop size
if (typeof window !== 'undefined') {
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            showMobileMenu.value = false;
        }
    });
}
</script>

<style scoped>
/* Custom scrollbar for main content area */
main::-webkit-scrollbar {
    width: 8px;
}

main::-webkit-scrollbar-track {
    background: #f8fafc;
}

main::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

main::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth content transitions */
main {
    scroll-behavior: smooth;
}

/* Prevent layout shift */
.container {
    min-height: calc(100vh - 64px); /* Adjust based on header height */
}
</style>