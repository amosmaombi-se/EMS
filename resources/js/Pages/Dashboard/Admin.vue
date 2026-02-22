<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30">
            <div v-if="isLoading" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
                <div class="text-center">
                    <div class="relative">
                        <div class="w-20 h-20 border-4 border-gray-200 border-t-blue-500 rounded-full animate-spin"></div>
                        <div class="w-20 h-20 border-4 border-gray-200 border-b-indigo-500 rounded-full animate-spin absolute inset-0" style="animation-duration: 1.5s"></div>
                    </div>
                    <p class="mt-4 text-gray-600 font-medium animate-pulse">Loading your dashboard...</p>
                </div>
            </div>

            <div v-else class="p-3 sm:p-4 lg:p-6">
                <div class="mb-6">
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 bg-clip-text text-transparent mb-2">
                        Dashboard Overview
                    </h1>
                    <p class="text-gray-600 text-sm">Welcome back! Here's what's happening with your events. 🎉</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute inset-0 bg-white/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2.5 bg-white/20 backdrop-blur-sm rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-blue-100 text-xs font-bold tracking-wide">EVENTS</span>
                            </div>
                            <div class="text-4xl font-black text-white mb-2">{{ stats.total_events }}</div>
                            <div class="flex items-center text-blue-50 text-xs">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-bold">+{{ stats.events_this_month }}</span>
                                <span class="ml-1.5">this month</span>
                            </div>
                        </div>
                    </div>

                    <div class="group relative bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute inset-0 bg-white/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2.5 bg-white/20 backdrop-blur-sm rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="text-emerald-100 text-xs font-bold tracking-wide">GUESTS</span>
                            </div>
                            <div class="text-4xl font-black text-white mb-2">{{ stats.total_guests }}</div>
                            <div class="flex items-center text-emerald-50 text-xs">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-bold">{{ stats.total_attending }}</span>
                                <span class="ml-1.5">attending</span>
                            </div>
                        </div>
                    </div>

                    <div class="group relative bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute inset-0 bg-white/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2.5 bg-white/20 backdrop-blur-sm rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-purple-100 text-xs font-bold tracking-wide">REVENUE</span>
                            </div>
                            <div class="text-4xl font-black text-white mb-2">${{ formatNumber(stats.total_revenue) }}</div>
                            <div class="flex items-center text-purple-50 text-xs">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-bold">{{ stats.pending_bookings }}</span>
                                <span class="ml-1.5">pending</span>
                            </div>
                        </div>
                    </div>

                    <div class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute inset-0 bg-white/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2.5 bg-white/20 backdrop-blur-sm rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-amber-100 text-xs font-bold tracking-wide">UPCOMING</span>
                            </div>
                            <div class="text-4xl font-black text-white mb-2">{{ stats.upcoming_events }}</div>
                            <div class="flex items-center text-amber-50 text-xs">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                <span class="font-bold">{{ stats.active_vendors }}</span>
                                <span class="ml-1.5">vendors</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div v-for="(event, index) in recentEvents.slice(0, 3)" :key="event.id" 
                        class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden hover:-translate-y-1">
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-4">
                                <div :class="[
                                    'p-3 rounded-xl shadow-sm',
                                    index === 0 ? 'bg-gradient-to-br from-blue-500 to-blue-600' : '',
                                    index === 1 ? 'bg-gradient-to-br from-purple-500 to-purple-600' : '',
                                    index === 2 ? 'bg-gradient-to-br from-orange-500 to-orange-600' : ''
                                ]">
                                    <component :is="getEventIcon(event.event_type)" class="w-5 h-5 text-white" />
                                </div>
                                <span class="text-xs text-gray-400 font-medium">{{ event.date }}</span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition-colors">{{ event.name }}</h3>
                            
                            <div class="flex -space-x-2 mb-4">
                                <div v-for="n in Math.min(4, event.confirmed_guests)" :key="n" 
                                    :class="[
                                        'w-8 h-8 rounded-full border-2 border-white flex items-center justify-center text-xs font-bold text-white shadow-sm',
                                        index === 0 ? 'bg-gradient-to-br from-blue-400 to-blue-500' : '',
                                        index === 1 ? 'bg-gradient-to-br from-purple-400 to-purple-500' : '',
                                        index === 2 ? 'bg-gradient-to-br from-orange-400 to-orange-500' : ''
                                    ]">
                                    {{ String.fromCharCode(65 + n - 1) }}
                                </div>
                                <div v-if="event.confirmed_guests > 4" 
                                    class="w-8 h-8 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-xs font-bold text-gray-600 shadow-sm">
                                    +{{ event.confirmed_guests - 4 }}
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                                    <span class="font-medium">Completion</span>
                                    <span class="font-bold text-gray-700">{{ event.completion }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div :class="[
                                        'h-2 rounded-full transition-all duration-500',
                                        index === 0 ? 'bg-gradient-to-r from-blue-500 to-blue-600' : '',
                                        index === 1 ? 'bg-gradient-to-r from-purple-500 to-purple-600' : '',
                                        index === 2 ? 'bg-gradient-to-r from-orange-500 to-orange-600' : ''
                                    ]" :style="{ width: event.completion + '%' }"></div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center text-xs text-gray-500 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                    <span class="font-bold text-gray-700">{{ event.confirmed_guests }}/{{ event.guests_count }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-medium text-gray-600">{{ event.eventDay }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                            RECENT EVENTS
                        </h2>
                        <Link :href="route('events.index')" class="px-3 py-1.5 bg-white text-gray-700 hover:text-gray-900 rounded-lg text-xs font-semibold transition-all border border-gray-200 hover:border-gray-300 hover:shadow-sm">
                            View All →
                        </Link>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Event</th>
                                    <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Handler</th>
                                    <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Event Day</th>
                                    <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Guests</th>
                                    <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="event in recentEvents" :key="event.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-5">
                                        <div class="flex items-center space-x-3">
                                            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shadow-sm', event.iconBg || 'bg-gradient-to-br from-gray-100 to-gray-200']">
                                                <component :is="getEventIcon(event.event_type)" class="w-5 h-5 text-gray-600" />
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800 text-sm">{{ event.name }}</div>
                                                <div class="text-xs text-gray-400">{{ event.created }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-5 text-gray-600 font-medium text-sm">{{ event.handler }}</td>
                                    <td class="py-3 px-5 text-gray-600 font-medium text-sm">{{ event.eventDay }}</td>
                                    <td class="py-3 px-5">
                                        <span class="font-bold text-gray-700 text-sm">{{ event.confirmed_guests }}/{{ event.guests_count }}</span>
                                    </td>
                                    <td class="py-3 px-5">
                                        <span :class="['px-3 py-1.5 rounded-lg text-xs font-bold uppercase shadow-sm', event.status_class]">
                                            {{ event.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
                        <div class="mb-5">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                    GUEST OVERVIEW
                                </h2>
                                <div class="flex gap-2">
                                    <button @click="setChartPeriod('monthly')" 
                                        :class="['px-3 py-1.5 rounded-lg text-xs font-bold transition-all', 
                                                chartPeriod === 'monthly' 
                                                    ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm' 
                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
                                        Monthly
                                    </button>
                                    <button @click="setChartPeriod('weekly')"
                                        :class="['px-3 py-1.5 rounded-lg text-xs font-bold transition-all', 
                                                chartPeriod === 'weekly' 
                                                    ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm' 
                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
                                        Weekly
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex items-baseline space-x-3 mb-5">
                                <span class="text-4xl font-black text-gray-800">{{ stats.total_guests }}</span>
                                <div class="flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-emerald-50 to-green-50 rounded-full border border-emerald-100">
                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-xs font-bold text-emerald-700">+12%</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-5">
                                <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-4 rounded-xl border border-emerald-100 hover:shadow-sm transition-shadow">
                                    <div class="text-xs font-bold text-emerald-700 uppercase mb-1.5">Attending</div>
                                    <div class="text-2xl font-black text-emerald-800">{{ guestStats.rsvp_breakdown.attending }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-amber-50 to-yellow-50 p-4 rounded-xl border border-amber-100 hover:shadow-sm transition-shadow">
                                    <div class="text-xs font-bold text-amber-700 uppercase mb-1.5">Pending</div>
                                    <div class="text-2xl font-black text-amber-800">{{ guestStats.rsvp_breakdown.pending }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-red-50 to-rose-50 p-4 rounded-xl border border-red-100 hover:shadow-sm transition-shadow">
                                    <div class="text-xs font-bold text-red-700 uppercase mb-1.5">Not Attending</div>
                                    <div class="text-2xl font-black text-red-800">{{ guestStats.rsvp_breakdown.not_attending }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-4 rounded-xl border border-blue-100 hover:shadow-sm transition-shadow">
                                    <div class="text-xs font-bold text-blue-700 uppercase mb-1.5">Maybe</div>
                                    <div class="text-2xl font-black text-blue-800">{{ guestStats.rsvp_breakdown.maybe }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4">
                            <canvas ref="participantChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
                        <h2 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            UPCOMING EVENTS
                        </h2>
                        
                        <div class="space-y-3 mb-5">
                            <div v-for="(event, index) in upcomingEvents" :key="event.id" 
                                class="group bg-gradient-to-r from-gray-50 to-white p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:shadow-sm transition-all">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-800 text-sm mb-2 group-hover:text-blue-600 transition-colors">{{ event.title }}</h3>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="font-semibold">{{ event.date }} at {{ event.time }}</span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ event.venue }}
                                        </p>
                                    </div>
                                    <span :class="['px-3 py-1 rounded-lg text-xs font-bold uppercase shadow-sm', 
                                        event.status === 'published' ? 'bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-700 border border-emerald-200' : 'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 border border-amber-200']">
                                        {{ event.status }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <span class="text-gray-500 font-medium">Guest confirmation</span>
                                    <span class="font-black text-gray-800">{{ event.confirmed_guests }}/{{ event.expected_guests }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div :class="[
                                        'h-2 rounded-full transition-all duration-500',
                                        index === 0 ? 'bg-gradient-to-r from-blue-500 to-blue-600' : '',
                                        index === 1 ? 'bg-gradient-to-r from-purple-500 to-purple-600' : '',
                                        index === 2 ? 'bg-gradient-to-r from-orange-500 to-orange-600' : ''
                                    ]" :style="{ width: (event.confirmed_guests / event.expected_guests * 100) + '%' }">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Link :href="route('events.index')" 
                            class="block w-full text-center px-4 py-3 bg-gradient-to-r from-gray-800 to-gray-900 text-white rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all font-bold text-sm shadow-sm hover:shadow-md">
                            View All Events →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    stats: Object,
    recentEvents: Array,
    upcomingEvents: Array,
    recentBookings: Array,
    guestStats: Object
})

const isLoading = ref(true)
const participantChart = ref(null)
const chartPeriod = ref('monthly')
let chartInstance = null

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            updateChart()
        }, 100)
    }, 3000)
})

const formatNumber = (num) => {
    return new Intl.NumberFormat().format(num || 0)
}

const getEventIcon = (eventType) => {
    return 'div'
}

const setChartPeriod = (period) => {
    chartPeriod.value = period
    updateChart()
}

const updateChart = () => {
    if (chartInstance) {
        chartInstance.destroy()
    }
    
    if (participantChart.value) {
        const data = chartPeriod.value === 'monthly' ? getMonthlyData() : getWeeklyData()
            
        chartInstance = new Chart(participantChart.value, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: '#3B82F6',
                    borderRadius: 6,
                    barThickness: 14
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: '#E5E7EB' },
                        ticks: { display: false }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' } }
                    }
                }
            }
        })
    }
}

const getMonthlyData = () => {
    const days = Array.from({ length: 31 }, (_, i) => (i + 1).toString())
    const values = Object.values(props.guestStats?.daily || {}).length 
        ? Object.values(props.guestStats.daily) 
        : [150, 200, 180, 220, 190, 250, 240, 210, 280, 260, 290, 310, 280, 320, 350, 380, 360, 340, 370, 350, 330, 310, 290, 270, 250, 230, 210, 190, 170, 150, 130]
    
    return { labels: days, values }
}

const getWeeklyData = () => {
    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    const values = [180, 220, 280, 320, 380, 350, 290]
    return { labels: days, values }
}

watch(chartPeriod, () => {
    updateChart()
})
</script>