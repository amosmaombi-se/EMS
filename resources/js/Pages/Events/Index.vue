<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">Events</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage and organize all your events
                    </p>
                </div>
                <Link :href="route('events.create')"
                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Event
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div v-if="events.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Events Card -->
                    <div
                        class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-md p-5 border border-blue-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Total Events</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ events.total }}</p>
                                <p class="text-xs text-blue-600 font-medium mt-1">All events combined</p>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmed Events Card -->
                    <div
                        class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-md p-5 border border-green-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-green-700 uppercase tracking-wide">Confirmed</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{events.data.filter(e => e.status ===
                                    'confirmed').length }}</p>
                                <p class="text-xs text-green-600 font-medium mt-1">Ready to start</p>
                            </div>
                        </div>
                    </div>

                    <!-- Ongoing Events Card -->
                    <div
                        class="bg-gradient-to-br from-amber-50 to-white rounded-xl shadow-md p-5 border border-amber-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-amber-700 uppercase tracking-wide">Ongoing</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{events.data.filter(e => e.status ===
                                    'ongoing').length }}</p>
                                <p class="text-xs text-amber-600 font-medium mt-1">Currently active</p>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Events Card -->
                    <div
                        class="bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-md p-5 border border-purple-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide">Completed</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{events.data.filter(e => e.status ===
                                    'completed').length }}</p>
                                <p class="text-xs text-purple-600 font-medium mt-1">Successfully finished</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filters Section -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Filter Events</h3>
                            <p class="text-xs text-gray-500">Refine your search using the filters below</p>
                        </div>
                        <button v-if="hasActiveFilters" @click="resetFilters"
                            class="inline-flex items-center text-sm text-red-600 hover:text-red-800 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Filters
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 mt-4">
                        <!-- Search -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input v-model="form.search" @input="search" type="text" placeholder="Search events..."
                                class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm placeholder-gray-400">
                        </div>

                        <!-- Status -->
                        <select v-model="form.status" @change="search"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="planning">Planning</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <!-- Event Type -->
                        <select v-model="form.event_type_id" @change="search"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                            <option value="">All Types</option>
                            <option v-for="type in eventTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>

                        <!-- Start Date -->
                        <div>
                            <input v-model="form.start_date" @change="search" type="date"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                        </div>

                        <!-- End Date -->
                        <div>
                            <input v-model="form.end_date" @change="search" type="date"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                        </div>
                    </div>
                </div>

                <!-- Results Count -->
                <div v-if="events.data.length > 0" class="mb-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-gray-600">
                            Showing <span class="font-medium text-gray-900">{{ events.from }}</span> to
                            <span class="font-medium text-gray-900">{{ events.to }}</span> of
                            <span class="font-medium text-gray-900">{{ events.total }}</span> events
                        </p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Sort by:</span>
                            <select
                                class="text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500">
                                <option>Date (Newest)</option>
                                <option>Date (Oldest)</option>
                                <option>Name (A-Z)</option>
                                <option>Name (Z-A)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Events Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="event in events.data" :key="event.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all overflow-hidden group">

                        <div class="border-l-4" :class="getStatusBorderClass(event.status)"></div>

                        <div class="p-4">
                            <!-- Status & Type Badges -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full"
                                    :class="getStatusClass(event.status)">
                                    {{ event.status.charAt(0).toUpperCase() + event.status.slice(1) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ event.event_type?.name }}
                                </span>
                            </div>

                            <!-- Event Title -->
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-3 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ event.title }}
                            </h3>

                            <!-- Event Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="truncate">{{ formatDate(event.event_date) }}</span>
                                </div>

                                <div v-if="event.city" class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="truncate">{{ event.city }}, {{ event.country }}</span>
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>{{ event.expected_guests }} guests</span>
                                </div>
                            </div>

                            <!-- Budget & Action -->
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                
                                <div class="flex items-center gap-2">
                                    <Link :href="route('events.edit', event.id)"
                                        class="inline-flex items-center p-1.5 text-gray-400 hover:text-indigo-600 transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <Link :href="route('events.show', event.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded hover:bg-indigo-100 transition-colors">
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="events.data.length === 0"
                    class="bg-white rounded-lg shadow-sm p-8 text-center border border-gray-200">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No events found</h3>
                        <p class="text-sm text-gray-600 mb-6">
                            {{ hasActiveFilters ? 'Try adjusting your filters to find what you\'re looking for.' : 'Get started by creating your first event.' }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <Link v-if="!hasActiveFilters" :href="route('events.create')"
                                class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Create First Event
                            </Link>
                            <button v-else @click="resetFilters"
                                class="inline-flex items-center px-4 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="events.data.length > 0" class="mt-6">
                    <Pagination :links="events.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    events: Object,
    eventTypes: Array,
    filters: Object
})

const form = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    event_type_id: props.filters.event_type_id || '',
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || ''
})

const hasActiveFilters = computed(() => {
    return form.search || form.status || form.event_type_id || form.start_date || form.end_date
})

const search = () => {
    router.get(route('events.index'), form, {
        preserveState: true,
        preserveScroll: true
    })
}

const resetFilters = () => {
    form.search = ''
    form.status = ''
    form.event_type_id = ''
    form.start_date = ''
    form.end_date = ''
    search()
}

const formatDate = (date) => {
    if (!date) return 'No date set'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800',
        planning: 'bg-blue-100 text-blue-800',
        confirmed: 'bg-green-100 text-green-800',
        ongoing: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-purple-100 text-purple-800',
        cancelled: 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusBorderClass = (status) => {
    const classes = {
        draft: 'border-gray-400',
        planning: 'border-blue-400',
        confirmed: 'border-green-400',
        ongoing: 'border-yellow-400',
        completed: 'border-purple-400',
        cancelled: 'border-red-400'
    }
    return classes[status] || 'border-gray-400'
}
</script>