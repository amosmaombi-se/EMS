<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">Vendors</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage and organise all your vendors</p>
                </div>
                <Link :href="route('vendors.create')"
                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Vendor
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div v-for="stat in stats" :key="stat.label"
                        class="bg-white rounded-lg border border-gray-200 shadow-sm px-5 py-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                            :class="stat.iconBg">
                            <svg class="w-5 h-5" :class="stat.iconColor" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    :d="stat.icon" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ stat.label }}</p>
                            <p class="text-xl font-bold text-gray-900">{{ stat.value }}</p>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

                    <!-- Toolbar -->
                    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <!-- Search -->
                        <div class="relative w-full sm:w-72">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input v-model="form.search" @input="doSearch" type="text"
                                placeholder="Search vendors..."
                                class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                        </div>

                        <!-- Right-side filters -->
                        <div class="flex flex-wrap items-center gap-2">
                            <select v-model="form.category_id" @change="doSearch"
                                class="px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                                <option value="">All Categories</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>

                            <select v-model="form.verification_status" @change="doSearch"
                                class="px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="rejected">Rejected</option>
                            </select>

                            <select v-model="form.min_rating" @change="doSearch"
                                class="px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                                <option value="">Any Rating</option>
                                <option value="4">4★ & above</option>
                                <option value="3">3★ & above</option>
                                <option value="2">2★ & above</option>
                            </select>

                            <select v-model="form.is_featured" @change="doSearch"
                                class="px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                                <option value="">Featured &amp; All</option>
                                <option value="1">Featured Only</option>
                            </select>

                            <button v-if="hasActiveFilters" @click="resetFilters"
                                class="inline-flex items-center px-3 py-2 text-sm text-red-600 hover:text-red-800 font-medium border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Results count + per-page -->
                    <div v-if="vendors.total > 0"
                        class="px-5 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            Showing <span class="font-semibold text-gray-700">{{ vendors.from }}</span>–<span
                                class="font-semibold text-gray-700">{{ vendors.to }}</span>
                            of <span class="font-semibold text-gray-700">{{ vendors.total }}</span> vendors
                        </p>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Rows per page:</span>
                            <select v-model="form.per_page" @change="doSearch"
                                class="border border-gray-300 rounded px-2 py-1 text-xs bg-white focus:outline-none focus:border-indigo-400">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th v-for="col in columns" :key="col.key"
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                        :class="col.class ?? ''">
                                        <button v-if="col.sortable" @click="sortBy(col.key)"
                                            class="inline-flex items-center gap-1 hover:text-gray-800 transition-colors">
                                            {{ col.label }}
                                            <span class="flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5"
                                                    :class="form.sort === col.key && form.direction === 'asc' ? 'text-indigo-600' : 'text-gray-300'"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 3l5 7H5l5-7z" />
                                                </svg>
                                                <svg class="w-2.5 h-2.5"
                                                    :class="form.sort === col.key && form.direction === 'desc' ? 'text-indigo-600' : 'text-gray-300'"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 17l-5-7h10l-5 7z" />
                                                </svg>
                                            </span>
                                        </button>
                                        <span v-else>{{ col.label }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr v-if="vendors.data.length === 0">
                                    <td :colspan="columns.length" class="px-4 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-700">No vendors found</p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ hasActiveFilters ? 'Try adjusting your filters.' : 'Add your first vendor to get started.' }}
                                                </p>
                                            </div>
                                            <Link v-if="!hasActiveFilters" :href="route('vendors.create')"
                                                class="mt-1 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add Vendor
                                            </Link>
                                            <button v-else @click="resetFilters"
                                                class="mt-1 text-sm text-indigo-600 hover:underline">Clear filters</button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-for="vendor in vendors.data" :key="vendor.id"
                                    class="hover:bg-indigo-50/40 transition-colors group">

                                    <!-- Business Name + Logo -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                                                <img v-if="vendor.logo" :src="vendor.logo"
                                                    :alt="vendor.business_name"
                                                    class="w-full h-full object-cover">
                                                <div v-else
                                                    class="w-full h-full flex items-center justify-center text-gray-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <Link :href="route('vendors.show', vendor.id)"
                                                    class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors truncate block max-w-[200px]">
                                                    {{ vendor.business_name }}
                                                </Link>
                                                <p class="text-xs text-gray-400 truncate max-w-[200px]">{{
                                                    vendor.email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Category -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                                            {{ vendor.category?.name ?? '—' }}
                                        </span>
                                    </td>

                                    <!-- Location -->
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        {{ vendor.city }}<span v-if="vendor.state">, {{ vendor.state }}</span>
                                    </td>

                                    <!-- Rating -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div v-if="vendor.rating" class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-amber-400" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                            <span class="text-sm font-medium text-gray-800">{{ vendor.rating }}</span>
                                            <span class="text-xs text-gray-400">({{ vendor.total_reviews }})</span>
                                        </div>
                                        <span v-else class="text-xs text-gray-400">—</span>
                                    </td>

                                    <!-- Min Order -->
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                        <span v-if="vendor.minimum_order_value" class="font-medium">
                                            TZS {{ formatPrice(vendor.minimum_order_value) }}
                                        </span>
                                        <span v-else class="text-gray-400">—</span>
                                    </td>

                                    <!-- Verification -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full"
                                            :class="getVerificationClass(vendor.verification_status)">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                :class="getVerificationDot(vendor.verification_status)"></span>
                                            {{ formatVerification(vendor.verification_status) }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full"
                                            :class="vendor.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">
                                            {{ vendor.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    <!-- Featured -->
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <svg v-if="vendor.is_featured" class="w-4 h-4 text-amber-400 mx-auto"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        <span v-else class="text-gray-300 text-xs">—</span>
                                    </td>

                                    <!-- Bookings -->
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                        {{ vendor.total_bookings ?? 0 }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <Link :href="route('vendors.show', vendor.id)"
                                                class="inline-flex items-center p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                                                title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <Link :href="route('vendors.edit', vendor.id)"
                                                class="inline-flex items-center p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button @click="confirmDelete(vendor)"
                                                class="inline-flex items-center p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="vendors.last_page > 1"
                        class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-xs text-gray-500">
                            Page {{ vendors.current_page }} of {{ vendors.last_page }}
                        </p>
                        <div class="flex items-center gap-1">
                            <component
                                v-for="link in vendors.links"
                                :key="link.label"
                                :is="link.url ? 'a' : 'span'"
                                v-html="link.label"
                                :href="link.url ?? undefined"
                                @click.prevent="link.url && router.get(link.url, form, { preserveState: true, preserveScroll: true })"
                                class="px-3 py-1.5 text-xs rounded border transition-colors"
                                :class="link.active
                                    ? 'bg-indigo-600 border-indigo-600 text-white font-semibold'
                                    : link.url
                                        ? 'border-gray-300 text-gray-600 hover:bg-gray-50 cursor-pointer'
                                        : 'border-gray-200 text-gray-300 cursor-not-allowed'"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="deleteTarget" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/40" @click="deleteTarget = null"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-11 h-11 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Delete Vendor</h3>
                            <p class="text-sm text-gray-500">This action cannot be undone.</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-6">
                        Are you sure you want to delete <strong class="text-gray-900">{{ deleteTarget?.business_name }}</strong>?
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="deleteTarget = null"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button @click="executeDelete"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            Delete
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
import { reactive, computed, ref } from 'vue'

const props = defineProps({
    vendors:    Object,
    categories: Array,
    filters:    Object,
})

// ── Filters & sort ─────────────────────────────────────────────────────────────
const form = reactive({
    search:              props.filters.search              || '',
    category_id:         props.filters.category_id         || '',
    city:                props.filters.city                || '',
    verification_status: props.filters.verification_status || '',
    is_featured:         props.filters.is_featured         ?? '',
    min_rating:          props.filters.min_rating          || '',
    per_page:            props.filters.per_page            || 25,
    sort:                props.filters.sort                || '',
    direction:           props.filters.direction           || 'asc',
})

const hasActiveFilters = computed(() =>
    form.search || form.category_id || form.city ||
    form.verification_status || form.is_featured !== '' || form.min_rating
)

const doSearch = () => {
    router.get(route('vendors.index'), form, { preserveState: true, preserveScroll: true })
}

const resetFilters = () => {
    Object.assign(form, {
        search: '', category_id: '', city: '',
        verification_status: '', is_featured: '', min_rating: '',
    })
    doSearch()
}

const sortBy = (key) => {
    if (form.sort === key) {
        form.direction = form.direction === 'asc' ? 'desc' : 'asc'
    } else {
        form.sort = key
        form.direction = 'asc'
    }
    doSearch()
}

// ── Delete ─────────────────────────────────────────────────────────────────────
const deleteTarget = ref(null)
const confirmDelete = (vendor) => { deleteTarget.value = vendor }
const executeDelete = () => {
    router.delete(route('vendors.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => { deleteTarget.value = null },
    })
}

// ── Stats ──────────────────────────────────────────────────────────────────────
const stats = computed(() => [
    {
        label: 'Total Vendors',
        value: props.vendors.total,
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        iconBg: 'bg-blue-100', iconColor: 'text-blue-600',
    },
    {
        label: 'Verified',
        value: props.vendors.data.filter(v => v.verification_status === 'verified').length,
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        iconBg: 'bg-green-100', iconColor: 'text-green-600',
    },
    {
        label: 'Pending',
        value: props.vendors.data.filter(v => v.verification_status === 'pending').length,
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        iconBg: 'bg-amber-100', iconColor: 'text-amber-600',
    },
    {
        label: 'Featured',
        value: props.vendors.data.filter(v => v.is_featured).length,
        icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        iconBg: 'bg-purple-100', iconColor: 'text-purple-600',
    },
])

const columns = [
    { key: 'business_name', label: 'Vendor',       sortable: true },
    { key: 'category',      label: 'Category',     sortable: false },
    { key: 'city',          label: 'Location',     sortable: true },
    { key: 'rating',        label: 'Rating',       sortable: true },
    { key: 'min_order',     label: 'Min. Order',   sortable: true },
    { key: 'verification',  label: 'Verification', sortable: false },
    { key: 'status',        label: 'Status',       sortable: false },
    { key: 'featured',      label: '★',            sortable: false, class: 'text-center' },
    { key: 'bookings',      label: 'Bookings',     sortable: true,  class: 'text-center' },
    { key: 'actions',       label: '',             sortable: false, class: 'text-right' },
]

const getVerificationClass = (status) => ({
    pending:  'bg-yellow-100 text-yellow-800',
    verified: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
}[status] || 'bg-gray-100 text-gray-800')

const getVerificationDot = (status) => ({
    pending:  'bg-yellow-500',
    verified: 'bg-green-500',
    rejected: 'bg-red-500',
}[status] || 'bg-gray-400')

const formatVerification = (status) =>
    status ? status.charAt(0).toUpperCase() + status.slice(1) : ''

const formatPrice = (price) =>
    price ? Number(price).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : '0'
</script>