<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <Link :href="route('events.index')"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors group">
                            <svg class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Events
                        </Link>
                        <div class="h-4 w-px bg-gray-300"></div>
                        <Link :href="route('events.show', event.id)"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            {{ event.title }}
                        </Link>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <h1 class="font-bold text-3xl text-gray-900 leading-tight mb-2">Guest Management</h1>
                            <p class="text-gray-600">Manage your guests for <span class="font-semibold">{{ event.title
                                    }}</span></p>
                        </div>
                        <div class="flex items-center gap-3">

                            <!-- Update the export button in the header section -->
                            <button @click="exportGuests" :disabled="exportLoading"
                                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg v-if="exportLoading" class="animate-spin w-4 h-4 mr-2" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                {{ exportLoading ? 'Generating PDF...' : 'Export PDF' }}
                            </button>

                            <Link :href="route('events.guests.create', event.id)"
                                class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Guest
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Quick Stats -->

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                    <!-- Total Guests -->
                    <div
                        class="rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all bg-gradient-to-br from-blue-50 to-blue-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                    Total Guests
                                </p>
                                <p class="text-2xl font-bold text-blue-900">{{ stats.total }}</p>
                                <p class="text-xs text-blue-700 mt-2 opacity-80">Including +1s: {{
                                    stats.total_guests_count }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Attending -->
                    <div
                        class="rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all bg-gradient-to-br from-green-50 to-emerald-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Attending
                                </p>
                                <p class="text-2xl font-bold text-green-900">{{ stats.attending }}</p>
                                <div class="w-full bg-green-200 rounded-full h-1.5 mt-2">
                                    <div class="bg-green-600 h-1.5 rounded-full"
                                        :style="{ width: stats.total > 0 ? `${(stats.attending / stats.total) * 100}%` : '0%' }">
                                    </div>
                                </div>
                                <p class="text-xs text-green-700 mt-2 opacity-80">
                                    {{ stats.total > 0 ? ((stats.attending / stats.total) * 100).toFixed(1) : '0' }}%
                                    attending
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- VIP Guests -->
                    <div
                        class="rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all bg-gradient-to-br from-yellow-50 to-amber-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-amber-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    VIP Guests
                                </p>
                                <p class="text-2xl font-bold text-amber-900">{{ stats.vip }}</p>
                                <div class="flex items-center mt-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-r from-amber-400 to-yellow-500 flex items-center justify-center mr-2">
                                        <span class="text-xs font-bold text-white">VIP</span>
                                    </div>
                                    <p class="text-xs text-amber-700 opacity-80">
                                        {{ stats.total > 0 ? ((stats.vip / stats.total) * 100).toFixed(1) : '0' }}% of
                                        total
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invitations Sent -->
                    <div
                        class="rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all bg-gradient-to-br from-purple-50 to-violet-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-purple-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    Invitations
                                </p>
                                <p class="text-2xl font-bold text-purple-900">{{ stats.invitations_sent }}</p>
                                <div class="w-full bg-purple-200 rounded-full h-1.5 mt-2">
                                    <div class="bg-gradient-to-r from-purple-500 to-violet-600 h-1.5 rounded-full"
                                        :style="{ width: stats.total > 0 ? `${(stats.invitations_sent / stats.total) * 100}%` : '0%' }">
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs text-purple-700 mt-2 opacity-80">
                                    <span>Sent</span>
                                    <span>{{ stats.total > 0 ? ((stats.invitations_sent / stats.total) * 100).toFixed(1)
                                        : '0'
                                        }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checked In -->
                    <div
                        class="rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all bg-gradient-to-br from-indigo-50 to-blue-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-indigo-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Checked In
                                </p>
                                <p class="text-2xl font-bold text-indigo-900">{{ checkedInCount }}</p>

                                <div class="flex justify-between text-xs text-indigo-700 mt-2 opacity-80">
                                    <span>Check-in Rate</span>
                                    <span>{{ stats.attending > 0 ? ((checkedInCount / stats.attending) * 100).toFixed(1)
                                        : '0'
                                        }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">Guest List</h3>
                                <p class="text-sm text-gray-500">Manage and filter your event guests</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <select v-model="filters.category"
                                        class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All Categories</option>
                                        <option value="vip">VIP</option>
                                        <option value="family">Family</option>
                                        <option value="friends">Friends</option>
                                        <option value="colleagues">Colleagues</option>
                                        <option value="business">Business</option>
                                        <option value="media">Media</option>
                                        <option value="sponsors">Sponsors</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <select v-model="filters.rsvp_status"
                                        class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All RSVP</option>
                                        <option value="pending">Pending</option>
                                        <option value="attending">Attending</option>
                                        <option value="not_attending">Not Attending</option>
                                        <option value="maybe">Maybe</option>
                                    </select>
                                </div>
                                <button @click="showFilters = !showFilters"
                                    class="inline-flex items-center px-3 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    More Filters
                                </button>
                            </div>
                        </div>

                        <!-- Search and Bulk Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input v-model="filters.search" type="search"
                                    placeholder="Search guests by name, email, or phone..."
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                           <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-700">Show:</label>
                                <select v-model="perPage"
                                    class="block pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option :value="5">5</option>
                                    <option :value="10">10</option>
                                    <option :value="15">15</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                </select>
                            </div>
                            <button @click="selectAll"
                                    class="inline-flex items-center px-3 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                    {{ selectedGuests.length === guests.data.length ? 'Deselect All' : 'Select All' }}
                                </button>
                                <div class="relative" v-if="selectedGuests.length > 0">
                                    <button @click="showBulkActions = !showBulkActions"
                                        class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700">
                                        Bulk Actions ({{ selectedGuests.length }})
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div v-if="showBulkActions"
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                        <div class="py-1">
                                            <!-- <button @click="sendBulkInvitations"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Send Invitations
                                            </button> -->

                                            <Link :href="route('events.guests.invitation-composer', {
                                                eventId: event.id,
                                                preselected: selectedGuests
                                            })" @click="showBulkActions = false" class="flex items-center w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors group">
                                                <span class="flex-1 font-medium">Send Invitations</span>
                                                <span
                                                    class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                                                    {{ selectedGuests.length }}
                                                </span>
                                            </Link>

                                            <button @click="updateBulkStatus('attending')"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Mark as Attending
                                            </button>
                                            <button @click="updateBulkStatus('not_attending')"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Mark as Not Attending
                                            </button>
                                            <button @click="exportSelected"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Export Selected
                                            </button>
                                            <button @click="deleteSelected"
                                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                Delete Selected
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Filters -->
                        <div v-if="showFilters"
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Guest Type</label>
                                <select v-model="filters.guest_type"
                                    class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Types</option>
                                    <option value="primary">Primary</option>
                                    <option value="plus_one">Plus One</option>
                                    <option value="child">Child</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="staff">Staff</option>
                                    <option value="speaker">Speaker</option>
                                    <option value="performer">Performer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Special Needs</label>
                                <select v-model="filters.has_special_needs"
                                    class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Guests</option>
                                    <option value="yes">Has Special Needs</option>
                                    <option value="no">No Special Needs</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Invitation Status</label>
                                <select v-model="filters.invitation_status"
                                    class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="not_sent">Not Sent</option>
                                    <option value="sent_pending">Sent - Pending</option>
                                    <option value="responded">Responded</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guest Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                        <input type="checkbox"
                                            :checked="selectedGuests.length === guests.data.length && guests.data.length > 0"
                                            @change="toggleSelectAll"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Guest
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        RSVP Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Invitation
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Check-in
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="guest in guests.data" :key="guest.id"
                                    :class="['hover:bg-gray-50 transition-colors', guest.is_vip ? 'bg-yellow-50' : '']">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" :value="guest.id" v-model="selectedGuests"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    :class="['rounded-full flex items-center justify-center h-10 w-10', getGuestAvatarClass(guest)]">
                                                    <span class="font-semibold text-white">
                                                        {{ getInitials(guest.first_name, guest.last_name) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ guest.first_name }} {{ guest.last_name }}
                                                        <span v-if="guest.is_vip"
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            VIP
                                                        </span>
                                                    </div>
                                                    <div v-if="guest.plus_ones > 0"
                                                        class="ml-2 flex items-center text-xs text-gray-500">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                                        </svg>
                                                        +{{ guest.plus_ones }}
                                                    </div>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <a :href="`mailto:${guest.email}`"
                                                        class="hover:text-indigo-600 hover:underline">{{
                                                            guest.email || 'No email'
                                                        }}</a>
                                                </div>
                                                <div v-if="guest.phone" class="text-xs text-gray-400">
                                                    {{ guest.phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                            :class="getCategoryClass(guest.category)">
                                            {{ guest.category }}
                                        </span>
                                        <div v-if="guest.guest_type !== 'primary'" class="text-xs text-gray-500 mt-1">
                                            {{ guest.guest_type }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                            :class="getRsvpStatusClass(guest.rsvp_status)">
                                            {{ guest.rsvp_status }}
                                        </span>
                                        <div v-if="guest.rsvp_responded_at" class="text-xs text-gray-500 mt-1">
                                            {{ formatDate(guest.rsvp_responded_at) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-medium"
                                                :class="guest.invitation_sent ? 'text-green-600' : 'text-gray-500'">
                                                {{ guest.invitation_sent ? 'Sent' : 'Not Sent' }}
                                            </span>
                                            <span v-if="guest.invitation_sent" class="text-xs text-gray-500">
                                                {{ guest.invitation_method }}
                                            </span>
                                            <span v-if="guest.invitation_sent_at" class="text-xs text-gray-400">
                                                {{ formatDate(guest.invitation_sent_at) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="guest.check_in_time" class="flex flex-col">
                                            <span class="text-xs font-medium text-green-600">Checked In</span>
                                            <span class="text-xs text-gray-500">{{ formatTime(guest.check_in_time)
                                                }}</span>
                                            <span v-if="guest.check_out_time" class="text-xs text-gray-400">
                                                Out: {{ formatTime(guest.check_out_time) }}
                                            </span>
                                        </div>
                                        <span v-else class="text-xs text-gray-500">Not Checked In</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3">
                                            <!-- Edit Button -->
                                            <Link :href="route('events.guests.edit', [event.id, guest.id])"
                                                class="inline-flex items-center p-2 text-indigo-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-all duration-200 group"
                                                title="Edit Guest">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>

                                            <!-- Send Invitation Button -->
                                            <button @click="sendEmailInvitation(guest)" :disabled="guest.invitation_sent"
                                                :class="[
                                                    'inline-flex items-center p-2 rounded-lg transition-all duration-200',
                                                    guest.invitation_sent
                                                        ? 'text-gray-400 cursor-not-allowed opacity-50'
                                                        : 'text-green-600 hover:text-white hover:bg-green-600'
                                                ]"
                                                :title="guest.invitation_sent ? 'Invitation Already Sent' : 'Send Invitation'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </button>

                                            <!-- View Details Button -->
                                            <button @click="showGuestDetails(guest)"
                                                class="inline-flex items-center p-2 text-gray-600 hover:text-white hover:bg-gray-600 rounded-lg transition-all duration-200"
                                                title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Delete Button -->
                                            <button @click="deleteGuest(guest)"
                                                class="inline-flex items-center p-2 text-red-600 hover:text-white hover:bg-red-600 rounded-lg transition-all duration-200"
                                                title="Delete Guest">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
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



                    <!-- Custom Pagination Section -->
                    <div v-if="guests.data && guests.data.length > 0"
                        class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <!-- Showing X to Y of Z results -->
                            <div class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ showingFrom }}</span>
                                to
                                <span class="font-medium">{{ showingTo }}</span>
                                of
                                <span class="font-medium">{{ totalGuests }}</span>
                                results
                            </div>

                            <!-- Pagination Buttons -->
                            <nav class="inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="(link, index) in paginationLinks" :key="index">
                                    <!-- Previous Button -->
                                    <button v-if="index === 0" @click="goToPage(link.url)" :disabled="!link.url" :class="[
                                        'relative inline-flex items-center px-3 py-2 rounded-l-lg border border-gray-300 text-sm font-medium',
                                        link.url
                                            ? 'text-gray-700 bg-white hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500'
                                            : 'text-gray-300 bg-gray-100 cursor-not-allowed'
                                    ]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                        <span class="ml-1 hidden sm:inline">Previous</span>
                                    </button>

                                    <!-- Page Numbers -->
                                    <button v-else-if="index !== paginationLinks.length - 1" @click="goToPage(link.url)"
                                        :disabled="link.active || !link.url" :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active
                                                ? 'z-10 bg-indigo-600 border-indigo-600 text-white'
                                                : link.url
                                                    ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500'
                                                    : 'border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed'
                                        ]" v-html="link.label" />

                                    <!-- Next Button -->
                                    <button v-else @click="goToPage(link.url)" :disabled="!link.url" :class="[
                                        'relative inline-flex items-center px-3 py-2 rounded-r-lg border border-gray-300 text-sm font-medium',
                                        link.url
                                            ? 'text-gray-700 bg-white hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500'
                                            : 'text-gray-300 bg-gray-100 cursor-not-allowed'
                                    ]">
                                        <span class="mr-1 hidden sm:inline">Next</span>
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </template>
                            </nav>
                        </div>
                    </div>


                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No guests yet</h3>
                        <p class="text-gray-500 mb-6">Get started by inviting your first guest to the event.</p>
                        <Link :href="route('events.guests.create', event.id)"
                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add First Guest
                        </Link>
                    </div>
                </div>

             
            </div>
        </div>

        <!-- Guest Details Modal -->
        <Modal :show="showDetailsModal" @close="showDetailsModal = false" max-width="2xl">
            <div class="p-6" v-if="selectedGuest">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ selectedGuest.first_name }} {{ selectedGuest.last_name }}
                            <span v-if="selectedGuest.is_vip"
                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                VIP
                            </span>
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">{{ selectedGuest.email }}</p>
                    </div>
                    <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Basic Information</h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Category:</span>
                                    <span class="text-sm font-medium text-gray-900 capitalize">{{ selectedGuest.category
                                        }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Guest Type:</span>
                                    <span class="text-sm font-medium text-gray-900 capitalize">{{
                                        selectedGuest.guest_type
                                        }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Phone:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ selectedGuest.phone || 'N/A'
                                        }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Language:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ selectedGuest.language_preference
                                        ||
                                        'English' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- RSVP Information -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">RSVP Information</h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Status:</span>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                        :class="getRsvpStatusClass(selectedGuest.rsvp_status)">
                                        {{ selectedGuest.rsvp_status }}
                                    </span>
                                </div>
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Plus Ones:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ selectedGuest.plus_ones }}
                                        allowed</span>
                                </div>
                                <div v-if="selectedGuest.rsvp_responded_at" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Responded:</span>
                                    <span class="text-sm font-medium text-gray-900">{{
                                        formatDateTime(selectedGuest.rsvp_responded_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Special Needs -->
                        <div
                            v-if="selectedGuest.dietary_preference || selectedGuest.allergies || selectedGuest.special_requirements">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Special Requirements</h3>
                            <div class="space-y-3">
                                <div v-if="selectedGuest.dietary_preference" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Dietary:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ selectedGuest.dietary_preference
                                        }}</span>
                                </div>
                                <div v-if="selectedGuest.allergies" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Allergies:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ selectedGuest.allergies }}</span>
                                </div>
                                <div v-if="selectedGuest.special_requirements" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Requirements:</span>
                                    <span class="text-sm font-medium text-gray-900">{{
                                        selectedGuest.special_requirements
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Invitation Information -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Invitation</h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Status:</span>
                                    <span class="text-sm font-medium"
                                        :class="selectedGuest.invitation_sent ? 'text-green-600' : 'text-gray-900'">
                                        {{ selectedGuest.invitation_sent ? 'Sent' : 'Not Sent' }}
                                    </span>
                                </div>
                                <div v-if="selectedGuest.invitation_sent" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Method:</span>
                                    <span class="text-sm font-medium text-gray-900 capitalize">{{
                                        selectedGuest.invitation_method }}</span>
                                </div>
                                <div v-if="selectedGuest.invitation_sent_at" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Sent At:</span>
                                    <span class="text-sm font-medium text-gray-900">{{
                                        formatDateTime(selectedGuest.invitation_sent_at) }}</span>
                                </div>
                                <div v-if="selectedGuest.invitation_link" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Invitation Link:</span>
                                    <a :href="selectedGuest.invitation_link" target="_blank"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 truncate">
                                        View Invitation
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Check-in Information -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Check-in</h3>
                            <div class="space-y-3">
                                <div v-if="selectedGuest.check_in_time" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Check-in:</span>
                                    <span class="text-sm font-medium text-green-600">{{
                                        formatDateTime(selectedGuest.check_in_time) }}</span>
                                </div>
                                <div v-if="selectedGuest.check_out_time" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Check-out:</span>
                                    <span class="text-sm font-medium text-gray-900">{{
                                        formatDateTime(selectedGuest.check_out_time) }}</span>
                                </div>
                                <div v-if="selectedGuest.welcome_pack_sent" class="flex">
                                    <span class="text-sm text-gray-500 w-32 flex-shrink-0">Welcome Pack:</span>
                                    <span class="text-sm font-medium text-green-600">Sent</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="selectedGuest.notes">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Notes</h3>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ selectedGuest.notes }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end">
                    <Link :href="route('events.guests.edit', [event.id, selectedGuest.id])"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700">
                        Edit Guest
                    </Link>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import { debounce } from 'lodash'
import Swal from 'sweetalert2'

const page = usePage();

const props = defineProps({
    event: Object,
    guests: Object,
    stats: Object,
    categories: Object,
    filters: Object
})

const selectedGuests = ref([])
const showBulkActions = ref(false)
const showFilters = ref(false)
const showDetailsModal = ref(false)
const selectedGuest = ref(null)
const exportLoading = ref(false)
const loading = ref(false)

const checkedInCount = computed(() => {
    return props.guests.data.filter(guest => guest.check_in_time).length
})

// Create a single reactive filters object
const filters = ref({
    search: props.filters?.search || '',
    rsvp_status: props.filters?.rsvp_status || '',
    category: props.filters?.category || '',
    guest_type: props.filters?.guest_type || '',
    has_special_needs: props.filters?.has_special_needs || '',
    invitation_status: props.filters?.invitation_status || ''
})

const perPage = ref(props.guests?.per_page || 15)

// Watch per page changes
watch(perPage, (newPerPage) => {
    loading.value = true
    router.get(route('events.guests.index', props.event.id), {
        ...filters.value,
        per_page: newPerPage
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => {
            loading.value = false
        }
    })
})

// Pagination computed properties
const paginationLinks = computed(() => {
    if (!props.guests.links) return []
    return props.guests.links
})

const showingFrom = computed(() => props.guests.from || 0)
const showingTo = computed(() => props.guests.to || 0)
const totalGuests = computed(() => props.guests.total || 0)

// Watch filters and apply debounced search
watch(filters, debounce((newFilters) => {
    loading.value = true
    router.get(route('events.guests.index', props.event.id), newFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => {
            loading.value = false
        }
    })
}, 300), { deep: true })

// Update filters when props.filters change (avoid infinite loop)
watch(() => props.filters, (newFilters) => {
    if (newFilters && JSON.stringify(newFilters) !== JSON.stringify(filters.value)) {
        filters.value = { ...newFilters }
    }
}, { deep: true, immediate: false })

const goToPage = (url) => {
    if (!url) return

    loading.value = true

    // Parse the URL to get the page number
    const urlObj = new URL(url, window.location.origin)
    const pageNum = urlObj.searchParams.get('page')

    // Make request with current filters AND page number
    router.get(route('events.guests.index', props.event.id), {
        ...filters.value,
        page: pageNum
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const selectAll = () => {
    if (selectedGuests.value.length === props.guests.data.length) {
        selectedGuests.value = []
    } else {
        selectedGuests.value = props.guests.data.map(guest => guest.id)
    }
}

const toggleSelectAll = (event) => {
    if (event.target.checked) {
        selectedGuests.value = props.guests.data.map(guest => guest.id)
    } else {
        selectedGuests.value = []
    }
}




const updateBulkStatus = (status) => {
    if (selectedGuests.value.length === 0) {
        Swal.fire({
            title: 'No guests selected',
            text: 'Please select at least one guest to update.',
            icon: 'warning',
            timer: 2000
        })
        return
    }

    const statusLabel = status.replace(/_/g, ' ')
    Swal.fire({
        title: 'Update Status?',
        text: `Mark ${selectedGuests.value.length} selected guests as ${statusLabel}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4F46E5',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, update status',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('events.guests.bulk-update', { eventId: props.event.id }), {
                guest_ids: selectedGuests.value,
                rsvp_status: status
            }, {
                onSuccess: () => {
                    selectedGuests.value = []
                    showBulkActions.value = false
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Guest statuses have been updated successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    })
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update statuses. Please try again.',
                        icon: 'error',
                        timer: 3000
                    })
                }
            })
        }
    })
}


const exportSelected = async () => {
    if (selectedGuests.value.length === 0) {
        Swal.fire({
            title: 'No Guests Selected',
            text: 'Please select guests to export.',
            icon: 'info',
            timer: 2000,
            timerProgressBar: true,
        })
        return
    }

    const selectedGuestsData = props.guests.data.filter(guest =>
        selectedGuests.value.includes(guest.id)
    )
    await exportToPdf(selectedGuestsData, true)
}

const deleteSelected = () => {
    Swal.fire({
        title: 'Delete Guests?',
        text: `Delete ${selectedGuests.value.length} selected guests? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, delete them',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('events.guests.bulk-destroy', props.event.id), {
                guest_ids: selectedGuests.value
            }, {
                onSuccess: () => {
                    selectedGuests.value = []
                    showBulkActions.value = false
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Selected guests have been deleted.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    })
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete guests. Please try again.',
                        icon: 'error',
                        timer: 3000
                    })
                }
            })
        }
    })
}

const exportGuests = async () => {
    await exportToPdf(props.guests.data, false)
}

const exportToPdf = async (guestsData, isSelectedExport = false) => {
    if (guestsData.length === 0) {
        Swal.fire({
            title: 'No Data',
            text: 'There are no guests to export.',
            icon: 'info',
            timer: 2000,
            timerProgressBar: true,
        })
        return
    }

    exportLoading.value = true
    try {
        const { jsPDF } = await import('jspdf')
        const { default: autoTable } = await import('jspdf-autotable')

        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'mm',
            format: 'a4'
        })

        doc.setFontSize(20)
        doc.setTextColor(37, 99, 235)
        doc.text(`${props.event.title} - Guest Report`, 14, 20)

        doc.setFontSize(12)
        doc.setTextColor(0, 0, 0)
        const eventDate = props.event.start_date ?
            new Date(props.event.start_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : 'Date not set'
        doc.text(`Event Date: ${eventDate}`, 14, 30)

        let filterText = 'All Guests'
        if (isSelectedExport) {
            filterText = `Selected Guests (${guestsData.length})`
        }

        if (filters.value.search) filterText += ` | Search: ${filters.value.search}`
        if (filters.value.rsvp_status) filterText += ` | RSVP: ${filters.value.rsvp_status}`
        if (filters.value.category) filterText += ` | Category: ${filters.value.category}`
        if (filters.value.guest_type) filterText += ` | Type: ${filters.value.guest_type}`

        doc.setFontSize(10)
        doc.text(`Filters: ${filterText}`, 14, 38)
        doc.text(`Generated on: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}`, 14, 44)

        const attendingCount = guestsData.filter(g => g.rsvp_status === 'attending').length
        const pendingCount = guestsData.filter(g => g.rsvp_status === 'pending').length
        const notAttendingCount = guestsData.filter(g => g.rsvp_status === 'not_attending').length
        const maybeCount = guestsData.filter(g => g.rsvp_status === 'maybe').length
        const vipCount = guestsData.filter(g => g.is_vip).length
        const checkedInCount = guestsData.filter(g => g.check_in_time).length
        const totalPlusOnes = guestsData.reduce((sum, guest) => sum + Number(guest.plus_ones || 0), 0)

        doc.setFontSize(14)
        doc.text("Guest Summary", 14, 55)

        autoTable(doc, {
            startY: 60,
            head: [['Metric', 'Count', 'Percentage']],
            body: [
                ['Total Guests', guestsData.length, '100%'],
                ['Attending', attendingCount, `${guestsData.length > 0 ? ((attendingCount / guestsData.length) * 100).toFixed(1) : 0}%`],
                ['VIP Guests', vipCount, `${guestsData.length > 0 ? ((vipCount / guestsData.length) * 100).toFixed(1) : 0}%`],
                ['Checked In', checkedInCount, `${guestsData.length > 0 ? ((checkedInCount / guestsData.length) * 100).toFixed(1) : 0}%`],
                ['Plus Ones', totalPlusOnes, '-'],
                ['Pending RSVP', pendingCount, `${guestsData.length > 0 ? ((pendingCount / guestsData.length) * 100).toFixed(1) : 0}%`],
                ['Not Attending', notAttendingCount, `${guestsData.length > 0 ? ((notAttendingCount / guestsData.length) * 100).toFixed(1) : 0}%`],
                ['Maybe', maybeCount, `${guestsData.length > 0 ? ((maybeCount / guestsData.length) * 100).toFixed(1) : 0}%`]
            ],
            styles: {
                fontSize: 10,
                cellPadding: 4,
            },
            headStyles: {
                fillColor: [37, 99, 235],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [248, 250, 252]
            }
        })

        doc.setFontSize(14)
        doc.text("Category Breakdown", 14, doc.lastAutoTable.finalY + 10)

        const categoryData = guestsData.reduce((acc, guest) => {
            const category = guest.category || 'other'
            acc[category] = (acc[category] || 0) + 1
            return acc
        }, {})

        const categoryRows = Object.entries(categoryData).map(([category, count]) => [
            category.charAt(0).toUpperCase() + category.slice(1),
            count,
            `${guestsData.length > 0 ? ((count / guestsData.length) * 100).toFixed(1) : 0}%`
        ])

        autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 15,
            head: [['Category', 'Count', 'Percentage']],
            body: categoryRows,
            styles: {
                fontSize: 10,
                cellPadding: 4,
            },
            headStyles: {
                fillColor: [139, 92, 246],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [245, 243, 255]
            }
        })

        doc.setFontSize(14)
        doc.text("Guest Details", 14, doc.lastAutoTable.finalY + 10)

        const guestRows = guestsData.map(guest => [
            `${guest.first_name} ${guest.last_name}`,
            guest.email || 'N/A',
            guest.phone || 'N/A',
            guest.category ? guest.category.charAt(0).toUpperCase() + guest.category.slice(1) : 'Other',
            guest.guest_type ? guest.guest_type.charAt(0).toUpperCase() + guest.guest_type.slice(1) : 'Primary',
            guest.is_vip ? 'Yes' : 'No',
            guest.rsvp_status ? guest.rsvp_status.charAt(0).toUpperCase() + guest.rsvp_status.slice(1) : 'Pending',
            guest.invitation_sent ? 'Yes' : 'No',
            guest.check_in_time ? 'Yes' : 'No',
            guest.plus_ones || '0'
        ])

        autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 15,
            head: [['Name', 'Email', 'Phone', 'Category', 'Type', 'VIP', 'RSVP', 'Invited', 'Checked In', '+1s']],
            body: guestRows,
            styles: {
                fontSize: 8,
                cellPadding: 3,
                overflow: 'linebreak',
                cellWidth: 'wrap'
            },
            headStyles: {
                fillColor: [16, 185, 129],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [240, 253, 244]
            },
            columnStyles: {
                0: { cellWidth: 25 },
                1: { cellWidth: 35 },
                2: { cellWidth: 20 },
                3: { cellWidth: 15 },
                4: { cellWidth: 15 },
                5: { cellWidth: 10 },
                6: { cellWidth: 15 },
                7: { cellWidth: 15 },
                8: { cellWidth: 15 },
                9: { cellWidth: 10 }
            },
            margin: { left: 14, right: 14 },
            didDrawPage: function (data) {
                const pageCount = doc.internal.getNumberOfPages()
                doc.setFontSize(8)
                doc.setTextColor(100, 100, 100)
                doc.text(
                    `Page ${doc.internal.getCurrentPageInfo().pageNumber} of ${pageCount}`,
                    doc.internal.pageSize.getWidth() - 25,
                    doc.internal.pageSize.getHeight() - 10
                )
            }
        })

        const finalY = doc.lastAutoTable.finalY + 10
        if (finalY < doc.internal.pageSize.getHeight() - 20) {
            doc.setFontSize(9)
            doc.text('Note: This report includes all guest information as of the generation date.', 14, finalY)
            doc.text('For the most up-to-date information, please check the online dashboard.', 14, finalY + 5)
        }

        const fileName = isSelectedExport
            ? `selected_guests_${props.event.id}_${new Date().toISOString().slice(0, 10)}.pdf`
            : `all_guests_${props.event.id}_${new Date().toISOString().slice(0, 10)}.pdf`

        doc.save(fileName)

        Swal.fire({
            title: 'Success!',
            text: `PDF exported successfully with ${guestsData.length} guests.`,
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        })
    } catch (error) {
        console.error('Error generating PDF:', error)
        Swal.fire({
            title: 'Error',
            text: 'Failed to generate PDF. Please try again.',
            icon: 'error',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        })
    } finally {
        exportLoading.value = false
    }
}

const sendEmailInvitation = (guest) => {
    Swal.fire({
        title: 'Send Invitation?',
        text: `Send invitation to ${guest.first_name} ${guest.last_name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, send it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('events.guests.send-invitations', [props.event.id]),
                {
                    guest_ids: [guest.id]
                },
                {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Sent!',
                            text: 'Invitation has been sent successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        })
                    },
                    onError: () => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to send invitation. Please try again.',
                            icon: 'error',
                            timer: 3000
                        })
                    }
                })
        }
    })
}

const showGuestDetails = (guest) => {
    selectedGuest.value = guest
    showDetailsModal.value = true
}

const deleteGuest = (guest) => {
    Swal.fire({
        title: 'Delete Guest?',
        text: `Delete ${guest.first_name} ${guest.last_name}? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('events.guests.destroy', [props.event.id, guest.id]), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Guest has been deleted.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    })
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete guest. Please try again.',
                        icon: 'error',
                        timer: 3000
                    })
                }
            })
        }
    })
}

// Helper functions
const getInitials = (firstName, lastName) => {
    return `${firstName?.[0] || ''}${lastName?.[0] || ''}`.toUpperCase()
}

const getGuestAvatarClass = (guest) => {
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
    return colors[guest.category] || 'bg-gray-500'
}

const getCategoryClass = (category) => {
    const classes = {
        vip: 'bg-yellow-100 text-yellow-800',
        family: 'bg-purple-100 text-purple-800',
        friends: 'bg-blue-100 text-blue-800',
        colleagues: 'bg-green-100 text-green-800',
        business: 'bg-indigo-100 text-indigo-800',
        media: 'bg-pink-100 text-pink-800',
        sponsors: 'bg-red-100 text-red-800',
        other: 'bg-gray-100 text-gray-800'
    }
    return classes[category] || 'bg-gray-100 text-gray-800'
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

const formatTime = (dateTime) => {
    if (!dateTime) return 'N/A'
    return new Date(dateTime).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit'
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
</script>