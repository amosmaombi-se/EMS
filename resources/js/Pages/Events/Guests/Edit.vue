<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <Link :href="route('events.guests.index', event.id)" 
                              class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors group">
                            <svg class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Guests
                        </Link>
                        <div class="h-4 w-px bg-gray-300"></div>
                        <Link :href="route('events.show', event.id)" 
                              class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ event.title }}
                        </Link>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-bold" 
                                     :class="getAvatarColorClass(form.category)">
                                    {{ getInitials(form.first_name, form.last_name) }}
                                </div>
                                <div>
                                    <h1 class="font-bold text-3xl text-gray-900 leading-tight mb-1">Edit Guest</h1>
                                    <p class="text-gray-600">Update details for <span class="font-semibold">{{ form.first_name }} {{ form.last_name }}</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1.5 rounded-full text-sm font-semibold capitalize"
                                  :class="getRsvpStatusClass(form.rsvp_status)">
                                {{ form.rsvp_status.replace('_', ' ') }}
                            </span>
                            <span v-if="form.is_vip" 
                                  class="px-3 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                VIP
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <form @submit.prevent="submit">
                                <div class="p-6 space-y-8">
                                    <!-- Basic Information -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                            <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Basic Information
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                    First Name *
                                                </label>
                                                <input type="text" 
                                                       id="first_name" 
                                                       v-model="form.first_name"
                                                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                       :class="{ 'border-red-300': form.errors.first_name }"
                                                       required>
                                                <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.first_name }}
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Last Name *
                                                </label>
                                                <input type="text" 
                                                       id="last_name" 
                                                       v-model="form.last_name"
                                                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                       :class="{ 'border-red-300': form.errors.last_name }"
                                                       required>
                                                <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.last_name }}
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Email Address
                                                </label>
                                                <input type="email" 
                                                       id="email" 
                                                       v-model="form.email"
                                                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                       :class="{ 'border-red-300': form.errors.email }"
                                                       placeholder="john.doe@example.com">
                                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.email }}
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Phone Number
                                                </label>
                                                <input type="tel" 
                                                       id="phone" 
                                                       v-model="form.phone"
                                                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                       :class="{ 'border-red-300': form.errors.phone }"
                                                       placeholder="+1 (555) 123-4567">
                                                <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.phone }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Guest Classification -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                            <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Guest Classification
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Category *
                                                </label>
                                                <select id="category" 
                                                        v-model="form.category"
                                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                        :class="{ 'border-red-300': form.errors.category }"
                                                        required>
                                                    <option value="vip">VIP</option>
                                                    <option value="family">Family</option>
                                                    <option value="friends">Friends</option>
                                                    <option value="colleagues">Colleagues</option>
                                                    <option value="business">Business</option>
                                                    <option value="media">Media</option>
                                                    <option value="sponsors">Sponsors</option>
                                                    <option value="other">Other</option>
                                                </select>
                                                <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.category }}
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <label for="guest_type" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Guest Type
                                                </label>
                                                <select id="guest_type" 
                                                        v-model="form.guest_type"
                                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors">
                                                    <option value="primary">Primary Guest</option>
                                                    <option value="plus_one">Plus One</option>
                                                    <option value="child">Child</option>
                                                    <option value="vendor">Vendor</option>
                                                    <option value="staff">Staff</option>
                                                    <option value="speaker">Speaker</option>
                                                    <option value="performer">Performer</option>
                                                    <option value="sponsor">Sponsor</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label for="rsvp_status" class="block text-sm font-medium text-gray-700 mb-2">
                                                    RSVP Status *
                                                </label>
                                                <select id="rsvp_status" 
                                                        v-model="form.rsvp_status"
                                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                        :class="{ 'border-red-300': form.errors.rsvp_status }"
                                                        required>
                                                    <option value="pending">Pending</option>
                                                    <option value="attending">Attending</option>
                                                    <option value="not_attending">Not Attending</option>
                                                    <option value="maybe">Maybe</option>
                                                </select>
                                                <p v-if="form.errors.rsvp_status" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.rsvp_status }}
                                                </p>
                                            </div>
                                            
                                            <div>
                                                <label for="language_preference" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Language Preference
                                                </label>
                                                <select id="language_preference" 
                                                        v-model="form.language_preference"
                                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors">
                                                    <option value="en">English</option>
                                                    <option value="es">Spanish</option>
                                                    <option value="fr">French</option>
                                                    <option value="de">German</option>
                                                    <option value="it">Italian</option>
                                                    <option value="pt">Portuguese</option>
                                                    <option value="ru">Russian</option>
                                                    <option value="zh">Chinese</option>
                                                    <option value="ja">Japanese</option>
                                                    <option value="ko">Korean</option>
                                                    <option value="ar">Arabic</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                            <div class="space-y-4">
                                                <div class="flex items-center">
                                                    <input type="checkbox" 
                                                           id="is_vip" 
                                                           v-model="form.is_vip"
                                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                    <label for="is_vip" class="ml-2 block text-sm text-gray-700">
                                                        Mark as VIP Guest
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="space-y-4">
                                                <div class="flex items-center">
                                                    <input type="checkbox" 
                                                           id="plus_one_allowed" 
                                                           v-model="form.plus_one_allowed"
                                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                    <label for="plus_one_allowed" class="ml-2 block text-sm text-gray-700">
                                                        Allow Plus One(s)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div v-if="form.plus_one_allowed" class="mt-6">
                                            <label for="plus_ones" class="block text-sm font-medium text-gray-700 mb-2">
                                                Number of Plus Ones
                                            </label>
                                            <input type="number" 
                                                   id="plus_ones" 
                                                   v-model="form.plus_ones"
                                                   min="0" 
                                                   max="10"
                                                   class="w-32 rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                   :class="{ 'border-red-300': form.errors.plus_ones }">
                                            <p v-if="form.errors.plus_ones" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.plus_ones }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Special Requirements -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                            <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            Special Requirements
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="dietary_preference" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Dietary Preference
                                                </label>
                                                <select id="dietary_preference" 
                                                        v-model="form.dietary_preference"
                                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors">
                                                    <option value="">None</option>
                                                    <option value="Vegetarian">Vegetarian</option>
                                                    <option value="Vegan">Vegan</option>
                                                    <option value="Gluten-Free">Gluten-Free</option>
                                                    <option value="Dairy-Free">Dairy-Free</option>
                                                    <option value="Kosher">Kosher</option>
                                                    <option value="Halal">Halal</option>
                                                    <option value="Pescatarian">Pescatarian</option>
                                                    <option value="Low-Carb">Low-Carb</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Allergies
                                                </label>
                                                <input type="text" 
                                                       id="allergies" 
                                                       v-model="form.allergies"
                                                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                       placeholder="e.g., Peanuts, Shellfish">
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 gap-6 mt-6">
                                            <div>
                                                <label for="special_requirements" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Special Requirements
                                                </label>
                                                <textarea id="special_requirements" 
                                                          v-model="form.special_requirements"
                                                          rows="3"
                                                          class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                          placeholder="Any special requirements for the guest..."></textarea>
                                            </div>
                                            
                                            <div>
                                                <label for="accessibility_needs" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Accessibility Needs
                                                </label>
                                                <textarea id="accessibility_needs" 
                                                          v-model="form.accessibility_needs"
                                                          rows="3"
                                                          class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                          placeholder="e.g., Wheelchair access, hearing assistance..."></textarea>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label for="accommodation_needs" class="block text-sm font-medium text-gray-700 mb-2">
                                                        Accommodation Needs
                                                    </label>
                                                    <textarea id="accommodation_needs" 
                                                              v-model="form.accommodation_needs"
                                                              rows="2"
                                                              class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                              placeholder="Hotel, special room requirements..."></textarea>
                                                </div>
                                                
                                                <div>
                                                    <label for="transportation_needs" class="block text-sm font-medium text-gray-700 mb-2">
                                                        Transportation Needs
                                                    </label>
                                                    <textarea id="transportation_needs" 
                                                              v-model="form.transportation_needs"
                                                              rows="2"
                                                              class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                              placeholder="Airport pickup, parking requirements..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Notes -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                                            <svg class="w-5 h-5 inline-block mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Additional Notes
                                        </h3>
                                        <div>
                                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                                Notes & Comments
                                            </label>
                                            <textarea id="notes" 
                                                      v-model="form.notes"
                                                      rows="4"
                                                      class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                                      placeholder="Any additional notes about this guest..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <Link :href="route('events.guests.index', event.id)" 
                                                  class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                Cancel
                                            </Link>
                                            <button type="button" 
                                                    @click="deleteGuest"
                                                    class="inline-flex items-center px-4 py-2.5 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Delete Guest
                                            </button>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="submit" 
                                                    :disabled="form.processing"
                                                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                {{ form.processing ? 'Saving...' : 'Update Guest' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sidebar (Simplified) -->
                    <div class="space-y-6">
                        <!-- Guest Summary -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Guest Summary</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Invitation Status:</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                          :class="guest.invitation_sent ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        {{ guest.invitation_sent ? 'Sent' : 'Not Sent' }}
                                    </span>
                                </div>
                                
                                <div v-if="guest.invitation_sent" class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Method:</span>
                                        <span class="text-sm font-medium text-gray-900 capitalize">{{ guest.invitation_method || 'email' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Sent On:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatDate(guest.invitation_sent_at) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Check-in Status -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Check-in Status</h3>
                            <div class="space-y-4">
                                <div v-if="guest.check_in_time" class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Check-in Time:</span>
                                        <span class="text-sm font-medium text-green-600">{{ formatDateTime(guest.check_in_time) }}</span>
                                    </div>
                                    <div v-if="guest.check_out_time" class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Check-out Time:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ formatDateTime(guest.check_out_time) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Duration:</span>
                                        <span class="text-sm font-medium text-gray-900">{{ getDuration(guest.check_in_time, guest.check_out_time) }}</span>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500">Guest has not checked in yet</p>
                                </div>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Guest ID:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ guest.id }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Created:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ formatDateTime(guest.created_at) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Last Updated:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ formatDateTime(guest.updated_at) }}</span>
                                </div>
                                <div v-if="guest.rsvp_responded_at" class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">RSVP Responded:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ formatDateTime(guest.rsvp_responded_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Delete Guest</h2>
                        <p class="text-sm text-gray-500">This action cannot be undone</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to delete <span class="font-semibold">{{ guest.first_name }} {{ guest.last_name }}</span> from the event?
                    This will remove all their information including RSVP responses and check-in records.
                </p>
                
                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="confirmDelete"
                        :disabled="deleteForm.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="deleteForm.processing">Deleting...</span>
                        <span v-else>Delete Guest</span>
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
    event: Object,
    guest: Object
})

const showDeleteModal = ref(false)

const form = useForm({
    first_name: props.guest.first_name,
    last_name: props.guest.last_name,
    email: props.guest.email,
    phone: props.guest.phone,
    category: props.guest.category,
    guest_type: props.guest.guest_type || 'primary',
    rsvp_status: props.guest.rsvp_status,
    plus_ones: props.guest.plus_ones || 0,
    plus_one_allowed: props.guest.plus_one_allowed || false,
    dietary_preference: props.guest.dietary_preference || '',
    allergies: props.guest.allergies || '',
    special_requirements: props.guest.special_requirements || '',
    accessibility_needs: props.guest.accessibility_needs || '',
    accommodation_needs: props.guest.accommodation_needs || '',
    transportation_needs: props.guest.transportation_needs || '',
    is_vip: props.guest.is_vip || false,
    language_preference: props.guest.language_preference || 'en',
    notes: props.guest.notes || '',
})

const deleteForm = useForm({})

const submit = async () => {
    try {
        await form.put(route('events.guests.update', [props.event.id, props.guest.id]), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Guest has been updated successfully.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                })
            },
            onError: (errors) => {
                console.error('Update error:', errors)
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update guest. Please check the form for errors.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                })
            }
        })
    } catch (error) {
        console.error('Submission error:', error)
        Swal.fire({
            title: 'Error!',
            text: 'Failed to update guest. Please try again.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        })
    }
}

const deleteGuest = () => {
    showDeleteModal.value = true
}

const confirmDelete = async () => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        html: `Delete <strong>${props.guest.first_name} ${props.guest.last_name}</strong> from this event?<br><span class="text-sm text-gray-500">This action cannot be undone.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete guest!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    })

    if (!result.isConfirmed) {
        showDeleteModal.value = false
        return
    }

    try {
        await deleteForm.delete(route('events.guests.destroy', [props.event.id, props.guest.id]), {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Guest has been deleted successfully.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    router.visit(route('events.guests.index', props.event.id))
                })
            },
            onError: () => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete guest. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                })
            }
        })
    } catch (error) {
        console.error('Delete error:', error)
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete guest. Please try again.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        })
    } finally {
        showDeleteModal.value = false
    }
}

// Helper functions
const getInitials = (firstName, lastName) => {
    return `${firstName?.[0] || ''}${lastName?.[0] || ''}`.toUpperCase()
}

const getAvatarColorClass = (category) => {
    const colors = {
        vip: 'bg-yellow-500',
        family: 'bg-purple-500',
        friends: 'bg-blue-500',
        colleagues: 'bg-green-500',
        business: 'bg-indigo-500',
        media: 'bg-pink-500',
        sponsors: 'bg-red-500',
        other: 'bg-gray-500'
    }
    return colors[category] || 'bg-gray-500'
}

const getRsvpStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        attending: 'bg-green-100 text-green-800',
        not_attending: 'bg-red-100 text-red-800',
        maybe: 'bg-blue-100 text-blue-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatDateTime = (dateTime) => {
    if (!dateTime) return 'N/A'
    const date = new Date(dateTime)
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    }) + ' at ' + date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit'
    })
}

const getDuration = (checkInTime, checkOutTime) => {
    if (!checkInTime) return 'N/A'
    
    const checkIn = new Date(checkInTime)
    const checkOut = checkOutTime ? new Date(checkOutTime) : new Date()
    
    const diffMs = checkOut - checkIn
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60))
    
    if (diffHours > 0) {
        return `${diffHours}h ${diffMinutes}m`
    }
    return `${diffMinutes}m`
}
</script>