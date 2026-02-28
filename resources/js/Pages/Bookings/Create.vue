<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <h1 class="font-bold text-3xl text-gray-900 leading-tight mb-2">Create New Booking</h1>
                    <p class="text-gray-600">Create a new booking for your event</p>
                </div>
                <Link :href="route('bookings.index')"
                    class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- ── Validation Summary Banner ─────────────────────────────── -->
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
                                Please fix the following errors before submitting:
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

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <form @submit.prevent="submit" novalidate>
                        <div class="p-6 space-y-8">

                            <!-- ── Event Selection ──────────────────────────── -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Event Selection
                                </h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Event <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.event_id" @change="onEventChange"
                                        @blur="touch('event_id')"
                                        class="w-full rounded-lg border py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm"
                                        :class="fieldClass('event_id')">
                                        <option value="">Choose an event…</option>
                                        <option v-for="event in events" :key="event.id" :value="event.id">
                                            {{ event.title }} — {{ formatDate(event.event_date) }}
                                        </option>
                                    </select>
                                    <FieldError :msg="getError('event_id')" :show="hasError('event_id')" />
                                </div>
                            </div>

                            <!-- ── Sections shown only after an event is chosen ── -->
                            <template v-if="form.event_id">

                                <!-- ── Venue Selection ──────────────────────── -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Venue Selection <span class="text-red-500 ml-1 text-base">*</span>
                                    </h3>

                                    <div v-if="venues.length === 0"
                                        class="text-center py-8 bg-gray-50 rounded-lg">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                        </svg>
                                        <p class="text-gray-500 text-sm">No venues available for this event</p>
                                    </div>

                                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="venue in venues" :key="venue.id"
                                            @click="selectVenue(venue.id)"
                                            class="cursor-pointer border-2 rounded-xl p-5 transition-all duration-200"
                                            :class="form.venue_id === venue.id
                                                ? 'border-indigo-600 bg-indigo-50 ring-2 ring-indigo-100'
                                                : 'border-gray-200 hover:border-indigo-300 hover:shadow-sm'">
                                            <div class="flex justify-between items-start mb-3">
                                                <h4 class="font-semibold text-gray-900">{{ venue.name }}</h4>
                                                <span
                                                    class="px-2.5 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                                                    {{ venue.type.replace('_', ' ') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-1.5 flex items-center gap-1">
                                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ venue.city }}, {{ venue.state }}
                                            </p>
                                            <p class="text-sm text-gray-600 flex items-center gap-1">
                                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                                Capacity: {{ venue.capacity_max }} guests
                                            </p>
                                            <div
                                                class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                                                <div>
                                                    <p class="text-xl font-bold text-indigo-600">
                                                        TZS {{ venue.base_price_per_day.toLocaleString() }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">per day</p>
                                                </div>
                                                <div v-if="form.venue_id === venue.id"
                                                    class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Venue validation message -->
                                    <div v-if="hasError('venue_id')"
                                        class="mt-3 flex items-center gap-1.5 text-sm text-red-600">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ getError('venue_id') }}
                                    </div>
                                </div>

                                <!-- ── Vendor Services ───────────────────────── -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        Vendor Services
                                        <span class="ml-2 text-sm font-normal text-gray-400">(optional)</span>
                                    </h3>

                                    <div v-if="vendors.length === 0"
                                        class="text-center py-8 bg-gray-50 rounded-lg">
                                        <p class="text-gray-500 text-sm">No vendor services available</p>
                                    </div>

                                    <div v-else class="space-y-4">
                                        <div v-for="vendor in vendors" :key="vendor.id"
                                            class="border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors">
                                            <div class="flex items-center mb-4 gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-indigo-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900">
                                                        {{ vendor.business_name }}
                                                    </h4>
                                                    <p v-if="vendor.description"
                                                        class="text-xs text-gray-500 mt-0.5">
                                                        {{ vendor.description }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <label v-for="service in vendor.services" :key="service.id"
                                                    class="flex items-start p-4 rounded-lg border transition-all cursor-pointer"
                                                    :class="isServiceSelected(service.id)
                                                        ? 'border-indigo-300 bg-indigo-50'
                                                        : 'border-gray-200 hover:bg-gray-50'">
                                                    <input type="checkbox"
                                                        :checked="isServiceSelected(service.id)"
                                                        @change="toggleService(vendor, service)"
                                                        class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                                    <div class="ml-3 flex-1">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <span
                                                                    class="font-medium text-gray-900 text-sm">{{ service.name }}</span>
                                                                <span v-if="service.pricing_type"
                                                                    class="ml-2 px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                                                    {{ service.pricing_type.replace('_', ' ') }}
                                                                </span>
                                                            </div>
                                                            <div class="text-right ml-4 flex-shrink-0">
                                                                <span
                                                                    class="font-semibold text-indigo-600 text-sm">
                                                                    TZS {{ service.base_price.toLocaleString() }}
                                                                </span>
                                                                <span v-if="service.unit"
                                                                    class="text-xs text-gray-500 block">
                                                                    per {{ service.unit }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <p v-if="service.description"
                                                            class="text-xs text-gray-500 mt-1.5">
                                                            {{ service.description }}
                                                        </p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ── Event Dates ────────────────────────────── -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Event Dates
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                            <FieldError :msg="getError('event_date')"
                                                :show="hasError('event_date')" />
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
                                            <FieldError :msg="getError('event_end_date')"
                                                :show="hasError('event_end_date')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- ── Additional Notes ───────────────────────── -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Additional Information
                                    </h3>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Special Requirements &amp; Notes
                                    </label>
                                    <textarea v-model="form.customer_notes" rows="4"
                                        placeholder="Any special requirements, dietary needs, accessibility requirements, or additional notes…"
                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors text-sm resize-none"></textarea>
                                </div>

                                <!-- ── Booking Summary ─────────────────────────── -->
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>

                                    <!-- Empty summary prompt -->
                                    <div v-if="!form.venue_id && form.items.length === 0"
                                        class="text-center py-8">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-sm text-gray-400">
                                            Select a venue and services to see pricing
                                        </p>
                                    </div>

                                    <div v-else class="space-y-2">
                                        <!-- Venue row -->
                                        <div v-if="form.venue_id"
                                            class="flex justify-between items-center py-2.5 border-b border-gray-200">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ getVenueName(form.venue_id) }}
                                                </p>
                                                <p class="text-xs text-gray-500">Venue</p>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900">
                                                TZS {{ formatCurrency(getVenuePrice(form.venue_id)) }}
                                            </span>
                                        </div>

                                        <!-- Service rows -->
                                        <div v-for="item in form.items" :key="item.itemable_id"
                                            class="flex justify-between items-center py-2.5 border-b border-gray-200">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ getServiceName(item.itemable_id) }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ getServiceCategory(item.itemable_id) }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    TZS {{ formatCurrency(getServicePrice(item.itemable_id) * item.quantity) }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    TZS {{ formatCurrency(getServicePrice(item.itemable_id)) }}
                                                    × {{ item.quantity }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Totals -->
                                        <div class="pt-3 space-y-2">
                                            <div class="flex justify-between text-sm text-gray-600">
                                                <span>Subtotal</span>
                                                <span>TZS {{ formatCurrency(calculateSubtotal()) }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm text-gray-500">
                                                <span>Estimated Tax ({{ taxRate }}%)</span>
                                                <span>TZS {{ formatCurrency(calculateTax()) }}</span>
                                            </div>
                                            <div
                                                class="flex justify-between items-baseline pt-3 border-t border-gray-300">
                                                <div>
                                                    <span class="font-bold text-base text-gray-900">
                                                        Estimated Total
                                                    </span>
                                                    <p class="text-xs text-gray-400 mt-0.5">
                                                        All prices are estimates
                                                    </p>
                                                </div>
                                                <span class="text-2xl font-bold text-indigo-600">
                                                    TZS {{ formatCurrency(calculateTotal()) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Prompt when no event chosen yet -->
                            <div v-if="!form.event_id"
                                class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-gray-400">Select an event above to continue</p>
                            </div>
                        </div>

                        <!-- ── Form Actions ────────────────────────────────────── -->
                        <div
                            class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex items-center justify-between">
                            <Link :href="route('bookings.index')"
                                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ form.processing ? 'Creating Booking…' : 'Create Booking' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Help Card -->
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Need Help?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Booking Process</h4>
                                <p class="text-xs text-gray-500 mt-1">Select an event, choose a venue and services,
                                    then review your booking summary.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Payment Security</h4>
                                <p class="text-xs text-gray-500 mt-1">All transactions are secure and encrypted. We
                                    never store your payment details.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Support Available</h4>
                                <p class="text-xs text-gray-500 mt-1">Our support team is available 24/7 to help
                                    with your booking.</p>
                            </div>
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
import { ref, reactive, computed, defineComponent, h } from 'vue'

const props = defineProps({
    events:  Array,
    venues:  Array,
    vendors: Array,
})

// ── Inline field-error component ──────────────────────────────────────────────
const FieldError = defineComponent({
    props: { msg: String, show: Boolean },
    setup(p) {
        return () => p.show && p.msg
            ? h('p', { class: 'mt-1 text-xs text-red-600 flex items-center gap-1' }, [
                h('svg', {
                    class: 'w-3.5 h-3.5 flex-shrink-0',
                    fill: 'currentColor',
                    viewBox: '0 0 20 20',
                }, [
                    h('path', {
                        'fill-rule': 'evenodd',
                        'clip-rule': 'evenodd',
                        d: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z',
                    }),
                ]),
                p.msg,
            ])
            : null
    },
})

// ── Form ──────────────────────────────────────────────────────────────────────
const form = useForm({
    event_id:       '',
    venue_id:       null,
    event_date:     '',
    event_end_date: '',
    items:          [],
    customer_notes: '',
})

const taxRate = 8.5

// ── Validation ────────────────────────────────────────────────────────────────
const touched     = reactive({})
const showSummary = ref(false)

const today = new Date().toISOString().split('T')[0]

// Rules return an error string or null
const rules = {
    event_id(v) {
        return !v ? 'Please select an event.' : null
    },
    venue_id(v) {
        // Only enforce venue once an event is chosen
        if (!form.event_id) return null
        return !v ? 'Please select a venue.' : null
    },
    event_date(v) {
        if (!form.event_id) return null
        if (!v) return 'Event date is required.'
        if (v < today) return 'Event date cannot be in the past.'
        return null
    },
    event_end_date(v) {
        if (!v || !form.event_date) return null
        if (v < form.event_date) return 'End date cannot be before the event date.'
        return null
    },
}

const clientErrors = computed(() => {
    const errors = {}
    for (const [field, rule] of Object.entries(rules)) {
        const msg = rule(form[field])
        if (msg) errors[field] = msg
    }
    return errors
})

const touch    = (field)   => { touched[field] = true }
const touchAll = ()        => Object.keys(rules).forEach(f => { touched[f] = true })
const hasError = (field)   => (touched[field] || showSummary.value) && !!clientErrors.value[field]
const getError = (field)   => form.errors[field] || clientErrors.value[field]

const fieldClass = (field) => hasError(field)
    ? 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500'
    : 'border-gray-300 focus:border-indigo-500'

const selectVenue = (id) => {
    form.venue_id = id
    touch('venue_id')
}

const onEventChange = () => {
    touch('event_id')
    const event = props.events.find(e => e.id === form.event_id)
    if (event?.event_date) {
        form.event_date = event.event_date.split('T')[0]
    }
    form.venue_id = null
    form.items    = []
}

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
            unit_price:    service.base_price,
            category:      service.category,
        })
    }
}

const getVenueName  = (id) => props.venues.find(v => v.id === id)?.name || ''
const getVenuePrice = (id) => props.venues.find(v => v.id === id)?.base_price_per_day || 0

const findService = (id) => {
    for (const vendor of props.vendors) {
        const s = vendor.services.find(s => s.id === id)
        if (s) return s
    }
    return null
}

const getServiceName     = (id) => findService(id)?.name      || ''
const getServiceCategory = (id) => findService(id)?.category  || 'Service'
const getServicePrice    = (id) => findService(id)?.base_price || 0

const calculateSubtotal = () => {
    let total = form.venue_id ? getVenuePrice(form.venue_id) : 0
    form.items.forEach(item => { total += getServicePrice(item.itemable_id) * item.quantity })
    return total
}
const calculateTax   = () => (calculateSubtotal() * taxRate) / 100
const calculateTotal = () => calculateSubtotal() + calculateTax()

const formatCurrency = (amount) =>
    parseFloat(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    })

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
    })
}

const submit = () => {
    touchAll()
    showSummary.value = true

    if (Object.keys(clientErrors.value).length > 0) {
        // Scroll to first red field
        setTimeout(() => {
            const el = document.querySelector(
                '.border-red-300, [class*="border-red"]'
            )
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }, 50)
        return
    }

    form.post(route('bookings.store'))
}
</script>