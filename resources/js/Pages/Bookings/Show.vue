<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <Link :href="route('bookings.index')"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h1 class="font-bold text-2xl text-gray-900">{{ booking.booking_number }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                            :class="getStatusClass(booking.status)">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                :class="getStatusDot(booking.status)"></span>
                            {{ booking.status.replace('_', ' ') }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
                            :class="getPaymentStatusClass(booking.payment_status)">
                            {{ booking.payment_status.replace('_', ' ') }}
                        </span>
                    </div>
                    <p class="text-gray-500 text-sm mt-1 ml-8">
                        Created {{ formatDate(booking.booking_date) }}
                        <span v-if="booking.due_date">
                            · Due
                            <span :class="isPastDue(booking.due_date) ? 'text-red-500 font-semibold' : ''">
                                {{ formatDate(booking.due_date) }}
                            </span>
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('bookings.edit', booking.id)"
                        class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-sm font-medium rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Booking
                    </Link>
                    <button @click="showDeleteModal = true"
                        class="inline-flex items-center px-4 py-2.5 border border-red-200 text-sm font-medium rounded-lg text-red-600 bg-white hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- ══════════════════════════════════════════════════════ -->
                    <!-- Main Content (left 2 cols)                            -->
                    <!-- ══════════════════════════════════════════════════════ -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- ── Event & Venue ──────────────────────────────── -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h2 class="text-sm font-semibold text-gray-900">Event Details</h2>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Event -->
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Event</p>
                                    <p class="text-base font-semibold text-gray-900">
                                        {{ booking.event?.title || '—' }}
                                    </p>
                                    <p v-if="booking.event?.event_type"
                                        class="text-sm text-gray-500 mt-0.5 capitalize">
                                        {{ booking.event.event_type.replace('_', ' ') }}
                                    </p>
                                    <div class="mt-3 flex flex-wrap gap-4 text-sm text-gray-600">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ formatDate(booking.event_date) }}
                                        </div>
                                        <div v-if="booking.event_end_date" class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                            {{ formatDate(booking.event_end_date) }}
                                        </div>
                                        <div v-if="booking.start_time" class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ formatTime(booking.start_time) }}
                                            <span v-if="booking.end_time">– {{ formatTime(booking.end_time) }}</span>
                                        </div>
                                        <div v-if="booking.guest_count" class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            {{ booking.guest_count }} guests
                                        </div>
                                    </div>
                                </div>
                                <!-- Venue -->
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Venue</p>
                                    <div v-if="booking.venue"
                                        class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                        <p class="text-sm font-semibold text-gray-900">{{ booking.venue.name }}</p>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ booking.venue.city }}, {{ booking.venue.state }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5 capitalize">
                                            {{ booking.venue.type?.replace('_', ' ') }}
                                            · Cap. {{ booking.venue.capacity_max?.toLocaleString() }}
                                        </p>
                                    </div>
                                    <p v-else class="text-sm text-gray-400 italic">No venue assigned</p>
                                </div>
                            </div>
                        </div>

                        <!-- ── Booked Items ────────────────────────────────── -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <h2 class="text-sm font-semibold text-gray-900">Booked Items</h2>
                                </div>
                                <span class="text-xs text-gray-400">
                                    {{ (booking.items ?? []).length }} item{{ (booking.items ?? []).length !== 1 ? 's' : '' }}
                                </span>
                            </div>

                            <div v-if="!booking.items || booking.items.length === 0"
                                class="px-6 py-10 text-center">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <p class="text-sm text-gray-400">No items added to this booking</p>
                            </div>

                            <div v-else class="divide-y divide-gray-50">
                                <div v-for="item in booking.items" :key="item.id"
                                    class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                {{ item.itemable?.name || 'Service' }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ item.category || item.itemable?.category || 'Vendor Service' }}
                                                <span v-if="item.vendor_name">· {{ item.vendor_name }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6 flex-shrink-0 ml-4">
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400">Unit price</p>
                                            <p class="text-sm text-gray-600">
                                                TZS {{ formatCurrency(item.unit_price) }}
                                            </p>
                                        </div>
                                        <div class="text-center w-12">
                                            <p class="text-xs text-gray-400">Qty</p>
                                            <p class="text-sm font-medium text-gray-700">{{ item.quantity }}</p>
                                        </div>
                                        <div class="text-right w-32">
                                            <p class="text-xs text-gray-400">Subtotal</p>
                                            <p class="text-sm font-bold text-gray-900">
                                                TZS {{ formatCurrency(item.unit_price * item.quantity) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items total footer -->
                            <div v-if="booking.items && booking.items.length > 0"
                                class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Items Total</span>
                                    <span class="text-sm font-bold text-gray-900">
                                        TZS {{ formatCurrency(itemsTotal) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- ── Notes ──────────────────────────────────────── -->
                        <div v-if="booking.customer_notes || booking.internal_notes"
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h2 class="text-sm font-semibold text-gray-900">Notes</h2>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-if="booking.customer_notes">
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">
                                        Customer Notes
                                    </p>
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                        {{ booking.customer_notes }}
                                    </p>
                                </div>
                                <div v-if="booking.internal_notes">
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Internal Notes (Staff Only)
                                    </p>
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                        {{ booking.internal_notes }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- ── Activity Timeline ───────────────────────────── -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h2 class="text-sm font-semibold text-gray-900">Activity</h2>
                            </div>
                            <div class="p-6">
                                <ol class="relative border-l border-gray-200 space-y-6 ml-3">
                                    <!-- Created -->
                                    <li class="ml-6">
                                        <span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full ring-4 ring-white">
                                            <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <h3 class="text-sm font-semibold text-gray-900">Booking Created</h3>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ formatDateTime(booking.created_at) }}
                                        </p>
                                        <p v-if="booking.user" class="text-xs text-gray-500 mt-0.5">
                                            by {{ booking.user.name }}
                                        </p>
                                    </li>
                                    <!-- Confirmed -->
                                    <li v-if="booking.confirmed_at" class="ml-6">
                                        <span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-green-100 rounded-full ring-4 ring-white">
                                            <svg class="w-3 h-3 text-green-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <h3 class="text-sm font-semibold text-gray-900">Confirmed</h3>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ formatDateTime(booking.confirmed_at) }}
                                        </p>
                                    </li>
                                    <!-- Last updated -->
                                    <li v-if="booking.updated_at !== booking.created_at" class="ml-6">
                                        <span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full ring-4 ring-white">
                                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </span>
                                        <h3 class="text-sm font-semibold text-gray-900">Last Updated</h3>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ formatDateTime(booking.updated_at) }}
                                        </p>
                                    </li>
                                    <!-- Cancelled -->
                                    <li v-if="booking.status === 'cancelled'" class="ml-6">
                                        <span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-red-100 rounded-full ring-4 ring-white">
                                            <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <h3 class="text-sm font-semibold text-red-700">Cancelled</h3>
                                        <p v-if="booking.cancellation_reason"
                                            class="text-xs text-gray-500 mt-0.5">
                                            {{ booking.cancellation_reason }}
                                        </p>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════════════ -->
                    <!-- Sidebar                                               -->
                    <!-- ══════════════════════════════════════════════════════ -->
                    <div class="space-y-5">

                        <!-- Financial Summary -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Financials
                            </h3>

                            <!-- Payment progress bar -->
                            <div class="mb-5">
                                <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                                    <span>Payment Progress</span>
                                    <span>{{ paymentPercent }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-500"
                                        :class="paymentPercent >= 100 ? 'bg-green-500' : paymentPercent > 0 ? 'bg-indigo-500' : 'bg-gray-300'"
                                        :style="{ width: Math.min(paymentPercent, 100) + '%' }">
                                    </div>
                                </div>
                            </div>

                            <dl class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm text-gray-500">Total Amount</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        TZS {{ formatCurrency(booking.total_amount) }}
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm text-gray-500">Amount Paid</dt>
                                    <dd class="text-sm font-semibold text-green-600">
                                        TZS {{ formatCurrency(booking.paid_amount) }}
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                    <dt class="text-sm font-semibold text-gray-700">Balance Due</dt>
                                    <dd class="text-sm font-bold"
                                        :class="booking.balance > 0 ? 'text-red-600' : 'text-green-600'">
                                        {{ booking.balance > 0
                                            ? 'TZS ' + formatCurrency(booking.balance)
                                            : '✓ Fully Paid' }}
                                    </dd>
                                </div>
                            </dl>

                            <div v-if="booking.due_date" class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-400">Payment Due</span>
                                    <span :class="isPastDue(booking.due_date) ? 'text-red-600 font-semibold' : 'text-gray-600'">
                                        {{ formatDate(booking.due_date) }}
                                        <span v-if="isPastDue(booking.due_date)"
                                            class="ml-1 px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs">
                                            Overdue
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <!-- Invoice button -->
                            <button @click="downloadInvoice"
                                class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download Invoice
                            </button>
                        </div>

                        <!-- Booking Details -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Booking Details
                            </h3>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-400">Number</dt>
                                    <dd class="font-mono font-medium text-gray-900">
                                        {{ booking.booking_number }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-400">Status</dt>
                                    <dd>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full capitalize"
                                            :class="getStatusClass(booking.status)">
                                            {{ booking.status.replace('_', ' ') }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-400">Payment</dt>
                                    <dd>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full capitalize"
                                            :class="getPaymentStatusClass(booking.payment_status)">
                                            {{ booking.payment_status.replace('_', ' ') }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-400">Event Date</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ formatDate(booking.event_date) }}
                                    </dd>
                                </div>
                                <div v-if="booking.event_end_date" class="flex justify-between">
                                    <dt class="text-gray-400">End Date</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ formatDate(booking.event_end_date) }}
                                    </dd>
                                </div>
                                <div v-if="booking.guest_count" class="flex justify-between">
                                    <dt class="text-gray-400">Guests</dt>
                                    <dd class="font-medium text-gray-900">{{ booking.guest_count }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-400">Created</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ formatDate(booking.booking_date) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Client / User -->
                        <div v-if="booking.user" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Client
                            </h3>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-indigo-700 font-bold text-sm">
                                        {{ booking.user.name?.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ booking.user.name }}
                                    </p>
                                    <p class="text-xs text-gray-400 truncate">{{ booking.user.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5">
                            <h3 class="text-sm font-semibold text-red-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Danger Zone
                            </h3>
                            <p class="text-xs text-gray-500 mb-3">
                                Permanently delete this booking and all associated records. This cannot be undone.
                            </p>
                            <button @click="showDeleteModal = true"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 border border-red-200 rounded-lg text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Booking
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

  
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/40 transition-opacity" @click="showDeleteModal = false"></div>
                <!-- Modal -->
                <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-11 h-11 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Delete Booking</h3>
                            <p class="text-sm text-gray-500 mt-0.5">This action cannot be undone.</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-6">
                        Are you sure you want to permanently delete booking
                        <strong class="text-gray-900">{{ booking.booking_number }}</strong>?
                        All associated items and records will be removed.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button @click="deleteBooking"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            Delete Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    booking: Object,
})

const showDeleteModal = ref(false)

const itemsTotal = computed(() =>
    (props.booking.items ?? []).reduce(
        (sum, item) => sum + (parseFloat(item.unit_price) * (item.quantity ?? 1)),
        0
    )
)

const paymentPercent = computed(() => {
    const total = parseFloat(props.booking.total_amount || 0)
    const paid  = parseFloat(props.booking.paid_amount  || 0)
    if (!total) return 0
    return Math.round((paid / total) * 100)
})

const deleteBooking = () => {
    router.delete(route('bookings.destroy', props.booking.id), {
        onSuccess: () => { showDeleteModal.value = false },
    })
}

const downloadInvoice = () => {
    router.get(route('bookings.invoice', props.booking.id))
}

const formatCurrency = (amount) =>
    parseFloat(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    })

const formatDate = (date) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
    })
}

const formatDateTime = (dt) => {
    if (!dt) return '—'
    return new Date(dt).toLocaleString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: 'numeric', minute: '2-digit',
    })
}

const formatTime = (time) => {
    if (!time) return ''
    try {
        if (time.includes('T') || time.includes(' ')) {
            return new Date(time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
        }
        const [h, m] = time.split(':')
        const d = new Date()
        d.setHours(parseInt(h), parseInt(m || 0))
        return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
    } catch { return time }
}

const today = new Date().toISOString().split('T')[0]
const isPastDue = (date) => date && date.split('T')[0] < today

const getStatusClass = (status) => ({
    pending:     'bg-yellow-100 text-yellow-800',
    confirmed:   'bg-green-100 text-green-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed:   'bg-purple-100 text-purple-800',
    cancelled:   'bg-red-100 text-red-800',
}[status] || 'bg-gray-100 text-gray-800')

const getStatusDot = (status) => ({
    pending:     'bg-yellow-500',
    confirmed:   'bg-green-500',
    in_progress: 'bg-blue-500',
    completed:   'bg-purple-500',
    cancelled:   'bg-red-500',
}[status] || 'bg-gray-400')

const getPaymentStatusClass = (status) => ({
    unpaid:         'bg-red-100 text-red-800',
    partially_paid: 'bg-yellow-100 text-yellow-800',
    paid:           'bg-green-100 text-green-800',
    refunded:       'bg-gray-100 text-gray-800',
}[status] || 'bg-gray-100 text-gray-800')
</script>