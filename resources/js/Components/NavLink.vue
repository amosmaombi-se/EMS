<template>
    <Link 
        :href="href"
        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group"
        :class="[
            active 
                ? 'bg-blue-50 text-blue-600 shadow-sm' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
        ]"
    >
        <span 
            class="mr-3 transition-colors"
            :class="[
                active 
                    ? 'text-blue-600' 
                    : 'text-gray-400 group-hover:text-gray-600'
            ]"
        >
            <slot name="icon" />
        </span>
        
        <span class="flex-1">
            <slot />
        </span>

        <!-- Active indicator -->
        <span 
            v-if="active"
            class="w-1.5 h-1.5 bg-blue-600 rounded-full"
        ></span>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: {
        type: String,
        required: true
    },
    active: {
        type: Boolean,
        default: false
    }
})
</script>

<style scoped>
/* Additional hover effects */
.group:hover span {
    transition: all 0.2s ease;
}
</style>