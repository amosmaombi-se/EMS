<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('venues.index')"
                    class="inline-flex items-center p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="font-bold text-2xl text-gray-900 truncate">{{ venue.name }}</h2>
                        <span v-if="venue.is_verified"
                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            ✓ Verified
                        </span>
                        <span v-if="venue.is_featured"
                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                            ★ Featured
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full"
                            :class="venue.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            {{ venue.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ [venue.address, venue.city, venue.country].filter(Boolean).join(', ') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('venues.edit', venue.id)"
                        class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Venue
                    </Link>
                    <button @click="confirmDelete" :disabled="deleting"
                        class="inline-flex items-center px-4 py-2.5 border border-red-200 text-sm font-medium rounded-lg text-red-600 bg-white hover:bg-red-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg v-if="deleting" class="animate-spin w-4 h-4 mr-1.5 text-red-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <svg v-else class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ deleting ? 'Deleting…' : 'Delete' }}
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- ══════════════════════════════════════════════════════ -->
                    <!-- Main Content                                          -->
                    <!-- ══════════════════════════════════════════════════════ -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Image Gallery -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div v-if="venue.images && venue.images.length > 0">
                                <div class="h-72 overflow-hidden">
                                    <img :src="venue.images[activeImage]" :alt="venue.name"
                                        class="w-full h-full object-cover transition-all duration-300">
                                </div>
                                <div v-if="venue.images.length > 1" class="flex gap-2 p-3 overflow-x-auto bg-gray-50 border-t border-gray-100">
                                    <button v-for="(img, i) in venue.images" :key="i"
                                        @click="activeImage = i"
                                        class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all"
                                        :class="activeImage === i ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-transparent hover:border-gray-300'">
                                        <img :src="img" class="w-full h-full object-cover">
                                    </button>
                                </div>
                            </div>
                            <div v-else class="h-56 flex items-center justify-center bg-gray-50">
                                <div class="text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-400">No images uploaded</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div v-if="venue.description" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                About this Venue
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ venue.description }}</p>
                        </div>

                        <!-- Amenities -->
                        <div v-if="venue.amenities && venue.amenities.length > 0"
                            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Amenities
                                <span class="text-xs font-normal text-gray-400">({{ venue.amenities.length }})</span>
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div v-for="amenity in venue.amenities" :key="amenity"
                                    class="flex items-center gap-2 text-sm text-gray-700">
                                    <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span>{{ formatAmenity(amenity) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Availability Schedule -->
                        <div v-if="venue.availability && venue.availability.length > 0"
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-900">Availability</h3>
                            </div>
                            <div class="divide-y divide-gray-50">
                                <div v-for="slot in venue.availability.slice(0, 6)" :key="slot.id"
                                    class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0"
                                            :class="slot.status === 'available' ? 'bg-green-500' : slot.status === 'booked' ? 'bg-red-400' : 'bg-yellow-400'">
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ formatDate(slot.date) }}</p>
                                            <p v-if="slot.start_time && slot.end_time" class="text-xs text-gray-400">
                                                {{ slot.start_time }} – {{ slot.end_time }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span v-if="slot.price_override" class="text-xs font-semibold text-indigo-600">
                                            TZS {{ formatPrice(slot.price_override) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full capitalize"
                                            :class="getAvailabilityStatusClass(slot.status)">
                                            {{ slot.status?.replace('_', ' ') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Policies -->
                        <div v-if="venue.terms_and_conditions || venue.cancellation_policy"
                            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Policies
                            </h3>
                            <div class="space-y-5">
                                <div v-if="venue.terms_and_conditions">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Terms &amp; Conditions</h4>
                                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ venue.terms_and_conditions }}</p>
                                </div>
                                <div v-if="venue.cancellation_policy">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Cancellation Policy</h4>
                                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ venue.cancellation_policy }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Bookings -->
                        <div v-if="venue.bookings && venue.bookings.length > 0"
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="text-sm font-semibold text-gray-900">Recent Bookings</h3>
                                </div>
                                <span class="text-xs text-gray-400">{{ venue.total_bookings ?? venue.bookings.length }} total</span>
                            </div>
                            <div class="divide-y divide-gray-50">
                                <div v-for="booking in venue.bookings.slice(0, 5)" :key="booking.id"
                                    class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ booking.booking_number ?? booking.reference_number ?? '#' + booking.id }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ formatDate(booking.event_date ?? booking.start_date) }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-semibold text-gray-700">
                                            TZS {{ formatPrice(booking.total_amount) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full"
                                            :class="getBookingStatusClass(booking.status)">
                                            {{ booking.status?.replace('_', ' ') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                                <Link :href="route('bookings.index', { venue_id: venue.id })"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                    View all bookings →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════════════ -->
                    <!-- Sidebar                                               -->
                    <!-- ══════════════════════════════════════════════════════ -->
                    <div class="space-y-5">

                        <!-- Overview Stats -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Overview
                            </h3>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Type</span>
                                    <span class="text-sm font-medium text-gray-900 capitalize">{{ formatType(venue.type) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Capacity</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ venue.capacity_min ?? '—' }}–{{ venue.capacity_max ?? '—' }} guests
                                    </span>
                                </div>
                                <div v-if="venue.area_sqft" class="flex items-center justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Area</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ Number(venue.area_sqft).toLocaleString() }} sq ft
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Rating</span>
                                    <div v-if="venue.rating" class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">{{ venue.rating }}</span>
                                        <span class="text-xs text-gray-400">({{ venue.total_reviews ?? 0 }})</span>
                                    </div>
                                    <span v-else class="text-sm text-gray-400">No reviews</span>
                                </div>
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-sm text-gray-500">Total Bookings</span>
                                    <span class="text-sm font-medium text-gray-900">{{ venue.total_bookings ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pricing
                            </h3>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Per Day</span>
                                    <span class="text-sm font-bold text-indigo-600">
                                        TZS {{ formatPrice(venue.base_price_per_day) }}
                                    </span>
                                </div>
                                <div v-if="venue.base_price_per_hour" class="flex items-center justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Per Hour</span>
                                    <span class="text-sm font-bold text-indigo-600">
                                        TZS {{ formatPrice(venue.base_price_per_hour) }}
                                    </span>
                                </div>
                                <div v-if="venue.security_deposit" class="flex items-center justify-between py-2.5">
                                    <span class="text-sm text-gray-500">Security Deposit</span>
                                    <span class="text-sm font-medium text-gray-700">
                                        TZS {{ formatPrice(venue.security_deposit) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Contact
                            </h3>
                            <div class="space-y-3">
                                <div v-if="venue.contact_person" class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Contact Person</p>
                                        <p class="text-sm font-medium text-gray-900">{{ venue.contact_person }}</p>
                                    </div>
                                </div>
                                <div v-if="venue.email" class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Email</p>
                                        <a :href="`mailto:${venue.email}`"
                                            class="text-sm font-medium text-indigo-600 hover:underline">{{ venue.email }}</a>
                                    </div>
                                </div>
                                <div v-if="venue.phone" class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Phone</p>
                                        <a :href="`tel:${venue.phone}`"
                                            class="text-sm font-medium text-indigo-600 hover:underline">{{ venue.phone }}</a>
                                    </div>
                                </div>
                                <p v-if="!venue.contact_person && !venue.email && !venue.phone"
                                    class="text-sm text-gray-400 italic">No contact info provided</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Location
                            </h3>
                            <address class="not-italic text-sm text-gray-600 leading-relaxed">
                                <span v-if="venue.address">{{ venue.address }}<br></span>
                                {{ venue.city }}<span v-if="venue.state">, {{ venue.state }}</span>
                                <span v-if="venue.postal_code"> {{ venue.postal_code }}</span><br>
                                {{ venue.country }}
                            </address>
                            <div v-if="venue.latitude && venue.longitude" class="mt-3">
                                <a :href="`https://maps.google.com/?q=${venue.latitude},${venue.longitude}`"
                                    target="_blank" rel="noopener"
                                    class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    View on Google Maps
                                </a>
                            </div>
                        </div>

                        <!-- Virtual Tour -->
                        <div v-if="venue.virtual_tour_url" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.277A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                                </svg>
                                Virtual Tour
                            </h3>
                            <a :href="venue.virtual_tour_url" target="_blank" rel="noopener"
                                class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 transition-colors w-full justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.277A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                                </svg>
                                Take Virtual Tour
                            </a>
                        </div>

                        <!-- Danger Zone -->
                        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5">
                            <h3 class="text-sm font-semibold text-red-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Danger Zone
                            </h3>
                            <p class="text-xs text-gray-500 mb-3">
                                Permanently delete this venue and all its associated data. This cannot be undone.
            </p>
                            <button @click="confirmDelete" :disabled="deleting"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 border border-red-200 rounded-lg text-sm font-medium text-red-700 hover:bg-red-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Venue
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Custom modal removed — SweetAlert2 handles confirmation -->

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
    venue: Object,
})

const activeImage = ref(0)
const deleting    = ref(false)

// ── SweetAlert2 delete confirmation ──────────────────────────────────────────
const confirmDelete = async () => {
    const result = await Swal.fire({
        title: 'Delete Venue?',
        html: `
            <p class="text-sm text-gray-600">
                You are about to permanently delete
                <strong class="text-gray-900">${props.venue.name}</strong>.
            </p>
            <p class="text-sm text-red-600 mt-2 font-medium">
                All associated bookings, availability slots, and records will be removed.
                This cannot be undone.
            </p>
        `,
        icon: 'warning',
        showCancelButton:  true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText:  'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor:  '#6b7280',
        focusCancel:    true,
        reverseButtons: true,
        customClass: {
            popup:         'rounded-xl shadow-2xl',
            title:         'text-lg font-bold text-gray-900',
            confirmButton: 'rounded-lg text-sm font-medium px-5 py-2.5',
            cancelButton:  'rounded-lg text-sm font-medium px-5 py-2.5',
        },
    })

    if (!result.isConfirmed) return

    deleting.value = true

    router.delete(route('venues.destroy', props.venue.id), {
        onSuccess: () => {
            deleting.value = false
            Swal.fire({
                title: 'Deleted!',
                text: `${props.venue.name} has been deleted.`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-xl shadow-xl' },
            })
        },
        onError: (errors) => {
            deleting.value = false
            // Show the server error message if available (e.g. "has active bookings")
            const msg = Object.values(errors)[0]
                ?? 'Failed to delete venue. It may have active bookings.'
            Swal.fire({
                title: 'Cannot Delete',
                text: msg,
                icon: 'error',
                confirmButtonColor: '#4f46e5',
                customClass: {
                    popup:         'rounded-xl shadow-xl',
                    confirmButton: 'rounded-lg text-sm font-medium px-5 py-2.5',
                },
            })
        },
    })
}

const formatType = (type) => {
    if (!type) return '—'
    return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

const formatAmenity = (amenity) => {
    if (!amenity) return ''
    return amenity.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

const formatPrice = (price) => {
    if (!price) return '0'
    return Number(price).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const formatDate = (date) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const getBookingStatusClass = (status) => ({
    pending:     'bg-yellow-100 text-yellow-800',
    confirmed:   'bg-green-100 text-green-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed:   'bg-purple-100 text-purple-800',
    cancelled:   'bg-red-100 text-red-800',
}[status] ?? 'bg-gray-100 text-gray-800')

const getAvailabilityStatusClass = (status) => ({
    available:   'bg-green-100 text-green-800',
    booked:      'bg-red-100 text-red-800',
    blocked:     'bg-gray-100 text-gray-800',
    maintenance: 'bg-yellow-100 text-yellow-800',
}[status] ?? 'bg-gray-100 text-gray-800')
</script>