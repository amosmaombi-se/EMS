<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <h1 class="font-bold text-3xl text-gray-900 leading-tight mb-2">My Bookings</h1>
                    <p class="text-gray-600">Manage all your event bookings in one place</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link :href="route('bookings.create')" 
                          class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Booking
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                    <!-- Total Bookings -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Total Bookings
                                </p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats?.total || 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    Total Revenue
                                </p>
                                <p class="text-2xl font-bold text-gray-900">${{ formatCurrency(stats?.total_revenue || 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Bookings -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Pending
                                </p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats?.pending || 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmed Bookings -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Confirmed
                                </p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats?.confirmed || 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Overdue Payments -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Overdue
                                </p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats?.overdue || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">Booking List</h3>
                                <p class="text-sm text-gray-500">Filter and manage your event bookings</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="resetFilters" 
                                        class="inline-flex items-center px-3 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                    Reset Filters
                                </button>
                                <button @click="showAdvancedFilters = !showAdvancedFilters" 
                                        class="inline-flex items-center px-3 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                    Advanced Filters
                                </button>
                            </div>
                        </div>

                        <!-- Quick Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input v-model="filters.search" 
                                           @input="search"
                                           type="search" 
                                           placeholder="Search bookings..."
                                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select v-model="filters.status" @change="search" 
                                        class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                                <select v-model="filters.payment_status" @change="search" 
                                        class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Payment</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partially_paid">Partially Paid</option>
                                    <option value="paid">Paid</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                                <input v-model="filters.start_date" 
                                       @change="search"
                                       type="date" 
                                       class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                                <input v-model="filters.end_date" 
                                       @change="search"
                                       type="date" 
                                       class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Advanced Filters -->
                        <div v-if="showAdvancedFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                                <select v-model="filters.event_type" @change="search" 
                                        class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Types</option>
                                    <option value="wedding">Wedding</option>
                                    <option value="corporate">Corporate</option>
                                    <option value="birthday">Birthday</option>
                                    <option value="conference">Conference</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Min Amount</label>
                                <input v-model="filters.min_amount" 
                                       @input="search"
                                       type="number" 
                                       min="0"
                                       placeholder="0"
                                       class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Amount</label>
                                <input v-model="filters.max_amount" 
                                       @input="search"
                                       type="number" 
                                       min="0"
                                       placeholder="Any"
                                       class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Booking Details
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Event Information
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Financial Details
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="booking in bookings.data" :key="booking.id" 
                                    class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                                     :class="getBookingColorClass(booking.status)">
                                                    <span class="text-white font-bold text-sm">
                                                        {{ booking.booking_number.substring(0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ booking.booking_number }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ formatDate(booking.booking_date) }}
                                                </div>
                                                <div v-if="booking.guest_count" class="text-xs text-gray-400 mt-1">
                                                    {{ booking.guest_count }} guests
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 line-clamp-1">
                                                {{ booking.event?.title || 'Event' }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <div class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    {{ booking.venue?.name || 'Venue TBD' }}
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ formatDate(booking.event_date) }} • {{ formatTime(booking.start_time) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="text-sm font-semibold text-gray-900">
                                                ${{ formatCurrency(booking.total_amount) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Paid: ${{ formatCurrency(booking.paid_amount) }}
                                            </div>
                                            <div v-if="booking.balance > 0" class="text-xs font-medium text-red-600">
                                                Balance: ${{ formatCurrency(booking.balance) }}
                                            </div>
                                            <div v-else class="text-xs font-medium text-green-600">
                                                Fully Paid
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize w-fit"
                                                  :class="getStatusClass(booking.status)">
                                                {{ booking.status.replace('_', ' ') }}
                                            </span>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize w-fit"
                                                  :class="getPaymentStatusClass(booking.payment_status)">
                                                {{ booking.payment_status.replace('_', ' ') }}
                                            </span>
                                        </div>
                                        <div v-if="booking.due_date" class="text-xs text-gray-500 mt-2">
                                            Due: {{ formatDate(booking.due_date) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="route('bookings.show', booking.id)" 
                                                  class="text-indigo-600 hover:text-indigo-900"
                                                  title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </Link>
                                            <Link :href="route('bookings.edit', booking.id)" 
                                                  class="text-green-600 hover:text-green-900"
                                                  title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </Link>
                                            <button @click="downloadInvoice(booking)" 
                                                    class="text-purple-600 hover:text-purple-900"
                                                    title="Download Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="bookings.data.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                        <p class="text-gray-500 mb-6">Get started by creating your first booking</p>
                        <Link :href="route('bookings.create')" 
                              class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create First Booking
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.data.length > 0" 
                     class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                            Showing <span class="font-medium">{{ bookings.from }}</span> to 
                            <span class="font-medium">{{ bookings.to }}</span> of 
                            <span class="font-medium">{{ bookings.total }}</span> bookings
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" 
                                 aria-label="Pagination">
                                <Link v-for="link in bookings.links" 
                                      :key="link.label"
                                      :href="link.url || '#'"
                                      :class="[
                                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                          link.active 
                                            ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' 
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                          link.label === 'Previous' ? 'rounded-l-lg' : '',
                                          link.label === 'Next' ? 'rounded-r-lg' : ''
                                      ]"
                                      v-html="link.label"
                                      preserve-scroll>
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, reactive, watch } from 'vue'
import { debounce } from 'lodash'

const props = defineProps({
    bookings: Object,
    filters: Object,
    stats: Object
})

const showAdvancedFilters = ref(false)

const appliedFilters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    payment_status: props.filters.payment_status || '',
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
    event_type: props.filters.event_type || '',
    min_amount: props.filters.min_amount || '',
    max_amount: props.filters.max_amount || '',
})

// Watch filters and apply debounced search
watch(appliedFilters, debounce(() => {
    router.get(route('bookings.index'), appliedFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}, 300), { deep: true })

// Update appliedFilters when props.filters change
watch(() => props.filters, (newFilters) => {
    Object.assign(appliedFilters, newFilters)
}, { deep: true, immediate: true })

const search = () => {
    // Debouncing is handled by the watcher
}

const resetFilters = () => {
    Object.assign(appliedFilters, {
        search: '',
        status: '',
        payment_status: '',
        start_date: '',
        end_date: '',
        event_type: '',
        min_amount: '',
        max_amount: '',
    })
}

const downloadInvoice = (booking) => {
    router.get(route('bookings.invoice', booking.id))
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (time) => {
    if (!time) return ''
    try {
        if (time.includes('T') || time.includes(' ')) {
            return new Date(time).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit'
            })
        }
        const [hours, minutes] = time.split(':')
        const date = new Date()
        date.setHours(parseInt(hours), parseInt(minutes || 0))
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit'
        })
    } catch (e) {
        return time
    }
}

const formatCurrency = (amount) => {
    return parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}

const getBookingColorClass = (status) => {
    const colors = {
        pending: 'bg-yellow-500',
        confirmed: 'bg-green-500',
        in_progress: 'bg-blue-500',
        completed: 'bg-purple-500',
        cancelled: 'bg-red-500'
    }
    return colors[status] || 'bg-gray-500'
}

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        in_progress: 'bg-blue-100 text-blue-800',
        completed: 'bg-purple-100 text-purple-800',
        cancelled: 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getPaymentStatusClass = (status) => {
    const classes = {
        unpaid: 'bg-red-100 text-red-800',
        partially_paid: 'bg-yellow-100 text-yellow-800',
        paid: 'bg-green-100 text-green-800',
        refunded: 'bg-gray-100 text-gray-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>