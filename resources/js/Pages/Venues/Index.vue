<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">Venues</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage and organize all your venues
                    </p>
                </div>
                <Link :href="route('venues.create')"
                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Venue
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Stats Overview -->
                <div v-if="venues.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Venues -->
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-md p-5 border border-blue-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Total Venues</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ venues.total }}</p>
                                <p class="text-xs text-blue-600 font-medium mt-1">All venues combined</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Venues -->
                    <div class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-md p-5 border border-green-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-green-700 uppercase tracking-wide">Active</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ venues.data.filter(v => v.is_active).length }}</p>
                                <p class="text-xs text-green-600 font-medium mt-1">Currently available</p>
                            </div>
                        </div>
                    </div>

                    <!-- Verified Venues -->
                    <div class="bg-gradient-to-br from-amber-50 to-white rounded-xl shadow-md p-5 border border-amber-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-amber-700 uppercase tracking-wide">Verified</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ venues.data.filter(v => v.is_verified).length }}</p>
                                <p class="text-xs text-amber-600 font-medium mt-1">Quality assured</p>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Venues -->
                    <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-md p-5 border border-purple-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide">Featured</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ venues.data.filter(v => v.is_featured).length }}</p>
                                <p class="text-xs text-purple-600 font-medium mt-1">Highlighted venues</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Filter Venues</h3>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                        <!-- Search -->
                        <div class="relative lg:col-span-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input v-model="form.search" @input="search" type="text" placeholder="Search by name, description, address or city..."
                                class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm placeholder-gray-400">
                        </div>

                        <!-- Type -->
                        <select v-model="form.type" @change="search"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                            <option value="">All Types</option>
                            <option value="indoor">Indoor</option>
                            <option value="outdoor">Outdoor</option>
                            <option value="banquet_hall">Banquet Hall</option>
                            <option value="garden">Garden</option>
                            <option value="rooftop">Rooftop</option>
                            <option value="beach">Beach</option>
                            <option value="hotel">Hotel</option>
                            <option value="restaurant">Restaurant</option>
                            <option value="other">Other</option>
                        </select>

                        <!-- City -->
                        <input v-model="form.city" @input="search" type="text" placeholder="Filter by city..."
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm placeholder-gray-400">
                    </div>

                    <!-- Second Row: Capacity + Price + Toggles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 mt-3">
                        <!-- Min Capacity -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-xs text-gray-400 pointer-events-none">Min</span>
                            <input v-model="form.min_capacity" @input="search" type="number" min="0" placeholder="Min guests"
                                class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                        </div>

                        <!-- Max Capacity -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-xs text-gray-400 pointer-events-none">Max</span>
                            <input v-model="form.max_capacity" @input="search" type="number" min="0" placeholder="Max guests"
                                class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                        </div>

                        <!-- Min Price -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-xs text-gray-400 pointer-events-none">TZS</span>
                            <input v-model="form.min_price" @input="search" type="number" min="0" placeholder="Min price/day"
                                class="w-full pl-11 pr-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                        </div>

                        <!-- Max Price -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-xs text-gray-400 pointer-events-none">TZS</span>
                            <input v-model="form.max_price" @input="search" type="number" min="0" placeholder="Max price/day"
                                class="w-full pl-11 pr-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                        </div>

                        <!-- Verified + Featured -->
                        <div class="flex gap-2">
                            <select v-model="form.is_verified" @change="search"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                                <option value="">All</option>
                                <option value="1">Verified</option>
                                <option value="0">Unverified</option>
                            </select>
                            <select v-model="form.is_featured" @change="search"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                                <option value="">All</option>
                                <option value="1">Featured</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Results Count -->
                <div v-if="venues.data.length > 0" class="mb-4">
                    <p class="text-sm text-gray-600">
                        Showing <span class="font-medium text-gray-900">{{ venues.from }}</span> to
                        <span class="font-medium text-gray-900">{{ venues.to }}</span> of
                        <span class="font-medium text-gray-900">{{ venues.total }}</span> venues
                        <span class="text-gray-400">(sorted by featured &amp; rating)</span>
                    </p>
                </div>

                <!-- Venues Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="venue in venues.data" :key="venue.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all overflow-hidden group">

                        <!-- Image -->
                        <div class="relative h-44 bg-gray-100 overflow-hidden">
                            <img v-if="venue.images && venue.images.length > 0"
                                :src="venue.images[0]"
                                :alt="venue.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>

                            <!-- Badges overlay -->
                            <div class="absolute top-2 left-2 flex gap-1.5">
                                <span v-if="venue.is_featured"
                                    class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-amber-400 text-amber-900">
                                    ★ Featured
                                </span>
                                <span v-if="venue.is_verified"
                                    class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-green-500 text-white">
                                    ✓ Verified
                                </span>
                            </div>

                            <!-- Active/Inactive badge -->
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full"
                                    :class="venue.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    {{ venue.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4">
                            <!-- Type Badge -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                                    {{ formatType(venue.type) }}
                                </span>
                                <!-- Rating -->
                                <div v-if="venue.rating" class="flex items-center text-sm text-amber-500">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    <span class="font-medium text-gray-700">{{ venue.rating }}</span>
                                    <span class="text-gray-400 ml-1">({{ venue.total_reviews }})</span>
                                </div>
                            </div>

                            <!-- Venue Name -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                                {{ venue.name }}
                            </h3>

                            <!-- Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="truncate">{{ venue.city }}, {{ venue.country }}</span>
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ venue.capacity_min }}–{{ venue.capacity_max }} guests</span>
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium text-gray-800">TZS {{ formatPrice(venue.base_price_per_day) }}<span class="font-normal text-gray-500">/day</span></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-2">
                                    <Link :href="route('venues.edit', venue.id)"
                                        class="inline-flex items-center p-1.5 text-gray-400 hover:text-indigo-600 transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <Link :href="route('venues.show', venue.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded hover:bg-indigo-100 transition-colors">
                                        View Details
                                    </Link>
                                </div>
                                <span class="text-xs text-gray-400">{{ venue.total_bookings }} bookings</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="venues.data.length === 0"
                    class="bg-white rounded-lg shadow-sm p-8 text-center border border-gray-200">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No venues found</h3>
                        <p class="text-sm text-gray-600 mb-6">
                            {{ hasActiveFilters ? "Try adjusting your filters to find what you're looking for." : 'Get started by adding your first venue.' }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <Link v-if="!hasActiveFilters" :href="route('venues.create')"
                                class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add First Venue
                            </Link>
                            <button v-else @click="resetFilters"
                                class="inline-flex items-center px-4 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="venues.data.length > 0" class="mt-6">
                    <Pagination :links="venues.links" />
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
    venues: Object,
    filters: Object
})

const form = reactive({
    search: props.filters.search || '',
    type: props.filters.type || '',
    city: props.filters.city || '',
    min_capacity: props.filters.min_capacity || '',
    max_capacity: props.filters.max_capacity || '',
    min_price: props.filters.min_price || '',
    max_price: props.filters.max_price || '',
    is_verified: props.filters.is_verified ?? '',
    is_featured: props.filters.is_featured ?? '',
})

const hasActiveFilters = computed(() => {
    return form.search || form.type || form.city ||
        form.min_capacity || form.max_capacity ||
        form.min_price || form.max_price ||
        form.is_verified !== '' || form.is_featured !== ''
})

const search = () => {
    router.get(route('venues.index'), form, {
        preserveState: true,
        preserveScroll: true
    })
}

const resetFilters = () => {
    form.search = ''
    form.type = ''
    form.city = ''
    form.min_capacity = ''
    form.max_capacity = ''
    form.min_price = ''
    form.max_price = ''
    form.is_verified = ''
    form.is_featured = ''
    search()
}

const formatType = (type) => {
    if (!type) return ''
    return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

const formatPrice = (price) => {
    if (!price) return '0'
    return Number(price).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}
</script>