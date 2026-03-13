<template>
    <component :is="'style'">
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Mono:wght@400;500&display=swap');
    </component>

    <header :class="[
        'flex items-center justify-between px-6 py-0 border-b transition-colors duration-300 relative z-10',
        isDark
            ? 'bg-[#1A1714] border-[#2E2925]'
            : 'bg-white border-gray-100'
    ]" style="height:64px; font-family:'DM Sans',sans-serif;">

        <!-- Left: Hamburger + Page title -->
        <div class="flex items-center gap-4">
            <!-- Mobile menu toggle -->
            <button @click="$emit('toggle-mobile-menu')"
                :class="[
                    'p-2 rounded-lg transition-colors lg:hidden',
                    isDark ? 'text-[#A09890] hover:bg-[#242018]' : 'text-gray-500 hover:bg-gray-100'
                ]">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                    <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <!-- Breadcrumb / page label -->
            <div class="hidden sm:flex items-center gap-2">
                <span style="font-family:'DM Mono',monospace;"
                    :class="['text-xs font-medium tracking-widest uppercase', isDark ? 'text-[#605850]' : 'text-gray-400']">
                    Event Portal
                </span>
                <span :class="isDark ? 'text-[#2E2925]' : 'text-gray-200'">/</span>
                <span style="font-family:'DM Mono',monospace;"
                    :class="['text-xs font-semibold tracking-wider uppercase', isDark ? 'text-[#F9B233]' : 'text-red-700']">
                    {{ currentPageTitle }}
                </span>
            </div>
        </div>

        <!-- Right: Clock + Notifications + Dark toggle + Profile -->
        <div class="flex items-center gap-2 sm:gap-3">

            <!-- Live mini clock (hidden on xs) -->
            <div class="hidden md:flex flex-col items-end leading-tight mr-1">
                <span style="font-family:'DM Mono',monospace; background:linear-gradient(135deg,#F05A00,#F9B233); -webkit-background-clip:text; -webkit-text-fill-color:transparent;"
                    class="text-sm font-bold tracking-wider">{{ currentTime }}</span>
                <span style="font-family:'DM Mono',monospace;"
                    :class="['text-[10px]', isDark ? 'text-[#605850]' : 'text-gray-400']">{{ currentDate }}</span>
            </div>

            <!-- Divider -->
            <div :class="['hidden md:block w-px h-8', isDark ? 'bg-[#2E2925]' : 'bg-gray-100']"></div>

            <!-- Notification bell -->
            <div class="relative">
                <button @click="toggleNotifications" :class="[
                    'relative p-2 rounded-xl transition-colors',
                    isDark ? 'text-[#A09890] hover:bg-[#242018]' : 'text-gray-500 hover:bg-gray-50'
                ]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <!-- Badge -->
                    <span v-if="notificationCount > 0"
                        class="absolute top-1 right-1 w-4 h-4 text-white text-[9px] font-bold rounded-full flex items-center justify-center"
                        style="background:#C0170F; font-family:'DM Mono',monospace;">
                        {{ notificationCount }}
                    </span>
                </button>

                <!-- Notifications dropdown -->
                <transition name="ep-fade">
                    <div v-if="showNotifications" :class="[
                        'absolute right-0 top-full mt-2 w-72 rounded-2xl shadow-xl border overflow-hidden z-50',
                        isDark ? 'bg-[#1A1714] border-[#2E2925]' : 'bg-white border-gray-100'
                    ]">
                        <div :class="['px-4 py-3 border-b flex items-center justify-between', isDark ? 'border-[#2E2925] bg-[#242018]' : 'border-gray-100 bg-gray-50']">
                            <span style="font-family:'DM Mono',monospace;" :class="['text-xs font-bold tracking-widest uppercase', isDark ? 'text-[#A09890]' : 'text-gray-500']">Notifications</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:rgba(192,23,15,.12); color:#C0170F; font-family:'DM Mono',monospace;">{{ notificationCount }} new</span>
                        </div>
                        <div class="divide-y" :class="isDark ? 'divide-[#2E2925]' : 'divide-gray-50'">
                            <div v-for="n in notifications" :key="n.id"
                                :class="['px-4 py-3 flex gap-3 items-start cursor-pointer transition-colors', isDark ? 'hover:bg-[#242018]' : 'hover:bg-gray-50']">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm flex-shrink-0 mt-0.5"
                                    :style="{ background: n.color + '18' }">{{ n.icon }}</div>
                                <div class="flex-1 min-w-0">
                                    <p :class="['text-xs font-semibold truncate', isDark ? 'text-[#F0EDE8]' : 'text-gray-700']">{{ n.title }}</p>
                                    <p :class="['text-[11px] mt-0.5', isDark ? 'text-[#605850]' : 'text-gray-400']">{{ n.time }}</p>
                                </div>
                                <div v-if="n.unread" class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background:#C0170F"></div>
                            </div>
                        </div>
                        <div :class="['px-4 py-2.5 text-center border-t', isDark ? 'border-[#2E2925] bg-[#242018]' : 'border-gray-50 bg-gray-50']">
                            <a href="#" style="font-family:'DM Mono',monospace; color:#C0170F;" class="text-xs font-semibold hover:underline">View all notifications →</a>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Dark mode toggle -->
            <button @click="toggleDark" :class="[
                'p-2 rounded-xl transition-colors',
                isDark ? 'text-[#F9B233] hover:bg-[#242018]' : 'text-gray-400 hover:bg-gray-50'
            ]" :title="isDark ? 'Switch to light' : 'Switch to dark'">
                <!-- Moon icon (dark mode on) -->
                <svg v-if="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
                <!-- Sun icon (light mode) -->
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
            </button>

            <!-- Divider -->
            <div :class="['w-px h-8', isDark ? 'bg-[#2E2925]' : 'bg-gray-100']"></div>

            <!-- Profile dropdown -->
            <div class="relative" ref="dropdownRef">
                <button @click="toggleDropdown" class="flex items-center gap-2.5 focus:outline-none group">
                    <!-- Name (hidden on small) -->
                    <div class="text-right hidden sm:block">
                        <p style="font-family:'DM Mono',monospace;" :class="['text-[10px] uppercase tracking-widest', isDark ? 'text-[#605850]' : 'text-gray-400']">
                            {{ page.props.auth.user?.roles?.[0] || 'Admin' }}
                        </p>
                        <p :class="['text-sm font-semibold leading-tight', isDark ? 'text-[#F0EDE8]' : 'text-gray-800']"
                            style="font-family:'DM Sans',sans-serif;">
                            {{ page.props.auth.user?.first_name }} {{ page.props.auth.user?.last_name }}
                        </p>
                    </div>
                    <!-- Avatar -->
                    <div class="relative">
                        <img
                            :src="page.props.auth.user?.avatar || `https://ui-avatars.com/api/?name=${page.props.auth.user?.first_name}+${page.props.auth.user?.last_name}&background=C0170F&color=fff`"
                            alt="avatar"
                            class="w-9 h-9 rounded-xl object-cover ring-2 ring-offset-1 transition-all"
                            :class="isDark ? 'ring-[#C0170F]/40 ring-offset-[#1A1714]' : 'ring-[#C0170F]/20 ring-offset-white group-hover:ring-[#C0170F]/60'"
                        />
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 rounded-full"
                            :class="isDark ? 'border-[#1A1714]' : 'border-white'"></span>
                    </div>
                    <!-- Chevron -->
                    <svg :class="['w-4 h-4 transition-transform duration-200', isDropdownOpen ? 'rotate-180' : '', isDark ? 'text-[#605850]' : 'text-gray-400']"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Profile dropdown menu -->
                <transition name="ep-fade">
                    <div v-if="isDropdownOpen" :class="[
                        'absolute right-0 top-full mt-2 w-52 rounded-2xl shadow-xl border overflow-hidden z-50',
                        isDark ? 'bg-[#1A1714] border-[#2E2925]' : 'bg-white border-gray-100'
                    ]">
                        <!-- User info -->
                        <div :class="['px-4 py-3 border-b', isDark ? 'border-[#2E2925] bg-[#242018]' : 'border-gray-50 bg-gray-50']">
                            <p :class="['text-xs font-semibold', isDark ? 'text-[#F0EDE8]' : 'text-gray-800']">
                                {{ page.props.auth.user?.first_name }} {{ page.props.auth.user?.last_name }}
                            </p>
                            <p :class="['text-[11px] truncate mt-0.5', isDark ? 'text-[#605850]' : 'text-gray-400']">{{ page.props.auth.user?.email }}</p>
                        </div>

                        <!-- Menu items -->
                        <div class="py-1">
                            <Link :href="route('profile.edit')"
                                :class="['flex items-center gap-3 px-4 py-2.5 text-sm transition-colors', isDark ? 'text-[#A09890] hover:bg-[#242018] hover:text-[#F0EDE8]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                My Profile
                            </Link>
                            <Link v-if="hasRole('admin')" :href="route('settings.index')"
                                :class="['flex items-center gap-3 px-4 py-2.5 text-sm transition-colors', isDark ? 'text-[#A09890] hover:bg-[#242018] hover:text-[#F0EDE8]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900']">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Settings
                            </Link>
                        </div>

                        <div :class="['border-t py-1', isDark ? 'border-[#2E2925]' : 'border-gray-100']">
                            <button @click="logout"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors text-red-600 hover:bg-red-50"
                                :class="isDark ? 'hover:bg-[#C0170F]/10' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'

const page = usePage()

// Props & emits
defineEmits(['toggle-mobile-menu'])

// ── Dark mode (shared via localStorage) ────────────
const isDark = ref(localStorage.getItem('ep-dark') === 'true')
const toggleDark = () => {
    isDark.value = !isDark.value
    localStorage.setItem('ep-dark', isDark.value)
    document.documentElement.classList.toggle('ep-dark', isDark.value)
    window.dispatchEvent(new CustomEvent('ep-dark-toggle', { detail: isDark.value }))
}

// ── Expose isDark for layout ───────────────────────
defineExpose({ isDark })

// ── Live clock ─────────────────────────────────────
const currentTime = ref('')
const currentDate = ref('')
const updateClock = () => {
    const now = new Date()
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
    currentTime.value = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    currentDate.value = `${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`
}
let clockTimer = null
onMounted(() => { updateClock(); clockTimer = setInterval(updateClock, 1000) })
onBeforeUnmount(() => clearInterval(clockTimer))

// ── Page title from route ──────────────────────────
const currentPageTitle = computed(() => {
    const name = page.component?.split('/').pop()?.replace(/([A-Z])/g, ' $1').trim() || 'Dashboard'
    return name
})

// ── Profile dropdown ───────────────────────────────
const isDropdownOpen = ref(false)
const dropdownRef = ref(null)
const toggleDropdown = () => { isDropdownOpen.value = !isDropdownOpen.value; showNotifications.value = false }

// ── Notifications ──────────────────────────────────
const showNotifications = ref(false)
const toggleNotifications = () => { showNotifications.value = !showNotifications.value; isDropdownOpen.value = false }
const notificationCount = ref(3)
const notifications = ref([
    { id: 1, icon: '💍', color: '#C0170F', title: 'Sunset Wedding — guest confirmed', time: '2 mins ago', unread: true },
    { id: 2, icon: '🎤', color: '#1D5C96', title: 'Tech Summit — new booking received', time: '18 mins ago', unread: true },
    { id: 3, icon: '📋', color: '#F05A00', title: 'Report ready for Q1 events', time: '1 hr ago', unread: true },
])

// ── Roles helper ───────────────────────────────────
const hasRole = (role) => (page.props.auth.user?.roles || []).includes(role)

// ── Logout ─────────────────────────────────────────
const logout = () => router.post(route('logout'))

// ── Click outside ──────────────────────────────────
const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) isDropdownOpen.value = false
    showNotifications.value = false
}
onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
.ep-fade-enter-active, .ep-fade-leave-active { transition: opacity .15s, transform .15s; }
.ep-fade-enter-from, .ep-fade-leave-to { opacity: 0; transform: translateY(-6px); }
</style>