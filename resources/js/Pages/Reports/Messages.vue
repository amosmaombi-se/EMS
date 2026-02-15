<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <Link :href="route('dashboard')"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors group">
                            <svg class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Dashboard
                        </Link>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <h1 class="font-bold text-3xl text-gray-900 leading-tight mb-2">Messages Dashboard</h1>
                            <p class="text-gray-600">Monitor and manage all SMS messages</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="refreshData" :disabled="loading"
                                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                <svg :class="['w-4 h-4 mr-2', loading ? 'animate-spin' : '']" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Refresh
                            </button>
                            <button @click="exportMessages" :disabled="exportLoading"
                                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                <svg v-if="exportLoading" class="animate-spin w-4 h-4 mr-2" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                {{ exportLoading ? 'Exporting...' : 'Export CSV' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <!-- Total Messages -->
                    <div
                        class="rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all bg-gradient-to-br from-blue-50 to-blue-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    Total Messages
                                </p>
                                <p class="text-2xl font-bold text-blue-900">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sent Messages -->
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
                                    Sent
                                </p>
                                <p class="text-2xl font-bold text-green-900">{{ stats.sent }}</p>
                                <div class="w-full bg-green-200 rounded-full h-1.5 mt-2">
                                    <div class="bg-green-600 h-1.5 rounded-full"
                                        :style="{ width: stats.total > 0 ? `${(stats.sent / stats.total) * 100}%` : '0%' }">
                                    </div>
                                </div>
                                <p class="text-xs text-green-700 mt-2 opacity-80">
                                    {{ stats.total > 0 ? ((stats.sent / stats.total) * 100).toFixed(1) : '0' }}% success
                                    rate
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Failed Messages -->
                    <div
                        class="rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all bg-gradient-to-br from-red-50 to-rose-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-red-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Failed
                                </p>
                                <p class="text-2xl font-bold text-red-900">{{ stats.failed }}</p>
                                <div class="w-full bg-red-200 rounded-full h-1.5 mt-2">
                                    <div class="bg-red-600 h-1.5 rounded-full"
                                        :style="{ width: stats.total > 0 ? `${(stats.failed / stats.total) * 100}%` : '0%' }">
                                    </div>
                                </div>
                                <p class="text-xs text-red-700 mt-2 opacity-80">
                                    {{ stats.total > 0 ? ((stats.failed / stats.total) * 100).toFixed(1) : '0' }}% failure
                                    rate
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Messages -->
                    <div
                        class="rounded-xl shadow-sm p-5 border border-gray-200 hover:shadow-md transition-all bg-gradient-to-br from-yellow-50 to-amber-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-amber-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Pending
                                </p>
                                <p class="text-2xl font-bold text-amber-900">{{ stats.pending }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">Messages List</h3>
                                <p class="text-sm text-gray-500">Monitor and filter message delivery status</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <select v-model="filters.event_id"
                                        class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All Events</option>
                                        <option v-for="event in events" :key="event.id" :value="event.id">
                                            {{ event.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <select v-model="filters.status"
                                        class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All Status</option>
                                        <option value="sent">Sent</option>
                                        <option value="failed">Failed</option>
                                        <option value="permanently_failed">Permanently Failed</option>
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <select v-model="filters.provider"
                                        class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All Providers</option>
                                        <option value="onfonmedia_v2">OnfonMedia V2</option>
                                        <option value="fcm">Firebase (Push)</option>
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

                        <!-- Search Bar -->
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
                                    placeholder="Search by phone number or message content..."
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-700">Show:</label>
                                    <select v-model="perPage"
                                        class="block pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option :value="10">10</option>
                                        <option :value="25">25</option>
                                        <option :value="50">50</option>
                                        <option :value="100">100</option>
                                        <option :value="200">200</option>
                                    </select>
                                </div>
                                <button @click="selectAll"
                                    class="inline-flex items-center px-3 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                    {{ selectedMessages.length === props.messages.data.length && props.messages.data.length > 0 ? 'Deselect All' : 'Select All' }}
                                </button>
                                <div class="relative" v-if="selectedMessages.length > 0">
                                    <button @click="showBulkActions = !showBulkActions"
                                        class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700">
                                        Bulk Actions ({{ selectedMessages.length }})
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div v-if="showBulkActions"
                                        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                        <div class="py-1">
                                            <button @click="resendReminders"
                                                class="flex items-center w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors group">
                                                <svg class="w-4 h-4 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                <span class="flex-1 font-medium">Send Reminders</span>
                                                <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                                                    {{ selectedMessages.length }}
                                                </span>
                                            </button>
                                            <button @click="exportSelected"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Export Selected
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                                <input v-model="filters.date_from" type="date"
                                    class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                                <input v-model="filters.date_to" type="date"
                                    class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input v-model="filters.phone" type="text" placeholder="255..."
                                    class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                        <input type="checkbox"
                                            :checked="selectedMessages.length === messages.data.length && messages.data.length > 0"
                                            @change="toggleSelectAll"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Phone
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Message
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Provider
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Event
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Created At
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="message in messages.data" :key="message.id"
                                    class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" :value="message.id" v-model="selectedMessages"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">#{{ message.id }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ message.phone }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" :title="message.message">
                                            {{ message.message }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="getStatusClass(message.status)">
                                            {{ formatStatus(message.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-500">{{ message.provider || 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="message.event" class="text-sm text-gray-900">{{ message.event.title }}</span>
                                        <span v-else class="text-sm text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDate(message.created_at) }}</div>
                                        <div class="text-xs text-gray-500">{{ formatTime(message.created_at) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3">
                                            <button @click="viewMessage(message)"
                                                class="inline-flex items-center p-2 text-indigo-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-all duration-200"
                                                title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button v-if="canRetry(message.status)" @click="retryMessage(message)"
                                                class="inline-flex items-center p-2 text-yellow-600 hover:text-white hover:bg-yellow-600 rounded-lg transition-all duration-200"
                                                title="Retry">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="messages.data && messages.data.length > 0"
                        class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ messages.from }}</span>
                                to
                                <span class="font-medium">{{ messages.to }}</span>
                                of
                                <span class="font-medium">{{ messages.total }}</span>
                                results
                            </div>
                            <nav class="inline-flex rounded-lg shadow-sm -space-x-px">
                                <template v-for="(link, index) in messages.links" :key="index">
                                    <button v-if="index === 0" @click="goToPage(link.url)" :disabled="!link.url"
                                        :class="[
                                            'relative inline-flex items-center px-3 py-2 rounded-l-lg border border-gray-300 text-sm font-medium',
                                            link.url
                                                ? 'text-gray-700 bg-white hover:bg-gray-50'
                                                : 'text-gray-300 bg-gray-100 cursor-not-allowed'
                                        ]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button v-else-if="index !== messages.links.length - 1" @click="goToPage(link.url)"
                                        :disabled="link.active || !link.url" :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active
                                                ? 'z-10 bg-indigo-600 border-indigo-600 text-white'
                                                : link.url
                                                    ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                                                    : 'border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed'
                                        ]" v-html="link.label" />
                                    <button v-else @click="goToPage(link.url)" :disabled="!link.url" :class="[
                                        'relative inline-flex items-center px-3 py-2 rounded-r-lg border border-gray-300 text-sm font-medium',
                                        link.url
                                            ? 'text-gray-700 bg-white hover:bg-gray-50'
                                            : 'text-gray-300 bg-gray-100 cursor-not-allowed'
                                    ]">
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
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No messages found</h3>
                        <p class="text-gray-500 mb-6">Try adjusting your filters or check back later.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Details Modal -->
        <Modal :show="showDetailsModal" @close="showDetailsModal = false" max-width="2xl">
            <div class="p-6" v-if="selectedMessage">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Message Details</h2>
                        <p class="text-sm text-gray-500 mt-1">ID: #{{ selectedMessage.id }}</p>
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
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Basic Information</h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32">Phone:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ selectedMessage.phone }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32">Status:</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="getStatusClass(selectedMessage.status)">
                                        {{ formatStatus(selectedMessage.status) }}
                                    </span>
                                </div>
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32">Provider:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ selectedMessage.provider || 'N/A'
                                        }}</span>
                                </div>
                                <div class="flex" v-if="selectedMessage.provider_message_id">
                                    <span class="text-sm text-gray-500 w-32">Provider ID:</span>
                                    <span class="text-sm font-medium text-gray-900">{{
                                        selectedMessage.provider_message_id }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Timestamps</h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="text-sm text-gray-500 w-32">Created:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ formatDateTime(selectedMessage.created_at) }}</span>
                                </div>
                                <div class="flex" v-if="selectedMessage.sent_at">
                                    <span class="text-sm text-gray-500 w-32">Sent:</span>
                                    <span class="text-sm font-medium text-green-600">{{
                                        formatDateTime(selectedMessage.sent_at) }}</span>
                                </div>
                                <div class="flex" v-if="selectedMessage.failed_at">
                                    <span class="text-sm text-gray-500 w-32">Failed:</span>
                                    <span class="text-sm font-medium text-red-600">{{
                                        formatDateTime(selectedMessage.failed_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Message Content</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-line">{{ selectedMessage.message }}</p>
                            </div>
                        </div>

                        <div v-if="selectedMessage.error_message">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Error Details</h3>
                            <div class="bg-red-50 rounded-lg p-4">
                                <p class="text-sm text-red-900">{{ selectedMessage.error_message }}</p>
                            </div>
                        </div>

                        <div v-if="selectedMessage.response">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">API Response</h3>
                            <div class="bg-gray-50 rounded-lg p-4 max-h-64 overflow-y-auto">
                                <pre class="text-xs text-gray-900">{{ formatJson(selectedMessage.response) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end gap-3">
                    <button v-if="canRetry(selectedMessage.status)" @click="retryMessage(selectedMessage)"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-yellow-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Retry Message
                    </button>
                    <button @click="showDetailsModal = false"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Reminder Message Modal -->
        <Modal :show="showReminderModal" @close="showReminderModal = false" max-width="xl">
            <div class="p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Send Reminder Message</h2>
                        <p class="text-sm text-gray-500 mt-1">Customize your reminder message for {{ selectedMessages.length }} recipients</p>
                    </div>
                    <button @click="showReminderModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="reminderMessage" class="block text-sm font-medium text-gray-700 mb-2">
                            Reminder Message
                        </label>
                        <textarea 
                            v-model="reminderMessage" 
                            id="reminderMessage"
                            rows="6"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter your reminder message here..."
                        ></textarea>
                        <p class="mt-2 text-xs text-gray-500">
                            Character count: {{ reminderMessage.length }} / 160 ({{ Math.ceil(reminderMessage.length / 160) }} SMS)
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Tips for effective reminders:</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Keep it concise and clear</li>
                                        <li>Include relevant event details</li>
                                        <li>Add a call-to-action if needed</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end gap-3">
                    <button @click="showReminderModal = false"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-300">
                        Cancel
                    </button>
                    <button @click="sendReminders"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send {{ selectedMessages.length }} Reminder{{ selectedMessages.length > 1 ? 's' : '' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { debounce } from 'lodash'
import Swal from 'sweetalert2'

const props = defineProps({
    messages: Object,
    stats: Object,
    filters: Object,
    events: Array
})

const loading = ref(false)
const exportLoading = ref(false)
const showFilters = ref(false)
const showDetailsModal = ref(false)
const showReminderModal = ref(false)
const selectedMessage = ref(null)
const selectedMessages = ref([])
const showBulkActions = ref(false)
const reminderMessage = ref('')
const perPage = ref(props.messages?.per_page || 50)

const filters = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    provider: props.filters?.provider || '',
    event_id: props.filters?.event_id || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    phone: props.filters?.phone || ''
})

// Watch filters and apply debounced search
watch(filters, debounce((newFilters) => {
    loading.value = true
    router.get(route('reports.messages'), { ...newFilters, per_page: perPage.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => {
            loading.value = false
        }
    })
}, 300), { deep: true })

// Watch per page changes
watch(perPage, (newPerPage) => {
    loading.value = true
    router.get(route('reports.messages'), { ...filters.value, per_page: newPerPage }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => {
            loading.value = false
        }
    })
})

const goToPage = (url) => {
    if (!url) return
    loading.value = true
    const urlObj = new URL(url, window.location.origin)
    const pageNum = urlObj.searchParams.get('page')
    router.get(route('reports.messages'), {
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

const refreshData = () => {
    loading.value = true
    router.reload({
        only: ['messages', 'stats'],
        onFinish: () => {
            loading.value = false
            Swal.fire({
                title: 'Refreshed!',
                text: 'Data has been updated.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            })
        }
    })
}

const viewMessage = (message) => {
    selectedMessage.value = message
    showDetailsModal.value = true
}

const canRetry = (status) => {
    return status === 'failed' || status === 'permanently_failed'
}

const retryMessage = (message) => {
    Swal.fire({
        title: 'Retry Message?',
        text: `Retry sending this message to ${message.phone}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#EAB308',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, retry',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('reports.messages.retry', message.id), {}, {
                onSuccess: () => {
                    showDetailsModal.value = false
                    Swal.fire({
                        title: 'Queued!',
                        text: 'Message has been queued for retry.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    })
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to queue message. Please try again.',
                        icon: 'error',
                        timer: 3000
                    })
                }
            })
        }
    })
}

const exportMessages = () => {
    exportLoading.value = true
    window.location.href = route('reports.export', { format: 'csv', ...filters.value })
    setTimeout(() => {
        exportLoading.value = false
    }, 2000)
}

const selectAll = () => {
    if (selectedMessages.value.length === props.messages.data.length && props.messages.data.length > 0) {
        selectedMessages.value = []
    } else {
        selectedMessages.value = props.messages.data.map(message => message.id)
    }
}

const toggleSelectAll = (event) => {
    if (event.target.checked) {
        selectedMessages.value = props.messages.data.map(message => message.id)
    } else {
        selectedMessages.value = []
    }
}

const resendReminders = () => {
    if (selectedMessages.value.length === 0) {
        Swal.fire({
            title: 'No Messages Selected',
            text: 'Please select messages to resend.',
            icon: 'warning',
            timer: 2000
        })
        return
    }

    // Show reminder modal
    showBulkActions.value = false
    showReminderModal.value = true
    reminderMessage.value = 'This is a reminder about your upcoming event.'
}

const sendReminders = () => {
    if (!reminderMessage.value.trim()) {
        Swal.fire({
            title: 'Message Required',
            text: 'Please enter a reminder message.',
            icon: 'warning',
            timer: 2000
        })
        return
    }

    Swal.fire({
        title: 'Send Reminders?',
        text: `Send reminder to ${selectedMessages.value.length} selected recipients?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4F46E5',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, send reminders',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('messages.bulk-resend'), {
                message_ids: selectedMessages.value,
                reminder_message: reminderMessage.value
            }, {
                onSuccess: () => {
                    selectedMessages.value = []
                    showReminderModal.value = false
                    reminderMessage.value = ''
                    Swal.fire({
                        title: 'Sent!',
                        text: 'Reminders have been queued for sending.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    })
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to queue reminders. Please try again.',
                        icon: 'error',
                        timer: 3000
                    })
                }
            })
        }
    })
}

const exportSelected = () => {
    if (selectedMessages.value.length === 0) {
        Swal.fire({
            title: 'No Messages Selected',
            text: 'Please select messages to export.',
            icon: 'info',
            timer: 2000
        })
        return
    }

    exportLoading.value = true
    window.location.href = route('reports.export', {
        format: 'csv',
        message_ids: selectedMessages.value.join(','),
        ...filters.value
    })
    setTimeout(() => {
        exportLoading.value = false
    }, 2000)
}

// Helper functions
const getStatusClass = (status) => {
    const classes = {
        sent: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800',
        permanently_failed: 'bg-red-200 text-red-900',
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status) => {
    return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatDate = (dateTime) => {
    if (!dateTime) return 'N/A'
    return new Date(dateTime).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (dateTime) => {
    if (!dateTime) return ''
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

const formatJson = (jsonString) => {
    try {
        const obj = typeof jsonString === 'string' ? JSON.parse(jsonString) : jsonString
        return JSON.stringify(obj, null, 2)
    } catch (e) {
        return jsonString
    }
}
</script>