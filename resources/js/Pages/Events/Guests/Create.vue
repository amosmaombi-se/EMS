<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <Link :href="route('events.guests.index', event.id)" 
                              class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors group">
                            <svg class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Guests
                        </Link>
                        <div class="h-4 w-px bg-gray-300"></div>
                        <Link :href="route('events.show', event.id)" 
                              class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ event.title }}
                        </Link>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <h1 class="font-bold text-3xl text-gray-900 leading-tight mb-2">Add Guests</h1>
                            <p class="text-gray-600">Quickly add multiple guests to <span class="font-semibold">{{ event.title }}</span></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1.5 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">
                                {{ form.guests.length }} guest{{ form.guests.length !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Quick Add Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Add Multiple Guests</h3>
                        <p class="text-sm text-gray-600 mb-6">Add the same type of guests with shared settings</p>
                        
                        <!-- Guest Type Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Guest Category</label>
                                <select v-model="bulkSettings.category" 
                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                    <option value="family">Family</option>
                                    <option value="friends">Friends</option>
                                    <option value="colleagues">Colleagues</option>
                                    <option value="business">Business</option>
                                    <option value="vip">VIP</option>
                                    <option value="sponsors">Sponsors</option>
                                    <option value="media">Media</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Guest Type</label>
                                <select v-model="bulkSettings.guest_type" 
                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                    <option value="primary">Primary Guest</option>
                                    <option value="plus_one">Plus One</option>
                                    <option value="child">Child</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="staff">Staff</option>
                                    <option value="speaker">Speaker</option>
                                    <option value="performer">Performer</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">RSVP Status</label>
                                <select v-model="bulkSettings.rsvp_status" 
                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                    <option value="pending">Pending</option>
                                    <option value="attending">Attending</option>
                                    <option value="not_attending">Not Attending</option>
                                    <option value="maybe">Maybe</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Bulk Options -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="bulk_is_vip" 
                                       v-model="bulkSettings.is_vip"
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="bulk_is_vip" class="ml-2 block text-sm text-gray-700">
                                    Mark as VIP
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="bulk_plus_one_allowed" 
                                       v-model="bulkSettings.plus_one_allowed"
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="bulk_plus_one_allowed" class="ml-2 block text-sm text-gray-700">
                                    Allow Plus One
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                <select v-model="bulkSettings.language_preference" 
                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                    <option value="en">English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <option value="de">German</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Plus Ones Count</label>
                                <input type="number" 
                                       v-model="bulkSettings.plus_ones"
                                       min="0" 
                                       max="10"
                                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <!-- Add Multiple Form -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Add Guest(s)</h4>
                            <div class="space-y-3">
                                <!-- Guest Entry Template -->
                                <div v-for="(guest, index) in tempGuests" :key="index" 
                                     class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div>
                                        <input type="text" 
                                               v-model="guest.first_name"
                                               placeholder="First name"
                                               class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <input type="text" 
                                               v-model="guest.last_name"
                                               placeholder="Last name"
                                               class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <input type="email" 
                                               v-model="guest.email"
                                               placeholder="Email (optional)"
                                               class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="text" 
                                               v-model="guest.phone"
                                               placeholder="Phone (optional)"
                                               class="flex-1 rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                        <button v-if="index > 0" 
                                                @click="removeTempGuest(index)"
                                                type="button"
                                                class="p-2 text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Add More Button -->
                                <button @click="addTempGuest" 
                                        type="button"
                                        class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add another guest
                                </button>
                            </div>
                        </div>
                        
                        <!-- Add to List Button -->
                        <button @click="addGuestsToList" 
                                type="button"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add to Guest List
                        </button>
                    </div>
                </div>

                <!-- Guest List Preview -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6" v-if="form.guests.length > 0">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Guest List Preview</h3>
                            <span class="px-3 py-1.5 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">
                                {{ form.guests.length }} guest{{ form.guests.length !== 1 ? 's' : '' }} ready
                            </span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RSVP</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VIP</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(guest, index) in form.guests" :key="index" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ guest.first_name }} {{ guest.last_name }}
                                            </div>
                                            <div v-if="guest.phone" class="text-xs text-gray-500">
                                                {{ guest.phone }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ guest.email || 'No email' }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                                  :class="getCategoryClass(guest.category)">
                                                {{ guest.category }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                                  :class="getRsvpStatusClass(guest.rsvp_status)">
                                                {{ guest.rsvp_status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span v-if="guest.is_vip" 
                                                  class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                VIP
                                            </span>
                                            <span v-else class="text-xs text-gray-500">-</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <button @click="editGuest(index)" 
                                                    class="text-indigo-600 hover:text-indigo-900 text-sm mr-3">
                                                Edit
                                            </button>
                                            <button @click="removeGuest(index)" 
                                                    class="text-red-600 hover:text-red-900 text-sm">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Guest Edit Modal -->
                <Modal :show="showEditModal" @close="showEditModal = false" max-width="lg">
                    <div class="p-6" v-if="editingGuest !== null">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Edit Guest</h2>
                            <button @click="showEditModal = false" 
                                    class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" 
                                       v-model="editingGuest.first_name"
                                       class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" 
                                       v-model="editingGuest.last_name"
                                       class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" 
                                       v-model="editingGuest.email"
                                       class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="tel" 
                                       v-model="editingGuest.phone"
                                       class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select v-model="editingGuest.category" 
                                        class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    <option value="family">Family</option>
                                    <option value="friends">Friends</option>
                                    <option value="colleagues">Colleagues</option>
                                    <option value="business">Business</option>
                                    <option value="vip">VIP</option>
                                    <option value="sponsors">Sponsors</option>
                                    <option value="media">Media</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">RSVP Status</label>
                                <select v-model="editingGuest.rsvp_status" 
                                        class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    <option value="pending">Pending</option>
                                    <option value="attending">Attending</option>
                                    <option value="not_attending">Not Attending</option>
                                    <option value="maybe">Maybe</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="edit_is_vip" 
                                               v-model="editingGuest.is_vip"
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="edit_is_vip" class="ml-2 block text-sm text-gray-700">
                                            VIP Guest
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="edit_plus_one_allowed" 
                                               v-model="editingGuest.plus_one_allowed"
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="edit_plus_one_allowed" class="ml-2 block text-sm text-gray-700">
                                            Allow Plus One
                                        </label>
                                    </div>
                                    <div v-if="editingGuest.plus_one_allowed" class="flex items-center">
                                        <label class="text-sm text-gray-700 mr-2">Count:</label>
                                        <input type="number" 
                                               v-model="editingGuest.plus_ones"
                                               min="0" 
                                               max="10"
                                               class="w-20 rounded-lg border border-gray-300 py-1 px-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button @click="showEditModal = false" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Cancel
                            </button>
                            <button @click="saveGuestEdit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </Modal>

                <!-- Bulk Actions -->
                <div v-if="form.guests.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Finalize Guest List</h3>
                    <div class="space-y-4">
                        <!-- Invitation Settings -->
                        <div>
                            <div class="flex items-center mb-4">
                                <input type="checkbox" 
                                       id="send_invitations" 
                                       v-model="form.send_invitations"
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="send_invitations" class="ml-2 block text-sm font-medium text-gray-900">
                                    Send invitations to all guests with email addresses
                                </label>
                            </div>
                            
                            <div v-if="form.send_invitations" class="ml-6 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600 mb-3">
                                    Invitations will be sent to {{ guestsWithEmail }} guest{{ guestsWithEmail !== 1 ? 's' : '' }} with email addresses.
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Invitation Method</label>
                                        <select v-model="form.invitation_method" 
                                                class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                            <option value="email">Email</option>
                                            <option value="sms">SMS</option>
                                            <option value="whatsapp">WhatsApp</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                            <textarea v-model="form.notes"
                                      rows="3"
                                      class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                      placeholder="Any notes that apply to all guests..."></textarea>
                        </div>
                        
                        <!-- Submit Actions -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button @click="clearAllGuests" 
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-lg hover:bg-red-50">
                                Clear All Guests
                            </button>
                            <button @click="submit" 
                                    :disabled="form.processing || isSubmitting"
                                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg v-if="form.processing || isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ (form.processing || isSubmitting) ? 'Saving...' : `Save ${form.guests.length} Guest${form.guests.length !== 1 ? 's' : ''}` }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
    event: Object
})

const showEditModal = ref(false)
const editingIndex = ref(null)
const editingGuest = ref(null)
const isSubmitting = ref(false)

const bulkSettings = ref({
    category: 'friends',
    guest_type: 'primary',
    rsvp_status: 'pending',
    is_vip: false,
    plus_one_allowed: false,
    plus_ones: 0,
    language_preference: 'en'
})

const tempGuests = ref([
    { first_name: '', last_name: '', email: '', phone: '' }
])

const form = ref({
    guests: [],
    send_invitations: false,
    invitation_method: 'email',
    notes: ''
})

const guestsWithEmail = computed(() => {
    return form.value.guests.filter(guest => guest.email && guest.email.trim()).length
})

const addTempGuest = () => {
    tempGuests.value.push({ first_name: '', last_name: '', email: '', phone: '' })
}

const removeTempGuest = async (index) => {
    if (tempGuests.value.length <= 1) {
        Swal.fire({
            title: 'Cannot Remove',
            text: 'You must have at least one guest entry.',
            icon: 'warning',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK'
        })
        return
    }

    const result = await Swal.fire({
        title: 'Remove Guest Entry?',
        text: 'Are you sure you want to remove this guest entry?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel'
    })

    if (result.isConfirmed) {
        tempGuests.value.splice(index, 1)
        Swal.fire({
            title: 'Removed!',
            text: 'Guest entry has been removed.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        })
    }
}

const addGuestsToList = async () => {
    const validGuests = tempGuests.value.filter(guest => 
        guest.first_name.trim() && guest.last_name.trim()
    )
    
    if (validGuests.length === 0) {
        Swal.fire({
            title: 'No Guests Added',
            text: 'Please add at least one guest with first and last name.',
            icon: 'warning',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK'
        })
        return
    }

    const guestsWithoutNames = tempGuests.value.filter(guest => 
        !guest.first_name.trim() || !guest.last_name.trim()
    ).length

    if (guestsWithoutNames > 0) {
        const result = await Swal.fire({
            title: 'Incomplete Guests',
            text: `${guestsWithoutNames} guest(s) are missing names. Only complete guests will be added. Continue?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Add Complete Guests',
            cancelButtonText: 'Cancel'
        })

        if (!result.isConfirmed) {
            return
        }
    }

    validGuests.forEach(guest => {
        form.value.guests.push({
            first_name: guest.first_name.trim(),
            last_name: guest.last_name.trim(),
            email: guest.email?.trim() || '',
            phone: guest.phone?.trim() || '',
            category: bulkSettings.value.category,
            guest_type: bulkSettings.value.guest_type,
            rsvp_status: bulkSettings.value.rsvp_status,
            is_vip: bulkSettings.value.is_vip,
            plus_one_allowed: bulkSettings.value.plus_one_allowed,
            plus_ones: bulkSettings.value.plus_one_allowed ? bulkSettings.value.plus_ones : 0,
            language_preference: bulkSettings.value.language_preference,
            dietary_preference: '',
            allergies: '',
            special_requirements: '',
            accessibility_needs: '',
            accommodation_needs: '',
            transportation_needs: '',
            notes: ''
        })
    })
    
    // Show success message
    Swal.fire({
        title: 'Guests Added!',
        text: `${validGuests.length} guest(s) added to the list.`,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    })

    // Reset temp guests
    tempGuests.value = [
        { first_name: '', last_name: '', email: '', phone: '' }
    ]
}

const removeGuest = async (index) => {
    const guestName = `${form.value.guests[index].first_name} ${form.value.guests[index].last_name}`
    
    const result = await Swal.fire({
        title: 'Remove Guest?',
        html: `Are you sure you want to remove <strong>${guestName}</strong> from the list?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel'
    })

    if (result.isConfirmed) {
        form.value.guests.splice(index, 1)
        
        Swal.fire({
            title: 'Removed!',
            text: `${guestName} has been removed from the list.`,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        })
    }
}

const editGuest = (index) => {
    editingIndex.value = index
    editingGuest.value = { ...form.value.guests[index] }
    showEditModal.value = true
}

const saveGuestEdit = async () => {
    if (!editingGuest.value.first_name.trim() || !editingGuest.value.last_name.trim()) {
        Swal.fire({
            title: 'Missing Information',
            text: 'Please enter both first and last name.',
            icon: 'warning',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK'
        })
        return
    }

    if (editingIndex.value !== null && editingGuest.value) {
        form.value.guests[editingIndex.value] = editingGuest.value
        showEditModal.value = false
        editingIndex.value = null
        editingGuest.value = null

        Swal.fire({
            title: 'Updated!',
            text: 'Guest information has been updated.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        })
    }
}

const clearAllGuests = async () => {
    if (form.value.guests.length === 0) return

    const result = await Swal.fire({
        title: 'Clear All Guests?',
        html: `Are you sure you want to remove all <strong>${form.value.guests.length}</strong> guests from the list?<br><span class="text-sm text-gray-500">This action cannot be undone.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, clear all!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    })

    if (result.isConfirmed) {
        form.value.guests = []
        
        Swal.fire({
            title: 'Cleared!',
            text: 'All guests have been removed from the list.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        })
    }
}

const submit = async () => {
    if (form.value.guests.length === 0) {
        Swal.fire({
            title: 'No Guests',
            text: 'Please add at least one guest before saving.',
            icon: 'warning',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK'
        })
        return
    }

    const result = await Swal.fire({
        title: 'Ready to Save?',
        html: `
            <div class="text-left">
                <p>You are about to add <strong>${form.value.guests.length} guest(s)</strong> to the event.</p>
                <div class="mt-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Category: ${form.value.guests[0].category}</span>
                    </div>
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>RSVP Status: ${form.value.guests[0].rsvp_status}</span>
                    </div>
                    ${form.value.send_invitations ? `
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <span>Invitations will be sent via ${form.value.invitation_method}</span>
                    </div>
                    ` : ''}
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, save ${form.value.guests.length} guest(s)`,
        cancelButtonText: 'Cancel'
    })

    if (!result.isConfirmed) {
        return
    }

    isSubmitting.value = true

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        
        const response = await fetch(route('events.guests.bulk-store', props.event.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                guests: form.value.guests.map(guest => ({
                    first_name: guest.first_name,
                    last_name: guest.last_name,
                    email: guest.email,
                    phone: guest.phone,
                    category: guest.category,
                    guest_type: guest.guest_type,
                    rsvp_status: guest.rsvp_status,
                    is_vip: guest.is_vip || false,
                    plus_one_allowed: guest.plus_one_allowed || false,
                    plus_ones: guest.plus_ones || 0,
                    language_preference: guest.language_preference,
                    dietary_preference: guest.dietary_preference || '',
                    allergies: guest.allergies || '',
                    special_requirements: guest.special_requirements || '',
                    accessibility_needs: guest.accessibility_needs || '',
                    accommodation_needs: guest.accommodation_needs || '',
                    transportation_needs: guest.transportation_needs || '',
                    notes: guest.notes || ''
                })),
                send_invitations: form.value.send_invitations || false,
                invitation_method: form.value.invitation_method || 'email',
                notes: form.value.notes || ''
            })
        })

        const data = await response.json()

        if (response.ok && data.success) {
            // Create detailed success message
            let successDetails = `
                <div class="text-left space-y-2">
                    <div class="flex items-center gap-2 text-green-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">${data.created_count} guest(s) created successfully</span>
                    </div>`

            if (data.skipped_count > 0) {
                successDetails += `
                    <div class="flex items-center gap-2 text-yellow-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">${data.skipped_count} guest(s) skipped (duplicate emails)</span>
                    </div>`
            }

            if (data.invitations_sent > 0) {
                successDetails += `
                    <div class="flex items-center gap-2 text-blue-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <span class="font-medium">${data.invitations_sent} invitation(s) sent</span>
                    </div>`
            }

            successDetails += `</div>`

            await Swal.fire({
                title: 'Success!',
                html: successDetails,
                icon: 'success',
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'View Guests'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = route('events.guests.index', props.event.id)
                }
            })
            
            // Reset form
            form.value.guests = []
            form.value.send_invitations = false
            form.value.invitation_method = 'email'
            form.value.notes = ''
        } else {
            let errorMessage = 'Failed to save guests.'
            
            if (data.errors) {
                errorMessage += '<div class="text-left mt-3"><p class="font-medium text-gray-900 mb-2">Please fix the following errors:</p><ul class="text-sm text-gray-600 space-y-1">'
                for (const field in data.errors) {
                    const fieldName = field.replace('guests.', '').replace(/\.[0-9]+\./, ' guest #').replace(/\./g, ' ')
                    data.errors[field].forEach(error => {
                        errorMessage += `<li class="text-red-600">• ${fieldName}: ${error}</li>`
                    })
                }
                errorMessage += '</ul></div>'
            } else if (data.message) {
                errorMessage += `<br><span class="text-sm text-gray-600">${data.message}</span>`
            }
            
            await Swal.fire({
                title: 'Error!',
                html: errorMessage,
                icon: 'error',
                confirmButtonColor: '#ef4444'
            })
        }
    } catch (error) {
        console.error('Submission error:', error)
        await Swal.fire({
            title: 'Network Error!',
            text: 'Failed to connect to the server. Please check your connection and try again.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        })
    } finally {
        isSubmitting.value = false
    }
}

// Helper functions
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
</script>