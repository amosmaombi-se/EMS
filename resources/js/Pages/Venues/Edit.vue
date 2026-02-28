<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('venues.show', venue.id)"
                    class="inline-flex items-center p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div class="flex-1 min-w-0">
                    <h2 class="font-bold text-2xl text-gray-900 truncate">Edit: {{ venue.name }}</h2>
                    <p class="mt-1 text-sm text-gray-600">Update the venue details below</p>
                </div>
                <!-- Unsaved changes indicator -->
                <span v-if="form.isDirty"
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-medium rounded-full">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                    Unsaved changes
                </span>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Validation Summary Banner -->
                <div v-if="showValidationSummary && Object.keys(clientErrors).length > 0"
                    class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800">Please fix the following errors before
                                saving:</p>
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

                <form @submit.prevent="submit" novalidate class="space-y-6">

                    <!-- ── Basic Information ─────────────────────────────── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Venue Name <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.name" @blur="touch('name')" type="text"
                                    placeholder="e.g. Grand Ballroom"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('name')">
                                <FieldError :message="getError('name')" :show="hasError('name')" />
                            </div>

                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Venue Type <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.type" @blur="touch('type')"
                                    class="w-full px-3 py-2 rounded-lg border bg-white focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('type')">
                                    <option value="">Select Type</option>
                                    <option v-for="t in venueTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                                </select>
                                <FieldError :message="getError('type')" :show="hasError('type')" />
                            </div>

                            <!-- Virtual Tour URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Virtual Tour URL</label>
                                <input v-model="form.virtual_tour_url" @blur="touch('virtual_tour_url')" type="text"
                                    placeholder="https://..."
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('virtual_tour_url')">
                                <FieldError :message="getError('virtual_tour_url')"
                                    :show="hasError('virtual_tour_url')" />
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                                <textarea v-model="form.description" rows="4" placeholder="Describe the venue..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ── Location ──────────────────────────────────────── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Location
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Street Address <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.address" @blur="touch('address')" type="text"
                                    placeholder="123 Main Street"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('address')">
                                <FieldError :message="getError('address')" :show="hasError('address')" />
                            </div>

                            <!-- City -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.city" @blur="touch('city')" type="text" placeholder="City"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('city')">
                                <FieldError :message="getError('city')" :show="hasError('city')" />
                            </div>

                            <!-- State -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    State / Province <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.state" @blur="touch('state')" type="text" placeholder="State"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('state')">
                                <FieldError :message="getError('state')" :show="hasError('state')" />
                            </div>

                            <!-- Country -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.country" @blur="touch('country')" type="text"
                                    placeholder="Country"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('country')">
                                <FieldError :message="getError('country')" :show="hasError('country')" />
                            </div>

                            <!-- Postal Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Postal Code</label>
                                <input v-model="form.postal_code" type="text" placeholder="Postal Code"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                            </div>

                            <!-- Latitude -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Latitude
                                    <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input v-model="form.latitude" @blur="touch('latitude')" type="number" step="any"
                                    placeholder="e.g. -6.7924"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('latitude')">
                                <FieldError :message="getError('latitude')" :show="hasError('latitude')" />
                            </div>

                            <!-- Longitude -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Longitude
                                    <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input v-model="form.longitude" @blur="touch('longitude')" type="number" step="any"
                                    placeholder="e.g. 39.2083"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('longitude')">
                                <FieldError :message="getError('longitude')" :show="hasError('longitude')" />
                            </div>
                        </div>
                    </div>

                    <!-- ── Capacity & Pricing ─────────────────────────────── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Capacity & Pricing
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                            <!-- Capacity Min -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Min Capacity <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.capacity_min" @blur="touch('capacity_min')" type="number" min="1"
                                    placeholder="e.g. 50"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('capacity_min')">
                                <FieldError :message="getError('capacity_min')" :show="hasError('capacity_min')" />
                            </div>

                            <!-- Capacity Max -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Max Capacity <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.capacity_max"
                                    @blur="touch('capacity_max'); touch('capacity_min')" type="number" min="1"
                                    placeholder="e.g. 500"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('capacity_max')">
                                <FieldError :message="getError('capacity_max')" :show="hasError('capacity_max')" />
                            </div>

                            <!-- Area -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Area (sq ft)
                                    <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input v-model="form.area_sqft" @blur="touch('area_sqft')" type="number" min="0"
                                    step="0.01" placeholder="e.g. 5000"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('area_sqft')">
                                <FieldError :message="getError('area_sqft')" :show="hasError('area_sqft')" />
                            </div>

                            <!-- Price Per Day -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Price Per Day (TZS) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm pointer-events-none">TZS</span>
                                    <input v-model="form.base_price_per_day" @blur="touch('base_price_per_day')"
                                        type="number" min="0" step="0.01" placeholder="0.00"
                                        class="w-full pl-11 pr-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                        :class="fieldClass('base_price_per_day')">
                                </div>
                                <FieldError :message="getError('base_price_per_day')"
                                    :show="hasError('base_price_per_day')" />
                            </div>

                            <!-- Price Per Hour -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Price Per Hour (TZS)
                                    <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm pointer-events-none">TZS</span>
                                    <input v-model="form.base_price_per_hour" @blur="touch('base_price_per_hour')"
                                        type="number" min="0" step="0.01" placeholder="0.00"
                                        class="w-full pl-11 pr-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                        :class="fieldClass('base_price_per_hour')">
                                </div>
                                <FieldError :message="getError('base_price_per_hour')"
                                    :show="hasError('base_price_per_hour')" />
                            </div>

                            <!-- Security Deposit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Security Deposit (TZS)
                                    <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm pointer-events-none">TZS</span>
                                    <input v-model="form.security_deposit" @blur="touch('security_deposit')"
                                        type="number" min="0" step="0.01" placeholder="0.00"
                                        class="w-full pl-11 pr-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                        :class="fieldClass('security_deposit')">
                                </div>
                                <FieldError :message="getError('security_deposit')"
                                    :show="hasError('security_deposit')" />
                            </div>
                        </div>
                    </div>

                    <!-- ── Contact Information ───────────────────────────── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Contact Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Contact Person <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.contact_person" @blur="touch('contact_person')" type="text"
                                    placeholder="Full name"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('contact_person')">
                                <FieldError :message="getError('contact_person')"
                                    :show="hasError('contact_person')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.email" @blur="touch('email')" type="email"
                                    placeholder="venue@example.com"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('email')">
                                <FieldError :message="getError('email')" :show="hasError('email')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Phone <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.phone" @blur="touch('phone')" type="tel"
                                    placeholder="+1 234 567 8900"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fieldClass('phone')">
                                <FieldError :message="getError('phone')" :show="hasError('phone')" />
                            </div>
                        </div>
                    </div>

                    <!-- ── Amenities ─────────────────────────────────────── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Amenities
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            <label v-for="amenity in availableAmenities" :key="amenity.value"
                                class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                                :class="form.amenities.includes(amenity.value) ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200'">
                                <input type="checkbox" :value="amenity.value" v-model="form.amenities"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">{{ amenity.label }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- ── Policies ──────────────────────────────────────── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Policies
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Terms &
                                    Conditions</label>
                                <textarea v-model="form.terms_and_conditions" rows="5"
                                    placeholder="Enter terms and conditions..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cancellation
                                    Policy</label>
                                <textarea v-model="form.cancellation_policy" rows="5"
                                    placeholder="Enter cancellation policy..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ── Settings ──────────────────────────────────────── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Settings
                        </h3>
                        <div class="flex flex-col sm:flex-row gap-6">
                            <label v-for="toggle in toggleSettings" :key="toggle.key"
                                class="flex items-center gap-3 cursor-pointer">
                                <div class="relative" @click="form[toggle.key] = !form[toggle.key]">
                                    <div class="w-10 h-6 rounded-full transition-colors duration-200"
                                        :class="form[toggle.key] ? 'bg-indigo-600' : 'bg-gray-200'">
                                        <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200"
                                            :class="form[toggle.key] ? 'translate-x-4 left-0.5' : 'left-0.5'"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ toggle.label }}</p>
                                    <p class="text-xs text-gray-500">{{ toggle.description }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- ── Form Actions ──────────────────────────────────── -->
                    <div class="flex flex-col sm:flex-row justify-between gap-3 pb-6">
                        <!-- Left: discard / view -->
                        <div class="flex gap-2">
                            <Link :href="route('venues.show', venue.id)"
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Discard & View
                            </Link>
                            <button v-if="form.isDirty" type="button" @click="resetForm"
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Changes
                            </button>
                        </div>

                        <!-- Right: save -->
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, reactive, computed, defineComponent, h } from 'vue'

// ─── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps({
    venue: Object,
})

// ─── Inline FieldError component ─────────────────────────────────────────────
const FieldError = defineComponent({
    props: { message: String, show: Boolean },
    setup(p) {
        return () => p.show && p.message
            ? h('p', { class: 'mt-1 text-xs text-red-600 flex items-center gap-1' }, [
                h('svg', { class: 'w-3.5 h-3.5 flex-shrink-0', fill: 'currentColor', viewBox: '0 0 20 20' }, [
                    h('path', { 'fill-rule': 'evenodd', d: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z', 'clip-rule': 'evenodd' })
                ]),
                p.message
            ])
            : null
    }
})

// ─── Form – pre-populated from prop ───────────────────────────────────────────
const form = useForm({
    name: props.venue.name ?? '',
    type: props.venue.type ?? '',
    description: props.venue.description ?? '',
    address: props.venue.address ?? '',
    city: props.venue.city ?? '',
    state: props.venue.state ?? '',
    country: props.venue.country ?? '',
    postal_code: props.venue.postal_code ?? '',
    latitude: props.venue.latitude ?? '',
    longitude: props.venue.longitude ?? '',
    contact_person: props.venue.contact_person ?? '',
    email: props.venue.email ?? '',
    phone: props.venue.phone ?? '',
    capacity_min: props.venue.capacity_min ?? '',
    capacity_max: props.venue.capacity_max ?? '',
    area_sqft: props.venue.area_sqft ?? '',
    base_price_per_day: props.venue.base_price_per_day ?? '',
    base_price_per_hour: props.venue.base_price_per_hour ?? '',
    security_deposit: props.venue.security_deposit ?? '',
    amenities: props.venue.amenities ?? [],
    virtual_tour_url: props.venue.virtual_tour_url ?? '',
    terms_and_conditions: props.venue.terms_and_conditions ?? '',
    cancellation_policy: props.venue.cancellation_policy ?? '',
    is_active: props.venue.is_active ?? true,
    is_featured: props.venue.is_featured ?? false,
    is_verified: props.venue.is_verified ?? false,
})

// ─── Validation (identical rules to Create.vue) ───────────────────────────────
const touched = reactive({})
const showValidationSummary = ref(false)

const VALID_TYPES = ['indoor', 'outdoor', 'banquet_hall', 'garden', 'rooftop', 'beach', 'hotel', 'restaurant', 'other']

const rules = {
    name(v) {
        if (!v?.trim()) return 'Venue name is required.'
        if (v.trim().length > 255) return 'Venue name must be 255 characters or fewer.'
        return null
    },
    type(v) {
        if (!v) return 'Venue type is required.'
        if (!VALID_TYPES.includes(v)) return 'Please select a valid venue type.'
        return null
    },
    address(v) { return !v?.trim() ? 'Street address is required.' : null },
    city(v) {
        if (!v?.trim()) return 'City is required.'
        if (v.trim().length > 100) return 'City must be 100 characters or fewer.'
        return null
    },
    state(v) {
        if (!v?.trim()) return 'State / Province is required.'
        if (v.trim().length > 100) return 'State must be 100 characters or fewer.'
        return null
    },
    country(v) {
        if (!v?.trim()) return 'Country is required.'
        if (v.trim().length > 100) return 'Country must be 100 characters or fewer.'
        return null
    },
    contact_person(v) { return !v?.trim() ? 'Contact person is required.' : null },
    email(v) {
        if (!v?.trim()) return 'Email address is required.'
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Please enter a valid email address.'
        return null
    },
    phone(v) {
        if (!v?.trim()) return 'Phone number is required.'
        if (!/^[\d\s+\-()]{7,20}$/.test(v)) return 'Please enter a valid phone number (7–20 digits).'
        return null
    },
    capacity_min(v) {
        if (v === '' || v === null || v === undefined) return 'Minimum capacity is required.'
        if (Number(v) < 1) return 'Minimum capacity must be at least 1.'
        return null
    },
    capacity_max(v) {
        if (v === '' || v === null || v === undefined) return 'Maximum capacity is required.'
        if (Number(v) < 1) return 'Maximum capacity must be at least 1.'
        if (form.capacity_min !== '' && Number(v) <= Number(form.capacity_min))
            return 'Maximum capacity must be greater than minimum capacity.'
        return null
    },
    base_price_per_day(v) {
        if (v === '' || v === null || v === undefined) return 'Base price per day is required.'
        if (Number(v) < 0) return 'Price cannot be negative.'
        return null
    },
    latitude(v) {
        if (v === '' || v === null || v === undefined) return null
        const n = Number(v)
        if (isNaN(n) || n < -90 || n > 90) return 'Latitude must be between -90 and 90.'
        return null
    },
    longitude(v) {
        if (v === '' || v === null || v === undefined) return null
        const n = Number(v)
        if (isNaN(n) || n < -180 || n > 180) return 'Longitude must be between -180 and 180.'
        return null
    },
    area_sqft(v) {
        if (v === '' || v === null || v === undefined) return null
        if (Number(v) < 0) return 'Area cannot be negative.'
        return null
    },
    base_price_per_hour(v) {
        if (v === '' || v === null || v === undefined) return null
        if (Number(v) < 0) return 'Price per hour cannot be negative.'
        return null
    },
    security_deposit(v) {
        if (v === '' || v === null || v === undefined) return null
        if (Number(v) < 0) return 'Security deposit cannot be negative.'
        return null
    },
    virtual_tour_url(v) {
        if (!v?.trim()) return null
        try { new URL(v); return null } catch { return 'Please enter a valid URL (e.g. https://example.com).' }
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

const touch = (field) => { touched[field] = true }
const touchAll = () => Object.keys(rules).forEach(f => { touched[f] = true })

const hasError = (field) => (touched[field] || showValidationSummary.value) && !!clientErrors.value[field]
const getError = (field) => form.errors[field] || clientErrors.value[field]

const fieldClass = (field) => hasError(field)
    ? 'border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50'
    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'

const isFormValid = computed(() => Object.keys(clientErrors.value).length === 0)

// ─── Submit ────────────────────────────────────────────────────────────────────
const submit = () => {
    touchAll()
    showValidationSummary.value = true

    if (!isFormValid.value) {
        setTimeout(() => {
            const el = document.querySelector('.border-red-300')
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }, 50)
        return
    }

    form.put(route('venues.update', props.venue.id))
}

// ─── Reset form to original venue values ─────────────────────────────────────
const resetForm = () => {
    form.reset()
    Object.keys(touched).forEach(k => delete touched[k])
    showValidationSummary.value = false
}

const venueTypes = [
    { value: 'indoor', label: 'Indoor' },
    { value: 'outdoor', label: 'Outdoor' },
    { value: 'banquet_hall', label: 'Banquet Hall' },
    { value: 'garden', label: 'Garden' },
    { value: 'rooftop', label: 'Rooftop' },
    { value: 'beach', label: 'Beach' },
    { value: 'hotel', label: 'Hotel' },
    { value: 'restaurant', label: 'Restaurant' },
    { value: 'other', label: 'Other' },
]

const availableAmenities = [
    { value: 'wifi', label: 'Wi-Fi' },
    { value: 'parking', label: 'Parking' },
    { value: 'air_conditioning', label: 'Air Conditioning' },
    { value: 'catering', label: 'Catering' },
    { value: 'av_equipment', label: 'AV Equipment' },
    { value: 'stage', label: 'Stage' },
    { value: 'dance_floor', label: 'Dance Floor' },
    { value: 'outdoor_space', label: 'Outdoor Space' },
    { value: 'bridal_suite', label: 'Bridal Suite' },
    { value: 'bar', label: 'Bar' },
    { value: 'kitchen', label: 'Kitchen' },
    { value: 'wheelchair_accessible', label: 'Wheelchair Accessible' },
    { value: 'security', label: 'Security' },
    { value: 'valet_parking', label: 'Valet Parking' },
    { value: 'generator', label: 'Generator' },
    { value: 'swimming_pool', label: 'Swimming Pool' },
]

const toggleSettings = [
    { key: 'is_active', label: 'Active', description: 'Venue is available for booking' },
    { key: 'is_featured', label: 'Featured', description: 'Show venue in featured sections' },
    { key: 'is_verified', label: 'Verified', description: 'Mark venue as verified' },
]
</script>