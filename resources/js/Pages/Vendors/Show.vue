<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('vendors.index')"
                    class="inline-flex items-center p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="font-bold text-2xl text-gray-900 truncate">{{ vendor.business_name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full"
                            :class="getVerificationClass(vendor.verification_status)">
                            {{ formatVerification(vendor.verification_status) }}
                        </span>
                        <span v-if="vendor.is_featured"
                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                            ★ Featured
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ vendor.category?.name }} · {{ vendor.city }}, {{ vendor.country }}</p>
                </div>
                <Link :href="route('vendors.edit', vendor.id)"
                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Vendor
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- ── Main Content ─── -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Cover + Logo -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="relative h-56 bg-gradient-to-br from-indigo-100 to-gray-100">
                                <img v-if="vendor.cover_image" :src="vendor.cover_image"
                                    :alt="vendor.business_name" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div v-if="vendor.logo"
                                    class="absolute -bottom-7 left-6 w-16 h-16 rounded-xl border-2 border-white shadow-lg overflow-hidden bg-white">
                                    <img :src="vendor.logo" :alt="vendor.business_name"
                                        class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div :class="vendor.logo ? 'pt-10 px-6 pb-6' : 'p-6'">
                                <p v-if="vendor.description" class="text-sm text-gray-600 leading-relaxed">
                                    {{ vendor.description }}
                                </p>
                                <p v-else class="text-sm text-gray-400 italic">No description provided.</p>
                            </div>
                        </div>

                        <!-- Service Areas + Specializations -->
                        <div v-if="(vendor.service_areas?.length || vendor.specializations?.length)"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Service Areas &
                                Specializations</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div v-if="vendor.service_areas?.length">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Service Areas</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="area in vendor.service_areas" :key="area"
                                            class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 text-xs rounded-full border border-blue-200">
                                            {{ area }}
                                        </span>
                                    </div>
                                </div>
                                <div v-if="vendor.specializations?.length">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Specializations</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="spec in vendor.specializations" :key="spec"
                                            class="inline-flex items-center px-2.5 py-1 bg-purple-50 text-purple-700 text-xs rounded-full border border-purple-200">
                                            {{ spec }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Services -->
                        <div v-if="vendor.services?.length" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Services
                                <span class="text-sm font-normal text-gray-400">({{ vendor.services.length }})</span>
                            </h3>
                            <div class="divide-y divide-gray-100">
                                <div v-for="service in vendor.services" :key="service.id"
                                    class="py-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ service.name }}</p>
                                        <p v-if="service.description" class="text-xs text-gray-500 mt-0.5">
                                            {{ service.description }}</p>
                                    </div>
                                    <span v-if="service.price"
                                        class="text-sm font-semibold text-indigo-600 ml-4 whitespace-nowrap">
                                        TZS {{ formatPrice(service.price) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Portfolio -->
                        <div v-if="vendor.portfolios?.length"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Portfolio</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div v-for="item in vendor.portfolios" :key="item.id"
                                    class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                                    <img v-if="item.image_url" :src="item.image_url" :alt="item.title"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    <div v-else
                                        class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div v-if="vendor.reviews?.length"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-base font-semibold text-gray-900">Reviews</h3>
                                <span class="text-xs text-gray-500">{{ vendor.reviews_count }} total</span>
                            </div>
                            <div class="space-y-4">
                                <div v-for="review in vendor.reviews.slice(0, 5)" :key="review.id"
                                    class="border-b border-gray-50 last:border-0 pb-4 last:pb-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ review.user?.name ?? 'Anonymous' }}</p>
                                        <div class="flex items-center">
                                            <svg v-for="s in 5" :key="s" class="w-3.5 h-3.5"
                                                :class="s <= review.rating ? 'text-amber-400' : 'text-gray-200'"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <p v-if="review.comment" class="text-xs text-gray-600">{{ review.comment }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Sidebar ─── -->
                    <div class="space-y-5">

                        <!-- Overview -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Overview</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full"
                                        :class="vendor.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ vendor.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Category</span>
                                    <span class="text-sm font-medium text-gray-900">{{ vendor.category?.name ?? '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Rating</span>
                                    <div v-if="vendor.rating" class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">{{ vendor.rating }}</span>
                                        <span class="text-xs text-gray-400">({{ vendor.total_reviews }})</span>
                                    </div>
                                    <span v-else class="text-sm text-gray-400">No reviews yet</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Experience</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ vendor.years_of_experience ? vendor.years_of_experience + ' years' : '—' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Team Size</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ vendor.team_size ?? '—' }}
                                    </span>
                                </div>
                                <div v-if="vendor.minimum_order_value" class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-sm text-gray-500">Min. Order</span>
                                    <span class="text-sm font-bold text-indigo-600">
                                        TZS {{ formatPrice(vendor.minimum_order_value) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-500">Total Bookings</span>
                                    <span class="text-sm font-medium text-gray-900">{{ vendor.total_bookings ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Contact</h3>
                            <div class="space-y-3">
                                <div v-if="vendor.contact_person" class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Contact Person</p>
                                        <p class="text-sm font-medium text-gray-900">{{ vendor.contact_person }}</p>
                                    </div>
                                </div>
                                <div v-if="vendor.email" class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <a :href="`mailto:${vendor.email}`"
                                            class="text-sm font-medium text-indigo-600 hover:underline">
                                            {{ vendor.email }}</a>
                                    </div>
                                </div>
                                <div v-if="vendor.phone" class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Phone</p>
                                        <a :href="`tel:${vendor.phone}`"
                                            class="text-sm font-medium text-indigo-600 hover:underline">{{ vendor.phone }}</a>
                                    </div>
                                </div>
                                <div v-if="vendor.website" class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Website</p>
                                        <a :href="vendor.website" target="_blank" rel="noopener"
                                            class="text-sm font-medium text-indigo-600 hover:underline truncate block max-w-[180px]">
                                            {{ vendor.website }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Location</h3>
                            <address class="not-italic text-sm text-gray-600 leading-relaxed">
                                {{ vendor.address }}<br>
                                {{ vendor.city }}<span v-if="vendor.state">, {{ vendor.state }}</span>
                                <span v-if="vendor.postal_code"> {{ vendor.postal_code }}</span><br>
                                {{ vendor.country }}
                            </address>
                        </div>

                        <!-- Business Info -->
                        <div v-if="vendor.business_registration_number || vendor.tax_id"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Business Info</h3>
                            <div class="space-y-2">
                                <div v-if="vendor.business_registration_number"
                                    class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Reg. Number</span>
                                    <span class="text-xs font-medium text-gray-900">{{
                                        vendor.business_registration_number }}</span>
                                </div>
                                <div v-if="vendor.tax_id" class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Tax ID</span>
                                    <span class="text-xs font-medium text-gray-900">{{ vendor.tax_id }}</span>
                                </div>
                                <div v-if="vendor.verified_at" class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Verified At</span>
                                    <span class="text-xs font-medium text-gray-900">{{ formatDate(vendor.verified_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="bg-white rounded-lg shadow-sm border border-red-100 p-5">
                            <h3 class="text-sm font-semibold text-red-700 mb-3">Danger Zone</h3>
                            <button @click="showDeleteModal = true"
                                class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors w-full justify-center border border-red-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Vendor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-40" @click="showDeleteModal = false"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Delete Vendor</h3>
                            <p class="text-sm text-gray-500">This action cannot be undone.</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-6">
                        Are you sure you want to delete <strong>{{ vendor.business_name }}</strong>? All associated
                        data will be permanently removed.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button @click="deleteVendor"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            Delete Vendor
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
import { ref } from 'vue'

const props = defineProps({ vendor: Object })

const showDeleteModal = ref(false)

const getVerificationClass = (status) => ({
    pending:  'bg-yellow-100 text-yellow-800',
    verified: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
}[status] || 'bg-gray-100 text-gray-800')

const formatVerification = (status) =>
    status ? status.charAt(0).toUpperCase() + status.slice(1) : ''

const formatPrice = (price) =>
    price ? Number(price).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : '0'

const formatDate = (date) =>
    date ? new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : ''

const deleteVendor = () => {
    router.delete(route('vendors.destroy', props.vendor.id), {
        onSuccess: () => { showDeleteModal.value = false }
    })
}
</script>