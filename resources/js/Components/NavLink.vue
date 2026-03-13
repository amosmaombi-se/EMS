<template>
    <Link
        :href="href"
        class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group relative"
        :class="[
            active
                ? isDark
                    ? 'bg-[#C0170F]/15 text-[#F0EDE8]'
                    : 'bg-[#C0170F]/08 text-[#C0170F]'
                : isDark
                    ? 'text-[#A09890] hover:bg-[#242018] hover:text-[#F0EDE8]'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
        ]"
        style="font-family:'DM Sans',sans-serif;"
    >
        <!-- Active left bar -->
        <span
            v-if="active"
            class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 rounded-r-full"
            style="background:#C0170F;"
        ></span>

        <!-- Icon -->
        <span
            class="flex-shrink-0 transition-colors duration-200"
            :class="[
                active
                    ? 'text-[#C0170F]'
                    : isDark
                        ? 'text-[#605850] group-hover:text-[#F0EDE8]'
                        : 'text-gray-400 group-hover:text-gray-600'
            ]"
        >
            <slot name="icon" />
        </span>

        <!-- Label -->
        <span class="flex-1 truncate">
            <slot />
        </span>

        <!-- Active dot indicator -->
        <span
            v-if="active"
            class="w-1.5 h-1.5 rounded-full flex-shrink-0"
            style="background:#C0170F;"
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
    },
    isDark: {
        type: Boolean,
        default: false
    }
})
</script>