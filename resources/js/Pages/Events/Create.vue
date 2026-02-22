<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-gray-900">Create Event</h2>
                <Link :href="route('events.index')" 
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </Link>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto px-2">
                <form @submit.prevent="submit" class="space-y-4">
                    
                    <!-- Event Type Selection -->
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Event Type *</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <div v-for="type in eventTypes" 
                                 :key="type.id"
                                 @click="form.event_type_id = type.id"
                                 class="cursor-pointer border rounded-lg p-3 text-center hover:bg-gray-50 transition-all"
                                 :class="form.event_type_id === type.id 
                                     ? 'border-indigo-500 bg-indigo-50 shadow-sm' 
                                     : 'border-gray-200'">
                                <div class="text-2xl mb-1">{{ type.icon }}</div>
                                <div class="text-xs font-medium text-gray-800">{{ type.name }}</div>
                            </div>
                        </div>
                        <p v-if="form.errors.event_type_id" class="text-red-600 text-xs mt-2">
                            {{ form.errors.event_type_id }}
                        </p>
                    </div>

                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Event Details</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Title *</label>
                                    <input v-model="form.title" 
                                           type="text" 
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                           placeholder="Event title">
                                    <p v-if="form.errors.title" class="text-red-600 text-xs mt-1">
                                        {{ form.errors.title }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Expected Guests *</label>
                                    <input v-model="form.expected_guests" 
                                           type="number" 
                                           min="1"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                           placeholder="100">
                                    <p v-if="form.errors.expected_guests" class="text-red-600 text-xs mt-1">
                                        {{ form.errors.expected_guests }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                                <textarea v-model="form.description" 
                                          rows="2" 
                                          class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                          placeholder="Event description"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time with Validation -->
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Date & Time</h3>
                        <div class="space-y-3">
                            <!-- Date Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Event Date *</label>
                                    <input v-model="form.event_date" 
                                           type="date" 
                                           :min="minDate"
                                           @change="validateDates"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                           required>
                                    <p v-if="form.errors.event_date" class="text-red-600 text-xs mt-1">
                                        {{ form.errors.event_date }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">End Date (Optional)</label>
                                    <input v-model="form.event_end_date" 
                                           type="date" 
                                           :min="form.event_date || minDate"
                                           @change="validateDates"
                                           :disabled="!form.event_date"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    <p v-if="dateErrors.event_end_date" class="text-orange-600 text-xs mt-1">
                                        {{ dateErrors.event_end_date }}
                                    </p>
                                    <p v-if="form.errors.event_end_date" class="text-red-600 text-xs mt-1">
                                        {{ form.errors.event_end_date }}
                                    </p>
                                </div>
                            </div>

                            <!-- Time Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                                    <input v-model="form.start_time" 
                                           type="time" 
                                           @change="validateTimes"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    <p v-if="form.errors.start_time" class="text-red-600 text-xs mt-1">
                                        {{ form.errors.start_time }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                                    <input v-model="form.end_time" 
                                           type="time" 
                                           @change="validateTimes"
                                           :disabled="!form.start_time"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    <p v-if="timeErrors.end_time" class="text-orange-600 text-xs mt-1">
                                        {{ timeErrors.end_time }}
                                    </p>
                                    <p v-if="form.errors.end_time" class="text-red-600 text-xs mt-1">
                                        {{ form.errors.end_time }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Validation Warning Box -->
                            <div v-if="timeErrors.end_time || dateErrors.event_end_date" 
                                 class="bg-orange-50 border border-orange-200 rounded-lg p-3 flex items-start gap-2">
                                <svg class="w-4 h-4 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div class="text-xs text-orange-800">
                                    <p class="font-semibold mb-1">Date/Time Validation Issues:</p>
                                    <ul class="space-y-1">
                                        <li v-if="timeErrors.end_time">• {{ timeErrors.end_time }}</li>
                                        <li v-if="dateErrors.event_end_date">• {{ dateErrors.event_end_date }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Location</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Venue Name</label>
                                    <input v-model="form.venue_name" 
                                           type="text" 
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                           placeholder="Venue name">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">City *</label>
                                    <input v-model="form.city" 
                                           type="text" 
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                           placeholder="City">
                                    <p v-if="form.errors.city" class="text-red-600 text-xs mt-1">
                                        {{ form.errors.city }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Venue Address</label>
                                <textarea v-model="form.venue_address" 
                                          rows="2" 
                                          class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                          placeholder="Full address"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Budget & Settings -->
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Budget & Settings</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Estimated Budget (Optional)</label>
                                <input v-model="form.estimated_budget" 
                                       type="number" 
                                       step="0.01"
                                       min="0"
                                       class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                       placeholder="Amount in TZS">
                                <p v-if="form.errors.estimated_budget" class="text-red-600 text-xs mt-1">
                                    {{ form.errors.estimated_budget }}
                                </p>
                            </div>

                            <div class="flex items-center pt-2 border-t border-gray-100">
                                <input v-model="form.is_public" 
                                       type="checkbox" 
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label class="ml-2 text-xs text-gray-700">
                                    Make this event public
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-xs text-gray-600">
                            * Required fields
                        </p>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <Link :href="route('events.index')" 
                                  class="flex-1 sm:flex-initial px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm text-center">
                                Cancel
                            </Link>
                            <button type="submit" 
                                    :disabled="form.processing || hasValidationErrors"
                                    class="flex-1 sm:flex-initial px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium">
                                <span v-if="form.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                                <span v-else>Create Event</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { onMounted, watch, ref, computed } from 'vue';

const props = defineProps({
    eventTypes: Array
})

const page = usePage();
const timeErrors = ref({});
const dateErrors = ref({});


const minDate = computed(() => {
    const today = new Date();
    return today.toISOString().split('T')[0];
});


const hasValidationErrors = computed(() => {
    return Object.keys(timeErrors.value).length > 0 || 
           Object.keys(dateErrors.value).length > 0;
});

const form = useForm({
    event_type_id: null,
    title: '',
    description: '',
    event_date: '',
    event_end_date: '',
    start_time: '',
    end_time: '',
    venue_name: '',
    venue_address: '',
    city: '',
    expected_guests: '',
    estimated_budget: '',
    is_public: false
})

const validateDates = () => {
    dateErrors.value = {};
    
    if (form.event_date && form.event_end_date) {
        const startDate = new Date(form.event_date);
        const endDate = new Date(form.event_end_date);
        
        if (endDate < startDate) {
            dateErrors.value.event_end_date = 'End date must be on or after the event start date';
            form.event_end_date = ''; // Clear invalid end date
        }
    }
};


const validateTimes = () => {
    timeErrors.value = {};
    if (form.start_time && form.end_time) {
        const isSameDay = !form.event_end_date || form.event_date === form.event_end_date;
        if (isSameDay) {
            const startTime = form.start_time.split(':').map(Number);
            const endTime = form.end_time.split(':').map(Number);
            
            const startMinutes = startTime[0] * 60 + startTime[1];
            const endMinutes = endTime[0] * 60 + endTime[1];
            
            if (endMinutes <= startMinutes) {
                timeErrors.value.end_time = 'End time must be after start time (for same-day events)';
            }
        }
    }
};


watch([() => form.event_date, () => form.event_end_date], () => {
    if (form.event_date && form.event_end_date && form.event_date !== form.event_end_date) {
        // Multi-day event - times don't need to be validated
        timeErrors.value = {};
    } else if (form.start_time && form.end_time) {
        // Single day - revalidate times
        validateTimes();
    }
});

watch([() => form.start_time, () => form.end_time], () => {
    if (form.start_time && form.end_time) {
        validateTimes();
    }
});


watch(() => form.errors, (errors) => {
    if (Object.keys(errors).length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: Object.values(errors).map(error => 
                `<div class="text-sm">${error}</div>`
            ).join(''),
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1.5 px-3'
            }
        });
    }
});


const submit = () => {
    timeErrors.value = {};
    dateErrors.value = {};
    
    validateDates();
    
    if (form.start_time && form.end_time) {
        validateTimes();
    }
    
    if (hasValidationErrors.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            html: `
                <div class="text-left text-sm">
                    <p class="mb-2">Please fix the following issues:</p>
                    <ul class="list-disc list-inside space-y-1">
                        ${timeErrors.value.end_time ? `<li>${timeErrors.value.end_time}</li>` : ''}
                        ${dateErrors.value.event_end_date ? `<li>${dateErrors.value.event_end_date}</li>` : ''}
                    </ul>
                </div>
            `,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1.5 px-3'
            }
        });
        return;
    }
    
    // Validate required fields
    if (!form.event_type_id) {
        Swal.fire({
            icon: 'warning',
            title: 'Event Type Required',
            text: 'Please select an event type to continue.',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1 px-3'
            }
        });
        return;
    }
    
    if (!form.title || !form.event_date || !form.city || !form.expected_guests) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in all required fields (Event Type, Title, Date, City, Expected Guests).',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1 px-3'
            }
        });
        return;
    }

    // Show confirmation
    Swal.fire({
        title: 'Create Event?',
        text: "Are you ready to create this event?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Create',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'text-sm',
            confirmButton: 'text-xs py-1.5 px-3',
            cancelButton: 'text-xs py-1.5 px-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route('events.store'));
        }
    });
}


onMounted(() => {
    if (page.props.flash.success) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: page.props.flash.success,
            confirmButtonText: 'OK',
            timer: 3000,
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1.5 px-3'
            }
        });
    }
    
    if (page.props.flash.error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: page.props.flash.error,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1.5 px-3'
            }
        });
    }
});
</script>