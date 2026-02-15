<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50">
            <div class="p-3 sm:p-4 lg:p-6">
                <!-- Animated Header Section -->
                <div class="mb-4 relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl blur-2xl opacity-10 animate-pulse"></div>
                    <div class="relative">
                        <h1 class="text-2xl sm:text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 mb-1">
                            Dashboard Overview
                        </h1>
                        <p class="text-gray-600 text-xs sm:text-sm">Welcome back! Here's what's happening with your events. 🎉</p>
                    </div>
                </div>

                <!-- Enhanced Stats Cards with Gradients -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                    <!-- Total Events Card -->
                    <div class="group relative bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl sm:rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
                        <div class="absolute inset-0 bg-white/10 transform -skew-y-6 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-2">
                                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="text-white/80 text-xs font-semibold">EVENTS</div>
                            </div>
                            <div class="text-3xl font-black text-white mb-1">{{ stats.total_events }}</div>
                            <div class="flex items-center text-white/90 text-xs">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-semibold">+{{ stats.events_this_month }}</span>
                                <span class="ml-1">this month</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Guests Card -->
                    <div class="group relative bg-gradient-to-br from-green-500 to-emerald-700 rounded-xl sm:rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
                        <div class="absolute inset-0 bg-white/10 transform -skew-y-6 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-2">
                                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="text-white/80 text-xs font-semibold">GUESTS</div>
                            </div>
                            <div class="text-3xl font-black text-white mb-1">{{ stats.total_guests }}</div>
                            <div class="flex items-center text-white/90 text-xs">
                                <svg class="w-3 h-3 mr-1 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-semibold">{{ stats.total_attending }}</span>
                                <span class="ml-1">attending</span>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Card -->
                    <div class="group relative bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl sm:rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
                        <div class="absolute inset-0 bg-white/10 transform -skew-y-6 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-2">
                                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-white/80 text-xs font-semibold">REVENUE</div>
                            </div>
                            <div class="text-3xl font-black text-white mb-1">${{ formatNumber(stats.total_revenue) }}</div>
                            <div class="flex items-center text-white/90 text-xs">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-semibold">{{ stats.pending_bookings }}</span>
                                <span class="ml-1">pending</span>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events Card -->
                    <div class="group relative bg-gradient-to-br from-orange-500 to-red-600 rounded-xl sm:rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
                        <div class="absolute inset-0 bg-white/10 transform -skew-y-6 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-2">
                                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-white/80 text-xs font-semibold">UPCOMING</div>
                            </div>
                            <div class="text-3xl font-black text-white mb-1">{{ stats.upcoming_events }}</div>
                            <div class="flex items-center text-white/90 text-xs">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                <span class="font-semibold">{{ stats.active_vendors }}</span>
                                <span class="ml-1">vendors</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Event Cards with Images -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div v-for="(event, index) in recentEvents.slice(0, 3)" :key="event.id" 
                        class="group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                        <!-- Gradient Background -->
                        <div :class="[
                            'absolute inset-0 opacity-90',
                            index === 0 ? 'bg-gradient-to-br from-cyan-400 via-blue-500 to-indigo-600' : '',
                            index === 1 ? 'bg-gradient-to-br from-pink-400 via-rose-500 to-red-600' : '',
                            index === 2 ? 'bg-gradient-to-br from-violet-400 via-purple-500 to-fuchsia-600' : ''
                        ]"></div>
                        
                        <!-- Pattern Overlay -->
                        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23fff\' fill-opacity=\'1\'%3E%3Cpath d=\'M0 0h20v20H0z\'/%3E%3C/g%3E%3C/svg%3E'); background-size: 40px 40px;"></div>
                        
                        <div class="relative p-4">
                            <!-- Icon Badge -->
                            <div class="absolute top-4 right-4 p-2 bg-white/30 backdrop-blur-md rounded-xl shadow-md">
                                <component :is="getEventIcon(event.event_type)" class="w-4 h-4 text-white" />
                            </div>
                            
                            <div class="mt-8">
                                <h3 class="text-xl font-black text-white mb-2 line-clamp-1">{{ event.name }}</h3>
                                
                                <!-- Avatar Stack -->
                                <div class="flex -space-x-2 mb-3">
                                    <div v-for="n in Math.min(5, event.confirmed_guests)" :key="n" 
                                        class="w-7 h-7 rounded-full border-2 border-white bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-xs shadow-md">
                                        {{ String.fromCharCode(65 + n - 1) }}
                                    </div>
                                    <div v-if="event.confirmed_guests > 5" 
                                        class="w-7 h-7 rounded-full border-2 border-white bg-white/30 backdrop-blur-sm flex items-center justify-center text-white font-bold text-xs shadow-md">
                                        +{{ event.confirmed_guests - 5 }}
                                    </div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <div class="flex items-center justify-between text-xs text-white/90 mb-1">
                                        <span class="font-semibold">Completion</span>
                                        <span class="font-black">{{ event.completion }}%</span>
                                    </div>
                                    <div class="w-full bg-white/20 backdrop-blur-sm rounded-full h-2 overflow-hidden">
                                        <div class="bg-white h-2 rounded-full shadow-md transition-all duration-500" 
                                            :style="{ width: event.completion + '%' }"></div>
                                    </div>
                                </div>
                                
                                <!-- Footer Info -->
                                <div class="flex justify-between items-center text-white/90 text-xs">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                        </svg>
                                        <span class="font-bold">{{ event.confirmed_guests }}/{{ event.guests_count }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="font-bold">{{ event.eventDay }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Events Table -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl overflow-hidden mb-6 border border-white/20">
                    <div class="px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 flex justify-between items-center">
                        <h2 class="text-lg font-black text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                            RECENT EVENTS
                        </h2>
                        <Link :href="route('events.index')" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-lg text-xs font-semibold transition-all hover:scale-105">
                            View All →
                        </Link>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="text-left py-2 px-4 text-xs font-black text-gray-700 uppercase tracking-wider">Event</th>
                                    <th class="text-left py-2 px-4 text-xs font-black text-gray-700 uppercase tracking-wider">Handler</th>
                                    <th class="text-left py-2 px-4 text-xs font-black text-gray-700 uppercase tracking-wider">Event Day</th>
                                    <th class="text-left py-2 px-4 text-xs font-black text-gray-700 uppercase tracking-wider">Guests</th>
                                    <th class="text-left py-2 px-4 text-xs font-black text-gray-700 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="event in recentEvents" :key="event.id" class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200">
                                    <td class="py-2 px-4">
                                        <div class="flex items-center space-x-2">
                                            <div :class="['w-8 h-8 rounded-lg flex items-center justify-center shadow-md transform hover:scale-110 transition-transform', event.iconBg]">
                                                <component :is="getEventIcon(event.event_type)" class="w-4 h-4 text-white" />
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 text-sm">{{ event.name }}</div>
                                                <div class="text-xs text-gray-500">{{ event.created }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 text-gray-700 font-medium text-sm">{{ event.handler }}</td>
                                    <td class="py-2 px-4 text-gray-700 font-medium text-sm">{{ event.eventDay }}</td>
                                    <td class="py-2 px-4">
                                        <span class="font-bold text-gray-900 text-sm">{{ event.confirmed_guests }}/{{ event.guests_count }}</span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span :class="['px-2 py-1 rounded-full text-xs font-black uppercase shadow-sm', event.status_class]">
                                            {{ event.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Guest Overview -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl p-4 border border-white/20">
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                    GUEST OVERVIEW
                                </h2>
                                <div class="flex gap-1">
                                    <button @click="setChartPeriod('monthly')" 
                                        :class="['px-3 py-1 rounded-lg text-xs font-bold transition-all shadow-sm', 
                                                chartPeriod === 'monthly' 
                                                    ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-md scale-105' 
                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
                                        Monthly
                                    </button>
                                    <button @click="setChartPeriod('weekly')"
                                        :class="['px-3 py-1 rounded-lg text-xs font-bold transition-all shadow-sm', 
                                                chartPeriod === 'weekly' 
                                                    ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-md scale-105' 
                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
                                        Weekly
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex items-baseline space-x-2 mb-4">
                                <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">{{ stats.total_guests }}</span>
                                <div class="flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r from-green-100 to-emerald-100 rounded-full">
                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-xs font-bold text-green-700">+12%</span>
                                </div>
                            </div>

                            <!-- RSVP Breakdown -->
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <div class="bg-gradient-to-br from-green-100 to-emerald-100 p-3 rounded-xl border border-green-200 shadow-sm hover:shadow transition-shadow">
                                    <div class="text-xs font-bold text-green-600 uppercase mb-1">Attending</div>
                                    <div class="text-xl font-black text-green-700">{{ guestStats.rsvp_breakdown.attending }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-yellow-100 to-amber-100 p-3 rounded-xl border border-yellow-200 shadow-sm hover:shadow transition-shadow">
                                    <div class="text-xs font-bold text-yellow-600 uppercase mb-1">Pending</div>
                                    <div class="text-xl font-black text-yellow-700">{{ guestStats.rsvp_breakdown.pending }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-red-100 to-rose-100 p-3 rounded-xl border border-red-200 shadow-sm hover:shadow transition-shadow">
                                    <div class="text-xs font-bold text-red-600 uppercase mb-1">Not Attending</div>
                                    <div class="text-xl font-black text-red-700">{{ guestStats.rsvp_breakdown.not_attending }}</div>
                                </div>
                                <div class="bg-gradient-to-br from-blue-100 to-cyan-100 p-3 rounded-xl border border-blue-200 shadow-sm hover:shadow transition-shadow">
                                    <div class="text-xs font-bold text-blue-600 uppercase mb-1">Maybe</div>
                                    <div class="text-xl font-black text-blue-700">{{ guestStats.rsvp_breakdown.maybe }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-3">
                            <canvas ref="participantChart"></canvas>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl p-4 border border-white/20">
                        <h2 class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            UPCOMING EVENTS
                        </h2>
                        
                        <div class="space-y-3 mb-4">
                            <div v-for="(event, index) in upcomingEvents" :key="event.id" 
                                class="group bg-gradient-to-r from-white to-gray-50 p-3 rounded-xl border border-gray-100 hover:border-purple-200 hover:shadow-md transition-all duration-300">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-black text-gray-900 text-sm mb-1">{{ event.title }}</h3>
                                        <div class="flex items-center gap-2 text-xs text-gray-600">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="font-semibold">{{ event.date }}</span>
                                            <span>at {{ event.time }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ event.venue }}
                                        </p>
                                    </div>
                                    <span :class="['px-2 py-0.5 rounded-full text-xs font-black uppercase', 
                                        event.status === 'published' ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 border border-green-200' : 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-700 border border-yellow-200']">
                                        {{ event.status }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-gray-600 font-semibold">Guest confirmation</span>
                                    <span class="font-black text-gray-900">{{ event.confirmed_guests }}/{{ event.expected_guests }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div :class="[
                                        'h-1.5 rounded-full transition-all duration-500 shadow-sm',
                                        index === 0 ? 'bg-gradient-to-r from-blue-500 to-cyan-500' : '',
                                        index === 1 ? 'bg-gradient-to-r from-purple-500 to-pink-500' : '',
                                        index === 2 ? 'bg-gradient-to-r from-orange-500 to-red-500' : ''
                                    ]"
                                        :style="{ width: (event.confirmed_guests / event.expected_guests * 100) + '%' }">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Link :href="route('events.index')" 
                            class="block w-full text-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-lg transition-all font-bold text-xs hover:scale-105">
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
import { ref, onMounted, watch, h } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    stats: Object,
    recentEvents: Array,
    upcomingEvents: Array,
    recentBookings: Array,
    guestStats: Object
})

const participantChart = ref(null)
const chartPeriod = ref('monthly')
let chartInstance = null

const formatNumber = (num) => {
    return new Intl.NumberFormat().format(num || 0)
}

const getEventIcon = (eventType) => {
    const icons = {
        'comedy': () => h('svg', { class: 'w-6 h-6', fill: 'currentColor', viewBox: '0 0 20 20' }, [
            h('path', { d: 'M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z' })
        ]),
        'graphics': () => h('svg', { class: 'w-6 h-6', fill: 'currentColor', viewBox: '0 0 20 20' }, [
            h('path', { 'fill-rule': 'evenodd', d: 'M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z', 'clip-rule': 'evenodd' })
        ]),
        'tech': () => h('svg', { class: 'w-6 h-6', fill: 'currentColor', viewBox: '0 0 20 20' }, [
            h('path', { d: 'M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z' })
        ]),
    }
    return icons[eventType?.toLowerCase()] || (() => h('div'))
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
                    backgroundColor: (context) => {
                        const canvas = context.chart.ctx
                        const gradient = canvas.createLinearGradient(0, 0, 0, 300)
                        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.8)')
                        gradient.addColorStop(1, 'rgba(147, 51, 234, 0.8)')
                        return gradient
                    },
                    borderRadius: 8,
                    barThickness: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
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

onMounted(() => {
    updateChart()
})

watch(chartPeriod, () => {
    updateChart()
})
</script>