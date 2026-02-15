<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-gray-900">Edit Event</h2>
                <Link :href="route('events.show', event.id)" 
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
                                 class="cursor-pointer border rounded-lg p-3 text-center hover:bg-gray-50"
                                 :class="form.event_type_id === type.id 
                                     ? 'border-indigo-500 bg-indigo-50' 
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

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="form.status" 
                                        class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    <option value="draft">Draft</option>
                                    <option value="planning">Planning</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <p v-if="form.errors.status" class="text-red-600 text-xs mt-1">
                                    {{ form.errors.status }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time -->
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Date & Time</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Event Date *</label>
                                    <input v-model="form.event_date" 
                                           type="date" 
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    <p v-if="form.errors.event_date" class="text-red-600 text-xs mt-1">
                                        {{ form.errors.event_date }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">End Date (Optional)</label>
                                    <input v-model="form.event_end_date" 
                                           type="date" 
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                                    <input v-model="form.start_time" 
                                           type="time" 
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                                    <input v-model="form.end_time" 
                                           type="time" 
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
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

                    <!-- Additional Notes -->
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Additional Notes</h3>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea v-model="form.notes" 
                                      rows="2" 
                                      class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                      placeholder="Additional notes or requirements"></textarea>
                            <p v-if="form.errors.notes" class="text-red-600 text-xs mt-1">
                                {{ form.errors.notes }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-xs text-gray-600">
                            * Required fields
                        </p>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <Link :href="route('events.show', event.id)" 
                                  class="flex-1 sm:flex-initial px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm text-center">
                                Cancel
                            </Link>
                            <button type="submit" 
                                    :disabled="form.processing"
                                    class="flex-1 sm:flex-initial px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50 text-sm font-medium">
                                <span v-if="form.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Updating...
                                </span>
                                <span v-else>Update Event</span>
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
import { Link, useForm, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { onMounted } from 'vue'

const props = defineProps({
    event: Object,
    eventTypes: Array,
    flash: Object
})

// Format date for input fields (YYYY-MM-DD)
const formatDateForInput = (date) => {
    if (!date) return ''
    const d = new Date(date)
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    return `${year}-${month}-${day}`
}

const form = useForm({
    event_type_id: props.event.event_type_id,
    title: props.event.title || '',
    description: props.event.description || '',
    status: props.event.status || 'draft',
    event_date: formatDateForInput(props.event.event_date),
    event_end_date: formatDateForInput(props.event.event_end_date),
    start_time: props.event.start_time || '',
    end_time: props.event.end_time || '',
    venue_name: props.event.venue_name || '',
    venue_address: props.event.venue_address || '',
    city: props.event.city || '',
    expected_guests: props.event.expected_guests || '',
    estimated_budget: props.event.estimated_budget || '',
    is_public: props.event.is_public || false,
    notes: props.event.notes || ''
})

const submit = () => {
    // Validate required fields first
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
            text: 'Please fill in all required fields.',
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
        title: 'Update Event?',
        text: "Are you sure you want to update this event?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'text-sm',
            confirmButton: 'text-xs py-1.5 px-3',
            cancelButton: 'text-xs py-1.5 px-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Updating...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
                customClass: {
                    popup: 'text-sm'
                }
            })

            form.put(route('events.update', props.event.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Event has been updated successfully.',
                        confirmButtonText: 'OK',
                        timer: 2000,
                        customClass: {
                            popup: 'text-sm',
                            confirmButton: 'text-xs py-1.5 px-3'
                        }
                    })
                },
                onError: (errors) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        html: Object.values(errors).map(error => 
                            `<div class="text-sm mb-1">${error}</div>`
                        ).join(''),
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'text-sm',
                            confirmButton: 'text-xs py-1.5 px-3'
                        }
                    })
                },
                onFinish: () => {
                    Swal.close()
                }
            })
        }
    })
}

// Show flash messages from backend
onMounted(() => {
    if (props.flash?.success) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: props.flash.success,
            confirmButtonText: 'OK',
            timer: 2000,
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1.5 px-3'
            }
        })
    }
    
    if (props.flash?.error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: props.flash.error,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1.5 px-3'
            }
        })
    }
})
</script>