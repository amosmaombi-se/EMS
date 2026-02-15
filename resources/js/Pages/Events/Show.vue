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
                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full shadow-sm"
                            :class="getStatusClass(event.status)">
                            <span class="w-2 h-2 rounded-full mr-2 animate-pulse"
                                :class="getStatusDotClass(event.status)"></span>
                            {{ event.status.charAt(0).toUpperCase() + event.status.slice(1) }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <h1 class="font-bold text-3xl text-gray-900 leading-tight mb-2">{{ event.title }}</h1>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ event.event_type?.name }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ formatDate(event.event_date) }}
                                </div>
                                <div v-if="event.venue_name" class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ event.venue_name }}
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 italic">
                            Created by {{ event.user?.name }} • {{ formatDate(event.created_at) }}
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <!-- Guests Card -->
                    <div
                        class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-sm p-5 border border-blue-100 hover:shadow-md hover:border-blue-200 transition-all duration-300 group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-3">
                                    <p
                                        class="text-xs font-semibold text-blue-700 uppercase tracking-wider flex items-center">
                                        <span
                                            class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-2 group-hover:bg-blue-200 transition-colors">
                                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                            </svg>
                                        </span>
                                        Guests
                                    </p>
                                </div>
                                <div class="flex items-baseline mb-3">
                                    <p class="text-2xl font-bold text-gray-900 mr-2">{{ stats.confirmed_guests || 0 }}
                                    </p>
                                    <p class="text-sm text-gray-500">/ {{ stats.total_guests || 0 }}</p>
                                </div>
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Confirmed</span>
                                        <span>{{ stats.total_guests > 0 ? Math.round((stats.confirmed_guests /stats.total_guests) * 100) : 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500"
                                            :style="{ width: stats.total_guests > 0 ? `${(stats.confirmed_guests / stats.total_guests) * 100}%` : '0%' }">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center text-xs text-gray-600 mt-4">
                                    <div class="flex items-center mr-4">
                                        <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                                        <span>{{ stats.confirmed_guests || 0 }} confirmed</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-gray-300 mr-2"></div>
                                        <span>{{ stats.total_guests - stats.confirmed_guests || 0 }} pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Card -->
                    <div
                        class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-sm p-5 border border-green-100 hover:shadow-md hover:border-green-200 transition-all duration-300 group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-3">
                                    <p
                                        class="text-xs font-semibold text-green-700 uppercase tracking-wider flex items-center">
                                        <span
                                            class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center mr-2 group-hover:bg-green-200 transition-colors">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        Tasks
                                    </p>
                                </div>
                                <div class="flex items-baseline mb-3">
                                    <p class="text-2xl font-bold text-gray-900 mr-2">{{ stats.completed_tasks || 0 }}
                                    </p>
                                    <p class="text-sm text-gray-500">/ {{ stats.total_tasks || 0 }}</p>
                                </div>
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Completed</span>
                                        <span>{{ stats.total_tasks > 0 ? Math.round((stats.completed_tasks /
                                            stats.total_tasks)
                                            * 100) : 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500"
                                            :style="{ width: stats.total_tasks > 0 ? `${(stats.completed_tasks / stats.total_tasks) * 100}%` : '0%' }">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-600 mt-4">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center">
                                            <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                            {{ stats.total_tasks - stats.completed_tasks || 0 }} tasks remaining
                                        </span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">
                                            {{ stats.overdue_tasks || 0 }} overdue
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Budget Card -->
                    <div
                        class="bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-sm p-5 border border-purple-100 hover:shadow-md hover:border-purple-200 transition-all duration-300 group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-3">
                                    <p
                                        class="text-xs font-semibold text-purple-700 uppercase tracking-wider flex items-center">
                                        <span
                                            class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center mr-2 group-hover:bg-purple-200 transition-colors">
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        Budget
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <p class="text-2xl font-bold text-gray-900 mb-1">
                                        TZS {{ event.estimated_budget ? event.estimated_budget.toLocaleString() : '0' }}
                                    </p>
                                    <p class="text-xs text-gray-500">Total estimated budget</p>
                                </div>
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Used</span>
                                        <span>{{ stats.budget_used_percentage ? stats.budget_used_percentage.toFixed(1)
                                            : '0.0'
                                            }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-500"
                                            :style="{ width: `${Math.min(stats.budget_used_percentage || 0, 100)}%` }">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-600 mt-4">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center">
                                            <div class="w-2 h-2 rounded-full bg-purple-500 mr-2"></div>
                                            {{ stats.budget_remaining ? stats.budget_remaining.toLocaleString() : '0' }}
                                            remaining
                                        </span>
                                        <span :class="[
                                            'px-2 py-1 rounded text-xs font-medium',
                                            stats.budget_used_percentage > 90 ? 'bg-red-100 text-red-700' :
                                                stats.budget_used_percentage > 70 ? 'bg-amber-100 text-amber-700' :
                                                    'bg-green-100 text-green-700'
                                        ]">
                                            {{ stats.budget_used_percentage > 90 ? 'Over budget' :
                                                stats.budget_used_percentage > 70 ? 'Approaching limit' :
                                            'Within budget' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Card -->
                    <div
                        class="bg-gradient-to-br from-amber-50 to-white rounded-xl shadow-sm p-5 border border-amber-100 hover:shadow-md hover:border-amber-200 transition-all duration-300 group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-3">
                                    <p
                                        class="text-xs font-semibold text-amber-700 uppercase tracking-wider flex items-center">
                                        <span
                                            class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center mr-2 group-hover:bg-amber-200 transition-colors">
                                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        Timeline
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <p class="text-2xl font-bold text-gray-900 mb-1">{{ getDaysUntilEvent() }}</p>
                                    <p class="text-xs text-gray-500">{{ getEventDateRange() }}</p>
                                </div>
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress</span>
                                        <span>{{ getProgressPercentage().toFixed(0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-2 rounded-full transition-all duration-500"
                                            :style="{ width: getProgressPercentage() + '%' }"></div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-600 mt-4">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center">
                                            <div class="w-2 h-2 rounded-full bg-amber-500 mr-2"></div>
                                            {{ getProgressText() }}
                                        </span>
                                        <span :class="[
                                            'px-2 py-1 rounded text-xs font-medium',
                                            getDaysUntilEvent().includes('Overdue') ? 'bg-red-100 text-red-700' :
                                                getDaysUntilEvent().includes('Today') ? 'bg-green-100 text-green-700' :
                                                    'bg-blue-100 text-blue-700'
                                        ]">
                                            {{ getTimelineStatus() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Event Details & Tasks -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Event Details Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="border-b border-gray-200 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Event Information</h3>
                                    <Link :href="route('events.edit', event.id)"
                                        class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Details
                                    </Link>
                                </div>
                            </div>
                            <div class="p-6">
                                <!-- Description -->
                                <div v-if="event.description" class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Description</h4>
                                    <div class="prose prose-sm max-w-none">
                                        <p class="text-gray-600 whitespace-pre-line">{{ event.description }}</p>
                                    </div>
                                </div>

                                <!-- Details Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Date & Time -->
                                    <div class="space-y-4">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Date & Time
                                            </h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center text-sm">
                                                    <span class="text-gray-500 w-24 flex-shrink-0">Start:</span>
                                                    <span class="font-medium text-gray-900">{{
                                                        formatDate(event.event_date) }}
                                                        {{ formatTime(event.start_time) }}</span>
                                                </div>
                                                <div v-if="event.event_end_date" class="flex items-center text-sm">
                                                    <span class="text-gray-500 w-24 flex-shrink-0">End:</span>
                                                    <span class="font-medium text-gray-900">{{
                                                        formatDate(event.event_end_date)
                                                        }} {{ formatTime(event.end_time) }}</span>
                                                </div>
                                                <div v-if="getDaysUntilEvent()" class="flex items-center text-sm">
                                                    <span class="text-gray-500 w-24 flex-shrink-0">Status:</span>
                                                    <span class="font-medium capitalize"
                                                        :class="getStatusTextColor(event.status)">{{ event.status
                                                        }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location -->
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Location
                                            </h4>
                                            <div class="space-y-2">
                                                <div v-if="event.venue_name" class="flex items-start text-sm">
                                                    <span class="text-gray-500 w-24 flex-shrink-0">Venue:</span>
                                                    <span class="font-medium text-gray-900">{{ event.venue_name
                                                        }}</span>
                                                </div>
                                                <div v-if="event.venue_address" class="flex items-start text-sm">
                                                    <span class="text-gray-500 w-24 flex-shrink-0">Address:</span>
                                                    <span class="font-medium text-gray-900">{{ event.venue_address
                                                        }}</span>
                                                </div>
                                                <div v-if="event.city" class="flex items-start text-sm">
                                                    <span class="text-gray-500 w-24 flex-shrink-0">City:</span>
                                                    <span class="font-medium text-gray-900">{{ event.city }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Information -->
                                    <div class="space-y-4">
                                        <!-- Budget -->
                                        <div v-if="event.estimated_budget">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Budget & Capacity
                                            </h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center text-sm">
                                                    <span class="text-gray-500 w-32 flex-shrink-0">Budget:</span>
                                                    <span class="font-medium text-gray-900">TZS {{
                                                        event.estimated_budget.toLocaleString() }}</span>
                                                </div>
                                                <div class="flex items-center text-sm">
                                                    <span class="text-gray-500 w-32 flex-shrink-0">Expected
                                                        Guests:</span>
                                                    <span class="font-medium text-gray-900">{{ event.expected_guests
                                                        }}</span>
                                                </div>
                                                <div class="flex items-center text-sm">
                                                    <span class="text-gray-500 w-32 flex-shrink-0">Visibility:</span>
                                                    <span class="font-medium text-gray-900">{{ event.is_public ?
                                                        'Public' :
                                                        'Private' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notes -->
                                        <div v-if="event.notes">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Notes
                                            </h4>
                                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ event.notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tasks Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="border-b border-gray-200 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <h3 class="text-lg font-semibold text-gray-900">Tasks</h3>
                                        <span
                                            class="ml-3 px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                                            {{ event.tasks?.length || 0 }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Link :href="route('events.tasks.index', event.id)"
                                            class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                                            View All
                                        </Link>
                                        <Link :href="route('events.tasks.create', event.id)"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-lg hover:bg-green-100 transition-colors border border-green-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Task
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <div v-if="event.tasks && event.tasks.length > 0" class="space-y-3">
                                    <div v-for="task in event.tasks.slice(0, 5)" :key="task.id"
                                        class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                        <input type="checkbox" :checked="task.completed" disabled
                                            class="mt-0.5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2 mb-1">
                                                <p class="font-medium text-gray-900"
                                                    :class="{ 'line-through text-gray-500': task.completed }">
                                                    {{ task.title }}
                                                </p>
                                                <span
                                                    class="px-2 py-0.5 text-xs font-semibold rounded-full flex-shrink-0"
                                                    :class="{
                                                        'bg-green-100 text-green-800': task.priority === 'low',
                                                        'bg-yellow-100 text-yellow-800': task.priority === 'medium',
                                                        'bg-red-100 text-red-800': task.priority === 'high'
                                                    }">
                                                    {{ task.priority }}
                                                </span>
                                            </div>
                                            <p v-if="task.description" class="text-sm text-gray-600 mb-2 line-clamp-2">
                                                {{ task.description }}
                                            </p>
                                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                                <span v-if="task.due_date" class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ formatDate(task.due_date) }}
                                                </span>
                                                <span v-if="task.assigned_to_user" class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ task.assigned_to_user.name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="event.tasks.length > 5" class="pt-3 border-t border-gray-200">
                                        <Link :href="route('events.tasks.index', event.id)"
                                            class="flex items-center justify-center text-indigo-600 text-sm font-medium hover:text-indigo-800 transition-colors">
                                            Show {{ event.tasks.length - 5 }} more tasks
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No tasks created yet</p>
                                    <p class="text-sm text-gray-400 mt-1">Start organizing your event by adding tasks
                                    </p>
                                    <Link :href="route('events.tasks.create', event.id)"
                                        class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Create First Task
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="space-y-6">
                        <!-- Action Buttons -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <Link :href="route('events.edit', event.id)"
                                    class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-900">Edit Event</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>

                                <Link :href="route('events.guests.index', event.id)"
                                    class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-900">Add Guest</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>

                                <Link :href="route('events.tasks.create', event.id)"
                                    class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-900">Add Task</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>

                                <button v-if="event.status !== 'cancelled'" @click="cancelEvent"
                                    class="w-full flex items-center justify-between p-3 border border-red-200 rounded-lg hover:bg-red-50 transition-colors text-red-700">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">Cancel Event</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Guest List -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="border-b border-gray-200 px-5 py-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Recent Guests</h3>
                                    <Link :href="route('events.guests.index', event.id)"
                                        class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                                        View All
                                    </Link>
                                </div>
                            </div>
                            <div class="p-5">
                                <div v-if="event.bookings && event.bookings.length > 0" class="space-y-3">
                                    <div v-for="booking in event.bookings.slice(0, 3)" :key="booking.id"
                                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm font-bold text-white">
                                                {{ booking.guest_name ? booking.guest_name.charAt(0).toUpperCase() : 'G'
                                                }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ booking.guest_name
                                                ||
                                                'Guest' }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ booking.guest_email }}</p>
                                        </div>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full capitalize flex-shrink-0"
                                            :class="getBookingStatusClass(booking.status)">
                                            {{ booking.status }}
                                        </span>
                                    </div>
                                    <div v-if="event.bookings.length > 3" class="pt-3 border-t border-gray-200">
                                        <Link :href="route('events.bookings.index', event.id)"
                                            class="flex items-center justify-center text-indigo-600 text-sm font-medium hover:text-indigo-800 transition-colors">
                                            Show {{ event.bookings.length - 3 }} more guests
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8">
                                    <div
                                        class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium text-sm">No guests yet</p>
                                    <Link :href="route('events.bookings.create', event.id)"
                                        class="inline-flex items-center mt-3 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                        Invite your first guest
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Event Status -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Status</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Current Status</span>
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full capitalize"
                                            :class="getStatusClass(event.status)">
                                            {{ event.status }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all"
                                            :class="getStatusProgressClass(event.status)"
                                            :style="{ width: getProgressPercentage() + '%' }"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-4 gap-2 text-center">
                                    <div class="text-xs">
                                        <div class="w-2 h-2 bg-gray-300 rounded-full mx-auto mb-1"></div>
                                        <span class="text-gray-600">Draft</span>
                                    </div>
                                    <div class="text-xs">
                                        <div class="w-2 h-2 bg-blue-300 rounded-full mx-auto mb-1"></div>
                                        <span class="text-gray-600">Planning</span>
                                    </div>
                                    <div class="text-xs">
                                        <div class="w-2 h-2 bg-green-300 rounded-full mx-auto mb-1"></div>
                                        <span class="text-gray-600">Confirmed</span>
                                    </div>
                                    <div class="text-xs">
                                        <div class="w-2 h-2 bg-purple-300 rounded-full mx-auto mb-1"></div>
                                        <span class="text-gray-600">Completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Event Modal -->
        <Modal :show="showCancelModal" @close="showCancelModal = false">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Cancel Event</h2>
                        <p class="text-sm text-gray-500">This action cannot be undone</p>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to cancel "{{ event.title }}"? Please provide a reason for cancellation.
                </p>

                <form @submit.prevent="submitCancellation">
                    <div class="mb-6">
                        <label for="cancellation_reason" class="block text-sm font-semibold text-gray-700 mb-2">
                            Cancellation Reason *
                        </label>
                        <textarea id="cancellation_reason" v-model="cancelForm.cancellation_reason" rows="4"
                            class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm"
                            placeholder="Explain why this event is being cancelled..." required></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showCancelModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            Keep Event
                        </button>
                        <button type="submit" :disabled="cancelForm.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span v-if="cancelForm.processing">Cancelling...</span>
                            <span v-else>Confirm Cancellation</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    event: Object,
    stats: Object
})

const showCancelModal = ref(false)

const cancelForm = useForm({
    cancellation_reason: ''
})

const cancelEvent = () => {
    showCancelModal.value = true
}

const submitCancellation = () => {
    cancelForm.post(route('events.cancel', props.event.id), {
        onSuccess: () => {
            showCancelModal.value = false
        }
    })
}

const formatDate = (date) => {
    if (!date) return 'Not set'
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (time) => {
    if (!time) return ''
    try {
        if (time.includes('T') || time.includes(' ')) {
            return new Date(time).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit'
            })
        }
        const [hours, minutes] = time.split(':')
        const date = new Date()
        date.setHours(parseInt(hours), parseInt(minutes || 0))
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit'
        })
    } catch (e) {
        return time
    }
}

const getDaysUntilEvent = () => {
    if (!props.event.event_date) return 'No date set'
    const today = new Date()
    const eventDate = new Date(props.event.event_date)
    const diffTime = eventDate - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays < 0) return 'Event passed'
    if (diffDays === 0) return 'Today'
    if (diffDays === 1) return 'Tomorrow'
    if (diffDays <= 7) return `${diffDays} days`
    if (diffDays <= 30) return `${Math.ceil(diffDays / 7)} weeks`
    return `${Math.ceil(diffDays / 30)} months`
}

const getProgressPercentage = () => {
    const statusOrder = ['draft', 'planning', 'confirmed', 'completed']
    const currentIndex = statusOrder.indexOf(props.event.status)
    return currentIndex >= 0 ? (currentIndex / (statusOrder.length - 1)) * 100 : 0
}

const getProgressText = () => {
    const percentage = getProgressPercentage()
    if (percentage === 0) return 'Just started'
    if (percentage <= 33) return 'Getting started'
    if (percentage <= 66) return 'In progress'
    if (percentage < 100) return 'Almost there'
    return 'Completed'
}

const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800',
        planning: 'bg-blue-100 text-blue-800',
        confirmed: 'bg-green-100 text-green-800',
        ongoing: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-purple-100 text-purple-800',
        cancelled: 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusDotClass = (status) => {
    const classes = {
        draft: 'bg-gray-400',
        planning: 'bg-blue-400',
        confirmed: 'bg-green-400',
        ongoing: 'bg-yellow-400',
        completed: 'bg-purple-400',
        cancelled: 'bg-red-400'
    }
    return classes[status] || 'bg-gray-400'
}

const getStatusTextColor = (status) => {
    const colors = {
        draft: 'text-gray-700',
        planning: 'text-blue-700',
        confirmed: 'text-green-700',
        ongoing: 'text-yellow-700',
        completed: 'text-purple-700',
        cancelled: 'text-red-700'
    }
    return colors[status] || 'text-gray-700'
}

const getStatusProgressClass = (status) => {
    const classes = {
        draft: 'bg-gray-400',
        planning: 'bg-blue-400',
        confirmed: 'bg-green-400',
        ongoing: 'bg-yellow-400',
        completed: 'bg-purple-400',
        cancelled: 'bg-red-400'
    }
    return classes[status] || 'bg-gray-400'
}

const getBookingStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
        completed: 'bg-purple-100 text-purple-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getEventDateRange = () => {
    if (!props.event.start_date || !props.event.end_date) return 'No date set';
    const start = new Date(props.event.start_date).toLocaleDateString();
    const end = new Date(props.event.end_date).toLocaleDateString();
    return `${start} - ${end}`;
};

const getTimelineStatus = () => {
    const days = parseInt(getDaysUntilEvent());

    if (getDaysUntilEvent().includes('Overdue')) return 'Overdue';
    if (getDaysUntilEvent().includes('Today')) return 'Today';
    if (days <= 7) return 'This week';
    if (days <= 30) return 'This month';
    return 'Upcoming';
};

</script>