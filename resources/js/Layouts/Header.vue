<template>
  <header class="flex items-center justify-between px-6 py-4 bg-white border-b-4 border-indigo-600">
    <div class="flex items-center">
      <button @click="toggleSidebar" class="text-gray-500 focus:outline-none lg:hidden">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
          <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <div class="flex items-center space-x-4">
      <!-- Profile Dropdown -->
      <div class="relative" ref="dropdownRef">
        <button @click="toggleDropdown" class="flex items-center space-x-3 focus:outline-none">
          <div class="text-right hidden md:block">
            <p class="text-xs text-gray-500">Principal</p>
            <p class="text-sm font-medium text-gray-700">
              {{ page.props.auth.user.first_name }} {{ page.props.auth.user.last_name }}
            </p>
          </div>
          <img
            class="object-cover w-10 h-10 rounded-full"
            src="https://ui-avatars.com/api/?name=Bima+Dwi+Kurnianto&background=4C51BF&color=fff"
            alt="User avatar"
          />
        </button>

        <!-- Dropdown Menu -->
        <transition name="fade">
          <div
            v-if="isDropdownOpen"
            class="absolute right-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl"
          >
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Profile</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Logout</a>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const isDropdownOpen = ref(false)
const dropdownRef = ref(null)

// Toggle sidebar
const toggleSidebar = () => {
  page.props.showingMobileMenu = !page.props.showingMobileMenu
}

// Toggle dropdown menu
const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

// Click outside handler to close dropdown
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isDropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style>
/* optional fade transition for dropdown */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
