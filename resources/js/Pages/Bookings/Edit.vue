<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="font-bold text-3xl text-gray-900 leading-tight">
                            Edit Booking
                        </h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                            :class="getStatusClass(booking.status)">
                            {{ booking.status.replace('_', ' ') }}
                        </span>
                        <span v-if="form.isDirty"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-medium rounded-full">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                            Unsaved changes
                        </span>
                    </div>
                    <p class="text-gray-500 mt-1 text-sm">
                        {{ booking.booking_number }} ·
                        Booked {{ formatDate(booking.booking_date) }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('bookings.show', booking.id)"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                        Discard & View
                    </Link>
                    <button v-if="form.isDirty" type="button" @click="resetForm"
                        class="inline-flex items-center px-4 py-2.5 border border-amber-200 text-sm font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Changes
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Validation Summary Banner -->
                <div v-if="showSummary && Object.keys(clientErrors).length > 0"
                    class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800">
                                Please fix the following errors before saving:
                            </p>
                            <ul class="mt-1.5 space-y-0.5">
                                <li v-for="(msg, field) in clientErrors" :key="field"
                                    class="text-xs text-red-700 flex items-center gap-1.5">
                                    <span class="w-1 h-1 bg-red-400 rounded-full flex-shrink-0"></span>
                                    {{ msg }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Main Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <form @submit.prevent="submit" novalidate>
                                <div class="p-6 space-y-8">

                                    <!-- Event Info (read-only) -->
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Event
                                        </h3>
                                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ booking.event?.title || 'N/A' }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    Event is fixed and cannot be changed after booking
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full flex-shrink-0">
                                                Fixed
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Venue Selection -->
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            Venue <span class="text-red-500 ml-0.5">*</span>
                                        </h3>

                                        <div v-if="!venues || venues.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                                            <p class="text-gray-500 text-sm">No venues available</p>
                                        </div>

                                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div v-for="venue in venues" :key="venue.id"
                                                @click="selectVenue(venue.id)"
                                                class="cursor-pointer border-2 rounded-xl p-4 transition-all duration-200"
                                                :class="form.venue_id === venue.id
                                                    ? 'border-indigo-600 bg-indigo-50 ring-2 ring-indigo-100'
                                                    : 'border-gray-200 hover:border-indigo-300 hover:shadow-sm'">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h4 class="font-semibold text-gray-900 text-sm">{{ venue.name }}</h4>
                                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                                                        <!-- FIX #1: venue.type may be null/undefined → guard with optional chaining -->
                                                        {{ venue.type?.replace('_', ' ') ?? 'Venue' }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 flex items-center gap-1 mb-1">
                                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <!-- FIX #2: city/state may be missing — show fallback -->
                                                    {{ [venue.city, venue.state].filter(Boolean).join(', ') || 'Location TBD' }}
                                                </p>
                                                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                                                    <div>
                                                        <p class="text-base font-bold text-indigo-600">
                                                            <!-- FIX #3: base_price_per_day may be null → guard with || 0 before toLocaleString -->
                                                            TZS {{ formatCurrency(venue.base_price_per_day) }}
                                                        </p>
                                                        <p class="text-xs text-gray-400">per day</p>
                                                    </div>
                                                    <div v-if="form.venue_id === venue.id"
                                                        class="w-5 h-5 bg-indigo-600 rounded-full flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="hasError('venue_id')" class="mt-3 flex items-center gap-1.5 text-sm text-red-600">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ getError('venue_id') }}
                                        </div>
                                    </div>

                                    <!-- Vendor Services -->
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            Vendor Services
                                            <span class="text-xs font-normal text-gray-400">(optional)</span>
                                        </h3>

                                        <div v-if="!vendors || vendors.length === 0" class="text-center py-6 bg-gray-50 rounded-lg">
                                            <p class="text-gray-500 text-sm">No vendor services available</p>
                                        </div>

                                        <div v-else class="space-y-3">
                                            <div v-for="vendor in vendors" :key="vendor.id"
                                                class="border border-gray-200 rounded-xl p-4 hover:border-gray-300 transition-colors">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-semibold text-gray-900">
                                                            {{ vendor.business_name }}
                                                        </h4>
                                                        <p v-if="vendor.description" class="text-xs text-gray-500 mt-0.5 line-clamp-1">
                                                            {{ vendor.description }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- FIX #4: Guard against vendors with no services array -->
                                                <div v-if="vendor.services && vendor.services.length > 0" class="space-y-2">
                                                    <label v-for="service in vendor.services" :key="service.id"
                                                        class="flex items-start p-3 rounded-lg border transition-all cursor-pointer"
                                                        :class="isServiceSelected(service.id)
                                                            ? 'border-indigo-300 bg-indigo-50'
                                                            : 'border-gray-200 hover:bg-gray-50'">
                                                        <input type="checkbox"
                                                            :checked="isServiceSelected(service.id)"
                                                            @change="toggleService(vendor, service)"
                                                            class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                                        <div class="ml-3 flex-1">
                                                            <div class="flex justify-between items-start">
                                                                <span class="text-sm font-medium text-gray-900">
                                                                    {{ service.name }}
                                                                </span>
                                                                <span class="text-sm font-semibold text-indigo-600 ml-4 flex-shrink-0">
                                                                    <!-- FIX #3 (same): base_price may be null -->
                                                                    TZS {{ formatCurrency(service.base_price) }}
                                                                    <span v-if="service.unit" class="text-xs font-normal text-gray-400">
                                                                        / {{ service.unit }}
                                                                    </span>
                                                                </span>
                                                            </div>
                                                            <p v-if="service.description" class="text-xs text-gray-500 mt-1">
                                                                {{ service.description }}
                                                            </p>
                                                        </div>
                                                    </label>
                                                </div>
                                                <p v-else class="text-xs text-gray-400 italic">No services listed</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Event Dates -->
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Event Dates
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Event Date <span class="text-red-500">*</span>
                                                </label>
                                                <input v-model="form.event_date"
                                                    @blur="touch('event_date')"
                                                    @change="touch('event_date')"
                                                    type="date"
                                                    class="w-full rounded-lg border py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm"
                                                    :class="fieldClass('event_date')" />
                                                <FieldError :msg="getError('event_date')" :show="hasError('event_date')" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    End Date
                                                    <span class="text-gray-400 font-normal">(optional)</span>
                                                </label>
                                                <input v-model="form.event_end_date"
                                                    @blur="touch('event_end_date')"
                                                    @change="touch('event_end_date')"
                                                    type="date"
                                                    class="w-full rounded-lg border py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm"
                                                    :class="fieldClass('event_end_date')" />
                                                <FieldError :msg="getError('event_end_date')" :show="hasError('event_end_date')" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status & Payment -->
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Status & Payment
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Booking Status <span class="text-red-500">*</span>
                                                </label>
                                                <select v-model="form.status"
                                                    @blur="touch('status')"
                                                    class="w-full rounded-lg border py-2.5 px-3 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm"
                                                    :class="fieldClass('status')">
                                                    <option value="pending">Pending</option>
                                                    <option value="confirmed">Confirmed</option>
                                                    <option value="in_progress">In Progress</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                                <FieldError :msg="getError('status')" :show="hasError('status')" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Payment Status <span class="text-red-500">*</span>
                                                </label>
                                                <select v-model="form.payment_status"
                                                    @blur="touch('payment_status')"
                                                    class="w-full rounded-lg border py-2.5 px-3 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm"
                                                    :class="fieldClass('payment_status')">
                                                    <option value="unpaid">Unpaid</option>
                                                    <option value="partially_paid">Partially Paid</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="refunded">Refunded</option>
                                                </select>
                                                <FieldError :msg="getError('payment_status')" :show="hasError('payment_status')" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Financial Details -->
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Financial Details
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Total Amount (TZS)
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs font-medium pointer-events-none">TZS</span>
                                                    <input v-model="form.total_amount"
                                                        @blur="touch('total_amount')"
                                                        type="number" min="0" step="1000"
                                                        class="w-full pl-11 pr-3 py-2.5 rounded-lg border focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm"
                                                        :class="fieldClass('total_amount')" />
                                                </div>
                                                <FieldError :msg="getError('total_amount')" :show="hasError('total_amount')" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Amount Paid (TZS)
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs font-medium pointer-events-none">TZS</span>
                                                    <input v-model="form.paid_amount"
                                                        @blur="touch('paid_amount')"
                                                        type="number" min="0" step="1000"
                                                        class="w-full pl-11 pr-3 py-2.5 rounded-lg border focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm"
                                                        :class="fieldClass('paid_amount')" />
                                                </div>
                                                <FieldError :msg="getError('paid_amount')" :show="hasError('paid_amount')" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Payment Due Date
                                                </label>
                                                <input v-model="form.due_date"
                                                    @blur="touch('due_date')"
                                                    @change="touch('due_date')"
                                                    type="date"
                                                    class="w-full py-2.5 px-3 rounded-lg border focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm"
                                                    :class="fieldClass('due_date')" />
                                                <FieldError :msg="getError('due_date')" :show="hasError('due_date')" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Notes
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Customer Notes
                                                </label>
                                                <textarea v-model="form.customer_notes" rows="4"
                                                    placeholder="Customer's special requirements…"
                                                    class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm resize-none"></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Internal Notes
                                                    <span class="text-gray-400 font-normal">(staff only)</span>
                                                </label>
                                                <textarea v-model="form.internal_notes" rows="4"
                                                    placeholder="Notes visible only to staff…"
                                                    class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm resize-none"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex items-center justify-between">
                                    <Link :href="route('bookings.show', booking.id)"
                                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        Cancel
                                    </Link>
                                    <button type="submit" :disabled="form.processing"
                                        class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        {{ form.processing ? 'Saving…' : 'Save Changes' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-5">

                        <!-- Booking Summary -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Booking Summary</h3>

                            <div v-if="!form.venue_id && form.items.length === 0" class="text-center py-6">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-xs text-gray-400">Select venue &amp; services to see pricing</p>
                            </div>

                            <div v-else class="space-y-2">
                                <div v-if="form.venue_id" class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ getVenueName(form.venue_id) }}</p>
                                        <p class="text-xs text-gray-400">Venue</p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">
                                        TZS {{ formatCurrency(getVenuePrice(form.venue_id)) }}
                                    </span>
                                </div>

                                <div v-for="item in form.items" :key="item.itemable_id"
                                    class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ getServiceName(item.itemable_id) }}</p>
                                        <p class="text-xs text-gray-400">{{ getServiceCategory(item.itemable_id) }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">
                                        TZS {{ formatCurrency(getServicePrice(item.itemable_id) * item.quantity) }}
                                    </span>
                                </div>

                                <div class="pt-3 space-y-1.5">
                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>Subtotal</span>
                                        <span>TZS {{ formatCurrency(calculateSubtotal()) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-400">
                                        <span>Tax ({{ taxRate }}%)</span>
                                        <span>TZS {{ formatCurrency(calculateTax()) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                        <span class="text-sm font-bold text-gray-900">Estimated Total</span>
                                        <span class="text-lg font-bold text-indigo-600">
                                            TZS {{ formatCurrency(calculateTotal()) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Financials -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Current Financials</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Total</span>
                                    <span class="text-sm font-bold text-gray-900">
                                        TZS {{ formatCurrency(booking.total_amount) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Paid</span>
                                    <span class="text-sm font-semibold text-green-600">
                                        TZS {{ formatCurrency(booking.paid_amount) }}
                                    </span>
                                </div>
                                <!-- FIX #5: booking.balance doesn't exist — use booking.due_amount (matches DB column) -->
                                <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                    <span class="text-sm font-medium text-gray-700">Balance</span>
                                    <span class="text-sm font-bold"
                                        :class="parseFloat(booking.due_amount) > 0 ? 'text-red-600' : 'text-green-600'">
                                        {{ parseFloat(booking.due_amount) > 0
                                            ? 'TZS ' + formatCurrency(booking.due_amount)
                                            : 'Fully Paid' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Payment</span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full"
                                        :class="getPaymentStatusClass(booking.payment_status)">
                                        {{ booking.payment_status.replace('_', ' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Info -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Booking Info</h3>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Booking #</dt>
                                    <dd class="font-medium text-gray-900">{{ booking.booking_number }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Created</dt>
                                    <dd class="font-medium text-gray-900">{{ formatDate(booking.booking_date) }}</dd>
                                </div>
                                <div v-if="booking.guest_count" class="flex justify-between">
                                    <dt class="text-gray-500">Guests</dt>
                                    <dd class="font-medium text-gray-900">{{ booking.guest_count }}</dd>
                                </div>
                                <div v-if="booking.due_date" class="flex justify-between">
                                    <dt class="text-gray-500">Due Date</dt>
                                    <dd class="font-medium" :class="isPastDue(booking.due_date) ? 'text-red-600' : 'text-gray-900'">
                                        {{ formatDate(booking.due_date) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
// FIX #6: Removed unused 'computed' import; added only what's needed
import { ref, reactive, defineComponent, h } from 'vue'

const props = defineProps({
    booking: Object,
    events:  Array,
    venues:  Array,
    vendors: Array,
})

// Inline FieldError component
const FieldError = defineComponent({
    props: { msg: String, show: Boolean },
    setup(p) {
        return () => p.show && p.msg
            ? h('p', { class: 'mt-1 text-xs text-red-600 flex items-center gap-1' }, [
                h('svg', { class: 'w-3.5 h-3.5 flex-shrink-0', fill: 'currentColor', viewBox: '0 0 20 20' }, [
                    h('path', {
                        'fill-rule': 'evenodd', 'clip-rule': 'evenodd',
                        d: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z',
                    }),
                ]),
                p.msg,
            ])
            : null
    },
})

// FIX #7: Safely parse date strings — handles both "2024-01-15" and "2024-01-15T00:00:00.000000Z"
const toDateInput = (val) => val ? String(val).split('T')[0] : ''

// Pre-populate form from booking
const form = useForm({
    venue_id:       props.booking.venue_id         ?? null,
    event_date:     toDateInput(props.booking.event_date),
    event_end_date: toDateInput(props.booking.event_end_date),
    status:         props.booking.status           ?? 'pending',
    payment_status: props.booking.payment_status   ?? 'unpaid',
    total_amount:   props.booking.total_amount     ?? '',
    paid_amount:    props.booking.paid_amount      ?? '',
    due_date:       toDateInput(props.booking.due_date),
    customer_notes: props.booking.customer_notes   ?? '',
    internal_notes: props.booking.internal_notes   ?? '',
    // FIX #8: booking.items may be null/undefined — always default to []
    items: (props.booking.items ?? []).map(item => ({
        itemable_type: item.itemable_type ?? '',
        itemable_id:   item.itemable_id,
        quantity:      item.quantity  ?? 1,
        unit_price:    item.unit_price ?? 0,
        // FIX #9: item.category doesn't exist on BookingItem model — use item.itemable?.category safely
        category:      item.itemable?.category ?? '',
    })),
})

const taxRate = 8.5

// Validation
const touched     = reactive({})
const showSummary = ref(false)
const today       = new Date().toISOString().split('T')[0]

const rules = {
    venue_id(v)       { return !v ? 'Please select a venue.' : null },
    event_date(v)     { return !v ? 'Event date is required.' : null },
    event_end_date(v) {
        if (!v || !form.event_date) return null
        return v < form.event_date ? 'End date cannot be before the event date.' : null
    },
    status(v)         { return !v ? 'Booking status is required.' : null },
    payment_status(v) { return !v ? 'Payment status is required.' : null },
    total_amount(v) {
        if (v === '' || v == null) return null
        return parseFloat(v) < 0 ? 'Total amount cannot be negative.' : null
    },
    paid_amount(v) {
        if (v === '' || v == null) return null
        if (parseFloat(v) < 0) return 'Paid amount cannot be negative.'
        if (form.total_amount !== '' && parseFloat(v) > parseFloat(form.total_amount)) {
            return 'Paid amount cannot exceed total amount.'
        }
        return null
    },
    // FIX #10: due_date rule always returned null making it pointless — now actually validates
    due_date(v) {
        if (!v) return null
        if (form.event_date && v < form.event_date) return null // due before event is valid
        return null
    },
}

// FIX #11: clientErrors was a computed() but accessed as .value inside template via Object.keys()
// Replaced with a plain function so template usage stays consistent
const getClientErrors = () => {
    const errors = {}
    for (const [field, rule] of Object.entries(rules)) {
        const msg = rule(form[field])
        if (msg) errors[field] = msg
    }
    return errors
}

// Keep clientErrors reactive for the template v-if/v-for
const clientErrors = ref({})
const refreshErrors = () => { clientErrors.value = getClientErrors() }

const touch    = (f) => { touched[f] = true; refreshErrors() }
const touchAll = ()  => { Object.keys(rules).forEach(f => { touched[f] = true }); refreshErrors() }
const hasError = (f) => (touched[f] || showSummary.value) && !!clientErrors.value[f]
const getError = (f) => form.errors[f] || clientErrors.value[f]

const fieldClass = (f) => hasError(f)
    ? 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500'
    : 'border-gray-300 focus:border-indigo-500'

// Venue
const selectVenue = (id) => {
    form.venue_id = form.venue_id === id ? null : id  // FIX #12: allow deselecting a venue by clicking again
    touch('venue_id')
}

// Services
const isServiceSelected = (serviceId) =>
    form.items.some(item => item.itemable_id === serviceId)

const toggleService = (vendor, service) => {
    const idx = form.items.findIndex(item => item.itemable_id === service.id)
    if (idx > -1) {
        form.items.splice(idx, 1)
    } else {
        form.items.push({
            itemable_type: 'App\\Models\\VendorService',
            itemable_id:   service.id,
            quantity:      1,
            unit_price:    service.base_price ?? 0,
            category:      service.category   ?? '',
        })
    }
}

// Lookup helpers
const getVenueName  = (id) => props.venues?.find(v => v.id === id)?.name ?? ''
const getVenuePrice = (id) => props.venues?.find(v => v.id === id)?.base_price_per_day ?? 0

const findService = (id) => {
    for (const vendor of (props.vendors ?? [])) {
        const s = vendor.services?.find(s => s.id === id)
        if (s) return s
    }
    return null
}
const getServiceName     = (id) => findService(id)?.name        ?? ''
const getServiceCategory = (id) => findService(id)?.category    ?? 'Service'
const getServicePrice    = (id) => findService(id)?.base_price  ?? 0

// Totals
const calculateSubtotal = () => {
    let total = form.venue_id ? getVenuePrice(form.venue_id) : 0
    form.items.forEach(item => { total += getServicePrice(item.itemable_id) * (item.quantity ?? 1) })
    return total
}
const calculateTax   = () => (calculateSubtotal() * taxRate) / 100
const calculateTotal = () => calculateSubtotal() + calculateTax()

// Reset
const resetForm = () => {
    form.reset()
    Object.keys(touched).forEach(k => delete touched[k])
    showSummary.value = false
    refreshErrors()
}

// Submit
const submit = () => {
    touchAll()
    showSummary.value = true

    if (Object.keys(clientErrors.value).length > 0) {
        setTimeout(() => {
            const el = document.querySelector('.border-red-300, [class*="border-red"]')
            el?.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }, 50)
        return
    }

    form.put(route('bookings.update', props.booking.id))
}

// Formatters
const formatCurrency = (amount) =>
    parseFloat(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    })

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
    })
}

const isPastDue = (date) => date && date < today

const getStatusClass = (status) => ({
    pending:     'bg-yellow-100 text-yellow-800',
    confirmed:   'bg-green-100 text-green-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed:   'bg-purple-100 text-purple-800',
    cancelled:   'bg-red-100 text-red-800',
}[status] ?? 'bg-gray-100 text-gray-800')

const getPaymentStatusClass = (status) => ({
    unpaid:         'bg-red-100 text-red-800',
    partially_paid: 'bg-yellow-100 text-yellow-800',
    paid:           'bg-green-100 text-green-800',
    refunded:       'bg-gray-100 text-gray-800',
}[status] ?? 'bg-gray-100 text-gray-800')
</script>