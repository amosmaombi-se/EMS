<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <h1 class="font-bold text-3xl text-gray-900 leading-tight mb-2">Create New Booking</h1>
                    <p class="text-gray-600">Create a new booking for your event</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link :href="route('bookings.index')" 
                          class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <form @submit.prevent="submit">
                        <div class="p-6 space-y-8">
                            <!-- Event Selection -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                    <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Event Selection
                                </h3>
                                <div>
                                    <label for="event_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Event *
                                    </label>
                                    <select id="event_id" 
                                            v-model="form.event_id" 
                                            @change="onEventChange"
                                            class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                            :class="{ 'border-red-300': form.errors.event_id }"
                                            required>
                                        <option value="">Choose an event...</option>
                                        <option v-for="event in events" :key="event.id" :value="event.id">
                                            {{ event.title }} - {{ formatDate(event.event_date) }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.event_id" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.event_id }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="form.event_id">
                                <!-- Venue Selection -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                        <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        Venue Selection
                                    </h3>
                                    <div v-if="venues.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <p class="text-gray-500">No venues available for this event</p>
                                    </div>
                                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="venue in venues" 
                                             :key="venue.id"
                                             @click="form.venue_id = venue.id"
                                             :class="[
                                                 'cursor-pointer border-2 rounded-xl p-5 transition-all duration-200',
                                                 form.venue_id === venue.id 
                                                     ? 'border-indigo-600 bg-indigo-50 ring-2 ring-indigo-100' 
                                                     : 'border-gray-200 hover:border-indigo-300 hover:shadow-sm'
                                             ]">
                                            <div class="flex justify-between items-start mb-3">
                                                <h3 class="font-semibold text-gray-900">{{ venue.name }}</h3>
                                                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                                                    {{ venue.type.replace('_', ' ') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2 flex items-center">
                                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ venue.city }}, {{ venue.state }}
                                            </p>
                                            <p class="text-sm text-gray-600 mb-2 flex items-center">
                                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                Capacity: {{ venue.capacity_max }} guests
                                            </p>
                                            <div class="mt-4 pt-4 border-t border-gray-100">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="text-2xl font-bold text-indigo-600">${{ venue.base_price_per_day.toLocaleString() }}</p>
                                                        <p class="text-xs text-gray-500">per day</p>
                                                    </div>
                                                    <div v-if="form.venue_id === venue.id" 
                                                         class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p v-if="form.errors.venue_id" class="mt-3 text-sm text-red-600">
                                        {{ form.errors.venue_id }}
                                    </p>
                                </div>

                                <!-- Vendor Services Selection -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                        <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        Vendor Services
                                    </h3>
                                    <div v-if="vendors.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <p class="text-gray-500">No vendor services available</p>
                                    </div>
                                    <div v-else class="space-y-4">
                                        <div v-for="vendor in vendors" :key="vendor.id" 
                                             class="border border-gray-200 rounded-xl p-5 hover:border-gray-300 transition-colors">
                                            <div class="flex items-start mb-4">
                                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="font-semibold text-gray-900">{{ vendor.business_name }}</h3>
                                                    <p v-if="vendor.description" class="text-sm text-gray-600 mt-1">
                                                        {{ vendor.description }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="space-y-2">
                                                <label v-for="service in vendor.services" 
                                                       :key="service.id"
                                                       :class="[
                                                           'flex items-start p-4 rounded-lg border transition-all duration-200 cursor-pointer',
                                                           isServiceSelected(service.id) 
                                                               ? 'border-indigo-300 bg-indigo-50' 
                                                               : 'border-gray-200 hover:bg-gray-50'
                                                       ]">
                                                    <input type="checkbox" 
                                                           :checked="isServiceSelected(service.id)"
                                                           @change="toggleService(vendor, service)"
                                                           class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <span class="font-medium text-gray-900">{{ service.name }}</span>
                                                                <span v-if="service.pricing_type" 
                                                                      class="ml-2 px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                                                    {{ service.pricing_type.replace('_', ' ') }}
                                                                </span>
                                                            </div>
                                                            <div class="text-right">
                                                                <span class="font-semibold text-indigo-600">
                                                                    ${{ service.base_price.toLocaleString() }}
                                                                </span>
                                                                <span v-if="service.unit" class="text-xs text-gray-500 block">
                                                                    per {{ service.unit }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <p v-if="service.description" class="text-sm text-gray-600 mt-2">
                                                            {{ service.description }}
                                                        </p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Event Dates -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                        <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Event Dates
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                Event Date *
                                            </label>
                                            <input id="event_date"
                                                   v-model="form.event_date" 
                                                   type="date" 
                                                   class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                   :class="{ 'border-red-300': form.errors.event_date }"
                                                   required>
                                            <p v-if="form.errors.event_date" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.event_date }}
                                            </p>
                                        </div>

                                        <div>
                                            <label for="event_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                End Date (Optional)
                                            </label>
                                            <input id="event_end_date"
                                                   v-model="form.event_end_date" 
                                                   type="date" 
                                                   class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors">
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                        <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Additional Information
                                    </h3>
                                    <div>
                                        <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Special Requirements & Notes
                                        </label>
                                        <textarea id="customer_notes"
                                                  v-model="form.customer_notes" 
                                                  rows="4" 
                                                  class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                  placeholder="Any special requirements, dietary needs, accessibility requirements, or additional notes..."></textarea>
                                    </div>
                                </div>

                                <!-- Booking Summary -->
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>
                                    <div class="space-y-3">
                                        <!-- Venue -->
                                        <div v-if="form.venue_id" 
                                             class="flex justify-between items-center py-2 border-b border-gray-200">
                                            <div>
                                                <span class="font-medium text-gray-900">{{ getVenueName(form.venue_id) }}</span>
                                                <p class="text-xs text-gray-500">Venue</p>
                                            </div>
                                            <span class="font-semibold text-gray-900">
                                                ${{ getVenuePrice(form.venue_id).toLocaleString() }}
                                            </span>
                                        </div>

                                        <!-- Services -->
                                        <div v-for="item in form.items" 
                                             :key="item.itemable_id" 
                                             class="flex justify-between items-center py-2 border-b border-gray-200">
                                            <div>
                                                <span class="font-medium text-gray-900">{{ getServiceName(item.itemable_id) }}</span>
                                                <p class="text-xs text-gray-500">{{ getServiceCategory(item.itemable_id) }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="font-semibold text-gray-900">
                                                    ${{ (getServicePrice(item.itemable_id) * item.quantity).toLocaleString() }}
                                                </span>
                                                <p class="text-xs text-gray-500">
                                                    ${{ getServicePrice(item.itemable_id).toLocaleString() }} × {{ item.quantity }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Subtotal -->
                                        <div v-if="form.items.length > 0 || form.venue_id" 
                                             class="pt-3 border-t border-gray-300">
                                            <div class="flex justify-between mb-2">
                                                <span class="font-medium text-gray-700">Subtotal:</span>
                                                <span class="font-medium text-gray-900">${{ calculateSubtotal().toLocaleString() }}</span>
                                            </div>
                                            
                                            <!-- Estimated Tax (if applicable) -->
                                            <div class="flex justify-between text-sm text-gray-500 mb-2">
                                                <span>Estimated Tax ({{ taxRate }}%):</span>
                                                <span>${{ calculateTax().toLocaleString() }}</span>
                                            </div>
                                            
                                            <!-- Total -->
                                            <div class="flex justify-between items-center pt-3 border-t border-gray-300">
                                                <div>
                                                    <span class="font-bold text-lg text-gray-900">Estimated Total:</span>
                                                    <p class="text-xs text-gray-500 mt-1">All prices are estimates</p>
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-bold text-2xl text-indigo-600">
                                                        ${{ calculateTotal().toLocaleString() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Empty Summary -->
                                        <div v-else class="text-center py-8">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-gray-500">Select venue and services to see pricing</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <Link :href="route('bookings.index')" 
                                          class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        Cancel
                                    </Link>
                                </div>
                                <div>
                                    <button type="submit" 
                                            :disabled="form.processing || !canSubmit"
                                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ form.processing ? 'Creating Booking...' : 'Create Booking' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Help Card -->
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Need Help?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Booking Process</h4>
                                <p class="text-sm text-gray-600 mt-1">Select an event, choose venue and services, then review your booking summary.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Payment Security</h4>
                                <p class="text-sm text-gray-600 mt-1">All transactions are secure and encrypted. We never store your payment details.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Support Available</h4>
                                <p class="text-sm text-gray-600 mt-1">Need assistance? Our support team is available 24/7 to help with your booking.</p>
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
import { computed } from 'vue'

const props = defineProps({
    events: Array,
    venues: Array,
    vendors: Array
})

const form = useForm({
    event_id: '',
    venue_id: null,
    event_date: '',
    event_end_date: '',
    items: [],
    customer_notes: ''
})

const taxRate = 8.5 // Example tax rate

const canSubmit = computed(() => {
    return form.event_id && form.venue_id && form.event_date
})

const onEventChange = () => {
    const event = props.events.find(e => e.id === form.event_id)
    if (event) {
        form.event_date = event.event_date.split('T')[0]
    }
}

const isServiceSelected = (serviceId) => {
    return form.items.some(item => item.itemable_id === serviceId)
}

const toggleService = (vendor, service) => {
    const index = form.items.findIndex(item => item.itemable_id === service.id)
    if (index > -1) {
        form.items.splice(index, 1)
    } else {
        form.items.push({
            itemable_type: 'App\\Models\\VendorService',
            itemable_id: service.id,
            quantity: 1,
            unit_price: service.base_price,
            category: service.category
        })
    }
}

const getVenueName = (id) => {
    return props.venues.find(v => v.id === id)?.name || ''
}

const getVenuePrice = (id) => {
    return props.venues.find(v => v.id === id)?.base_price_per_day || 0
}

const getServiceName = (id) => {
    for (const vendor of props.vendors) {
        const service = vendor.services.find(s => s.id === id)
        if (service) return service.name
    }
    return ''
}

const getServiceCategory = (id) => {
    for (const vendor of props.vendors) {
        const service = vendor.services.find(s => s.id === id)
        if (service) return service.category || 'Service'
    }
    return 'Service'
}

const getServicePrice = (id) => {
    for (const vendor of props.vendors) {
        const service = vendor.services.find(s => s.id === id)
        if (service) return service.base_price
    }
    return 0
}

const calculateSubtotal = () => {
    let subtotal = 0
    if (form.venue_id) {
        subtotal += getVenuePrice(form.venue_id)
    }
    form.items.forEach(item => {
        subtotal += getServicePrice(item.itemable_id) * item.quantity
    })
    return subtotal
}

const calculateTax = () => {
    return (calculateSubtotal() * taxRate) / 100
}

const calculateTotal = () => {
    return calculateSubtotal() + calculateTax()
}

const submit = () => {
    if (!canSubmit.value) {
        alert('Please select event, venue, and event date before submitting.')
        return
    }
    
    if (form.items.length === 0) {
        if (!confirm('No services have been selected. Do you want to proceed with just the venue?')) {
            return
        }
    }
    
    form.post(route('bookings.store'))
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}
</script>