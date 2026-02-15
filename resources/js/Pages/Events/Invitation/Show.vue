<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 py-6 px-4 sm:py-12 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Invitation Card -->
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden">
                <!-- Header Section -->
                <div class="relative h-48 sm:h-64 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 overflow-hidden">
                    <div class="absolute inset-0 opacity-20">
                        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-32 -translate-y-32"></div>
                        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-48 translate-y-48"></div>
                    </div>
                    
                    <div class="relative h-full flex flex-col items-center justify-center text-white p-4 sm:p-8">
                        <div class="w-16 h-16 sm:w-24 sm:h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3 sm:mb-4 backdrop-blur-sm">
                            <span class="text-3xl sm:text-5xl">🎉</span>
                        </div>
                        <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold text-center mb-1 sm:mb-2">You're Invited!</h1>
                        <p class="text-base sm:text-xl text-center opacity-90">{{ event.title }}</p>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-4 sm:p-8 md:p-12">
                    <!-- Greeting -->
                    <div class="text-center mb-6 sm:mb-8">
                        <p class="text-xl sm:text-3xl font-semibold text-gray-800 mb-2">
                            Dear {{ guest.first_name }} {{ guest.last_name }},
                        </p>
                        <p class="text-sm sm:text-lg text-gray-600">
                            We would be honored to have you join us for this special occasion.
                        </p>
                    </div>

                    <!-- QR Code Section - PRIORITIZED -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl sm:rounded-2xl p-6 sm:p-8 text-center border-2 border-indigo-300 mb-6 sm:mb-8 shadow-lg">
                        <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 bg-indigo-600 rounded-full mb-3 sm:mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2 sm:mb-4">Your Digital Pass</h3>
                        <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 px-2">Show this QR code at the event entrance for quick check-in</p>
                        
                        <!-- QR Code Display - Handle both SVG and Base64 -->
                        <div class="inline-block bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-xl mx-auto">
                            <!-- If QR code is base64 data URI -->
                            <img 
                                v-if="qrCode.startsWith('data:image')" 
                                :src="qrCode" 
                                alt="Event QR Code" 
                                class="w-56 h-56 sm:w-72 sm:h-72 md:w-80 md:h-80 mx-auto"
                            />
                            <!-- If QR code is raw SVG -->
                            <div 
                                v-else 
                                v-html="qrCode" 
                                class="w-56 h-56 sm:w-72 sm:h-72 md:w-80 md:h-80 mx-auto flex items-center justify-center"
                            ></div>
                        </div>
                        
                        <p class="text-xs sm:text-sm text-gray-500 mt-4 sm:mt-6">
                            Guest ID: <span class="font-mono font-semibold text-sm sm:text-base">#{{ guest.id }}</span>
                        </p>
                        
                        <!-- Download Button -->
                        <button
                            @click="downloadQrCode"
                            class="mt-4 sm:mt-6 w-full sm:w-auto inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-4 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl text-sm sm:text-base"
                        >
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download QR Code
                        </button>
                    </div>

                    <div class="bg-white border-2 border-gray-200 rounded-xl sm:rounded-2xl p-4 sm:p-8 mb-6 sm:mb-8">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">Will you be attending?</h2>
                        
                        <div v-if="!rsvpSubmitted" class="space-y-4">
                            <!-- RSVP Status Selection - Stacked on mobile -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                                <button
                                    @click="selectedRsvpStatus = 'attending'"
                                    :class="[
                                        'p-4 sm:p-6 rounded-xl border-2 transition-all duration-200 flex flex-row sm:flex-col items-center justify-center',
                                        selectedRsvpStatus === 'attending'
                                            ? 'border-green-500 bg-green-50 shadow-lg transform scale-105'
                                            : 'border-gray-300 hover:border-green-300 hover:bg-green-50'
                                    ]"
                                >
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mb-0 sm:mb-2 mr-3 sm:mr-0" :class="selectedRsvpStatus === 'attending' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-semibold text-base sm:text-lg" :class="selectedRsvpStatus === 'attending' ? 'text-green-700' : 'text-gray-700'">
                                        Yes, I'll be there!
                                    </span>
                                </button>

                                <button
                                    @click="selectedRsvpStatus = 'not_attending'"
                                    :class="[
                                        'p-4 sm:p-6 rounded-xl border-2 transition-all duration-200 flex flex-row sm:flex-col items-center justify-center',
                                        selectedRsvpStatus === 'not_attending'
                                            ? 'border-red-500 bg-red-50 shadow-lg transform scale-105'
                                            : 'border-gray-300 hover:border-red-300 hover:bg-red-50'
                                    ]"
                                >
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mb-0 sm:mb-2 mr-3 sm:mr-0" :class="selectedRsvpStatus === 'not_attending' ? 'text-red-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-semibold text-base sm:text-lg" :class="selectedRsvpStatus === 'not_attending' ? 'text-red-700' : 'text-gray-700'">
                                        Can't make it
                                    </span>
                                </button>

                                <button
                                    @click="selectedRsvpStatus = 'maybe'"
                                    :class="[
                                        'p-4 sm:p-6 rounded-xl border-2 transition-all duration-200 flex flex-row sm:flex-col items-center justify-center',
                                        selectedRsvpStatus === 'maybe'
                                            ? 'border-blue-500 bg-blue-50 shadow-lg transform scale-105'
                                            : 'border-gray-300 hover:border-blue-300 hover:bg-blue-50'
                                    ]"
                                >
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mb-0 sm:mb-2 mr-3 sm:mr-0" :class="selectedRsvpStatus === 'maybe' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-semibold text-base sm:text-lg" :class="selectedRsvpStatus === 'maybe' ? 'text-blue-700' : 'text-gray-700'">
                                        Maybe
                                    </span>
                                </button>
                            </div>

                            <!-- Plus Ones Input -->
                            <div v-if="guest.plus_ones > 0 && selectedRsvpStatus === 'attending'" class="mt-4 sm:mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    How many guests will you bring? (Maximum: {{ guest.plus_ones }})
                                </label>
                                <input
                                    v-model.number="numberOfPlusOnes"
                                    type="number"
                                    :min="0"
                                    :max="guest.plus_ones"
                                    class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>

                            <!-- Submit Button -->
                            <button
                                @click="submitRsvp"
                                :disabled="!selectedRsvpStatus || submitting"
                                :class="[
                                    'w-full py-3 sm:py-4 rounded-xl font-semibold text-base sm:text-lg transition-all duration-200',
                                    selectedRsvpStatus && !submitting
                                        ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1'
                                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                ]"
                            >
                                <span v-if="submitting" class="flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Submitting...
                                </span>
                                <span v-else>Confirm RSVP</span>
                            </button>
                        </div>

                        <!-- RSVP Confirmation -->
                        <div v-else class="text-center py-6 sm:py-8">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Thank You!</h3>
                            <p class="text-base sm:text-lg text-gray-600">Your RSVP has been recorded.</p>
                            <p v-if="guest.rsvp_status === 'attending'" class="text-sm sm:text-md text-gray-500 mt-2">
                                We look forward to seeing you at the event!
                            </p>
                        </div>
                    </div>

                    <!-- Event Details Card - Collapsed by default on mobile -->
                    <details class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl sm:rounded-2xl border border-gray-200 mb-6 sm:mb-8" open>
                        <summary class="p-4 sm:p-6 cursor-pointer font-semibold text-lg sm:text-xl text-gray-800 flex items-center justify-between">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Event Details
                            </span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        
                        <div class="px-4 pb-4 sm:px-6 sm:pb-6 space-y-3 sm:space-y-4">
                            <!-- Date & Time -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-3 sm:mr-4">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500">Date & Time</p>
                                    <p class="text-base sm:text-lg font-semibold text-gray-800">
                                        {{ formatDate(event.event_date) }}
                                    </p>
                                    <p class="text-sm sm:text-md text-gray-600">
                                        {{ formatTime(event.start_time) }}
                                        <span v-if="event.end_time"> - {{ formatTime(event.end_time) }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Venue -->
                            <div v-if="event.venue_name" class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-3 sm:mr-4">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500">Venue</p>
                                    <p class="text-base sm:text-lg font-semibold text-gray-800">{{ event.venue_name }}</p>
                                    <p v-if="event.venue_address" class="text-sm text-gray-600">{{ event.venue_address }}</p>
                                    <p v-if="event.city || event.state" class="text-sm text-gray-600">
                                        {{ event.city }}<span v-if="event.city && event.state">, </span>{{ event.state }}
                                    </p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div v-if="event.description" class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-pink-100 rounded-lg flex items-center justify-center mr-3 sm:mr-4">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500">About This Event</p>
                                    <p class="text-sm sm:text-md text-gray-700 leading-relaxed">{{ event.description }}</p>
                                </div>
                            </div>

                            <!-- Plus Ones -->
                            <div v-if="guest.plus_ones > 0" class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center mr-3 sm:mr-4">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs sm:text-sm font-medium text-gray-500">Plus Ones</p>
                                    <p class="text-base sm:text-lg font-semibold text-gray-800">
                                        You may bring {{ guest.plus_ones }} guest{{ guest.plus_ones > 1 ? 's' : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </details>
                    <!-- Rest of the component remains the same... -->
                    <!-- RSVP Section, Event Details, Footer -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
    event: Object,
    guest: Object,
    qrCode: String,
})

const selectedRsvpStatus = ref(props.guest.rsvp_status || null)
const numberOfPlusOnes = ref(props.guest.plus_ones || 0)
const submitting = ref(false)
const rsvpSubmitted = ref(props.guest.rsvp_status !== 'pending')

const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatTime = (time) => {
    if (!time) return ''
    const timeParts = time.split(':')
    if (timeParts.length >= 2) {
        const hours = parseInt(timeParts[0])
        const minutes = timeParts[1]
        const period = hours >= 12 ? 'PM' : 'AM'
        const displayHours = hours % 12 || 12
        return `${displayHours}:${minutes} ${period}`
    }
    return time
}

const submitRsvp = async () => {
    if (!selectedRsvpStatus.value) {
        Swal.fire({
            title: 'Please select an option',
            text: 'Please let us know if you will be attending',
            icon: 'info',
            confirmButtonColor: '#4F46E5'
        })
        return
    }

    submitting.value = true

    router.post(route('events.rsvp.update', [props.event.id, props.guest.id]), {
        rsvp_status: selectedRsvpStatus.value,
        plus_ones: selectedRsvpStatus.value === 'attending' ? numberOfPlusOnes.value : 0
    }, {
        onSuccess: () => {
            rsvpSubmitted.value = true
            submitting.value = false
            
            const message = selectedRsvpStatus.value === 'attending' 
                ? 'We look forward to seeing you!'
                : 'Thank you for letting us know.'

            Swal.fire({
                title: 'RSVP Confirmed!',
                text: message,
                icon: 'success',
                confirmButtonColor: '#4F46E5',
                timer: 3000
            })
        },
        onError: () => {
            submitting.value = false
            Swal.fire({
                title: 'Error',
                text: 'Failed to submit RSVP. Please try again.',
                icon: 'error',
                confirmButtonColor: '#DC2626'
            })
        }
    })
}

const downloadQrCode = () => {
    // Check if it's a data URI
    if (props.qrCode.startsWith('data:image')) {
        const link = document.createElement('a')
        link.href = props.qrCode
        link.download = `event-qr-code-${props.guest.id}.svg`
        link.click()
    } else {
        // Raw SVG string
        const svgBlob = new Blob([props.qrCode], { type: 'image/svg+xml;charset=utf-8' })
        const url = URL.createObjectURL(svgBlob)
        const link = document.createElement('a')
        link.href = url
        link.download = `event-qr-code-${props.guest.id}.svg`
        link.click()
        URL.revokeObjectURL(url)
    }
}


</script>