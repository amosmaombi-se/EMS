<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <Link :href="route('events.guests.index', event.id)"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors group">
                            <svg class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Guest List
                        </Link>
                    </div>
                    <h1 class="font-bold text-2xl text-gray-900">Create Invitation</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ event.title }}</p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-center max-w-2xl mx-auto">
                        <!-- Step 1 -->
                        <div class="flex items-center">
                            <div :class="[
                                'w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-bold text-xs sm:text-sm transition-all',
                                currentStep >= 1 ? 'bg-indigo-600 shadow-md' : 'bg-gray-300'
                            ]">
                                <span v-if="currentStep > 1">✓</span>
                                <span v-else>1</span>
                            </div>
                            <span class="ml-1.5 sm:ml-2 text-xs sm:text-sm font-medium"
                                :class="currentStep >= 1 ? 'text-indigo-600' : 'text-gray-500'">
                                Recipients
                            </span>
                        </div>

                        <div
                            :class="['flex-1 h-0.5 sm:h-1 mx-1.5 sm:mx-3 transition-all', currentStep > 1 ? 'bg-indigo-600' : 'bg-gray-300']">
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-center">
                            <div :class="[
                                'w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-bold text-xs sm:text-sm transition-all',
                                currentStep >= 2 ? 'bg-indigo-600 shadow-md' : 'bg-gray-300'
                            ]">
                                <span v-if="currentStep > 2">✓</span>
                                <span v-else>2</span>
                            </div>
                            <span class="ml-1.5 sm:ml-2 text-xs sm:text-sm font-medium"
                                :class="currentStep >= 2 ? 'text-indigo-600' : 'text-gray-500'">
                                Message
                            </span>
                        </div>

                        <div
                            :class="['flex-1 h-0.5 sm:h-1 mx-1.5 sm:mx-3 transition-all', currentStep > 2 ? 'bg-indigo-600' : 'bg-gray-300']">
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-center">
                            <div :class="[
                                'w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-bold text-xs sm:text-sm transition-all',
                                currentStep >= 3 ? 'bg-indigo-600 shadow-md' : 'bg-gray-300'
                            ]">
                                <span v-if="currentStep > 3">✓</span>
                                <span v-else>3</span>
                            </div>
                            <span class="ml-1.5 sm:ml-2 text-xs sm:text-sm font-medium"
                                :class="currentStep >= 3 ? 'text-indigo-600' : 'text-gray-500'">
                                Send
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Select Recipients -->
                <div v-if="currentStep === 1"
                    class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-4 sm:px-6 py-4">
                        <h2 class="text-lg sm:text-xl font-bold text-white">Select Recipients</h2>
                        <p class="text-indigo-100 text-xs sm:text-sm mt-1">Choose guests to invite to your event</p>
                    </div>

                    <div class="p-4 sm:p-6">
                        <!-- Selected Banner -->
                        <div v-if="selectedGuests.length > 0"
                            class="mb-4 p-3 sm:p-4 bg-indigo-50 border border-indigo-200 rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">{{ selectedGuests.length }} Guest{{
                                    selectedGuests.length !== 1 ? 's' : '' }} Selected</span>
                            </div>
                            <button @click="selectedGuests = []"
                                class="text-xs font-medium text-red-600 hover:text-red-700 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                Clear All
                            </button>
                        </div>

                        <!-- Search and Filter (if needed) -->
                        <div class="mb-4 flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" placeholder="Search guests..."
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>
                            <select
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                <option value="all">All Guests</option>
                                <option value="vip">VIP Only</option>
                                <option value="pending">Pending</option>
                                <option value="attending">Attending</option>
                            </select>
                        </div>

                        <!-- Guest Grid -->
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-6 max-h-96 overflow-y-auto p-1">
                            <div v-for="guest in guests" :key="guest.id" @click="toggleGuest(guest.id)" :class="[
                                'relative p-3 sm:p-4 rounded-lg border-2 cursor-pointer transition-all duration-200',
                                selectedGuests.includes(guest.id)
                                    ? 'border-indigo-500 bg-indigo-50/50 shadow-md'
                                    : 'border-gray-200 hover:border-indigo-300 hover:shadow-md hover:bg-gray-50/50'
                            ]">
                                <div class="flex items-center gap-3">
                                    <!-- Custom Checkbox -->
                                    <div :class="[
                                        'w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 transition-all',
                                        selectedGuests.includes(guest.id)
                                            ? 'bg-indigo-600 border-indigo-600 ring-2 ring-indigo-200'
                                            : 'border-gray-300 bg-white'
                                    ]">
                                        <svg v-if="selectedGuests.includes(guest.id)" class="w-3 h-3 text-white"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>

                                    <!-- Guest Avatar/Initials -->
                                    <div
                                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs sm:text-sm font-bold flex-shrink-0">
                                        {{ getGuestInitials(guest.id) }}
                                    </div>

                                    <!-- Guest Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1 mb-0.5">
                                            <h3 class="text-sm font-semibold text-gray-900 truncate">
                                                {{ guest.first_name }} {{ guest.last_name }}
                                            </h3>
                                            <span v-if="guest.is_vip"
                                                class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                VIP
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate">{{ guest.email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="guests.length === 0"
                            class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-900 mb-1">No Guests Available</h3>
                            <p class="text-xs text-gray-600 mb-3">Add guests to your event first.</p>
                            <Link :href="route('events.guests.create', event.id)"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Guest
                            </Link>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <Link :href="route('events.guests.index', event.id)"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all">
                                Cancel
                            </Link>
                            <button @click="nextStep" :disabled="selectedGuests.length === 0" :class="[
                                'inline-flex items-center px-5 sm:px-6 py-2.5 text-sm font-bold rounded-lg transition-all duration-200',
                                selectedGuests.length > 0
                                    ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white hover:from-indigo-700 hover:to-indigo-800 shadow-md hover:shadow-lg'
                                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            ]">
                                Continue
                                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>


                <!-- Step 2: Design Message (Generic for All Events) -->
                <div v-if="currentStep === 2" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <!-- Left: Form (3 columns) -->
                    <div class="lg:col-span-3 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">Design Message</h2>
                            <p class="text-purple-100 text-sm mt-1">Customize your invitation</p>
                        </div>

                        <div class="p-6 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                            <!-- Compact Delivery Method -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">
                                    DELIVERY METHOD <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <button @click="form.invitation_method = 'email'" type="button" :class="[
                                        'flex-1 px-3 py-2 rounded-lg border-2 transition-all text-xs font-semibold',
                                        form.invitation_method === 'email'
                                            ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                            : 'border-gray-200 hover:border-indigo-300 text-gray-600'
                                    ]">
                                        📧 Email
                                    </button>

                                    <button @click="form.invitation_method = 'sms'" type="button" :class="[
                                        'flex-1 px-3 py-2 rounded-lg border-2 transition-all text-xs font-semibold',
                                        form.invitation_method === 'sms'
                                            ? 'border-green-500 bg-green-50 text-green-700'
                                            : 'border-gray-200 hover:border-green-300 text-gray-600'
                                    ]">
                                        💬 SMS
                                    </button>

                                    <button @click="form.invitation_method = 'whatsapp'" type="button" :class="[
                                        'flex-1 px-3 py-2 rounded-lg border-2 transition-all text-xs font-semibold',
                                        form.invitation_method === 'whatsapp'
                                            ? 'border-green-500 bg-green-50 text-green-700'
                                            : 'border-gray-200 hover:border-green-300 text-gray-600'
                                    ]">
                                        📱 WhatsApp
                                    </button>
                                </div>
                            </div>

                            <!-- Subject (Email Only) -->
                            <div v-if="form.invitation_method === 'email'">
                                <label class="block text-xs font-bold text-gray-700 mb-2">
                                    EMAIL SUBJECT <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.subject" type="text" placeholder="You're Invited!"
                                    class="w-full px-3 py-2 text-sm rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            </div>

                            <!-- Message (Larger - Main Focus) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">
                                    INVITATION MESSAGE <span class="text-red-500">*</span>
                                </label>
                                <textarea v-model="form.message" rows="12" placeholder="Write your invitation message...

                    Example:
                    Dear [Guest Name],

                    You are cordially invited to [Event Name] on [Event Date].

                    We look forward to celebrating with you!

                    Warm regards"
                                    class="w-full px-3 py-2 text-sm rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all resize-none"></textarea>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-md font-medium">
                                        [Guest Name]
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-md font-medium">
                                        [Event Name]
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-md font-medium">
                                        [Event Date]
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded-md font-medium">
                                        [Event Location]
                                    </span>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">
                                    INVITATION CARD IMAGE (Optional)
                                </label>
                                <div
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-400 transition-all">
                                    <input type="file" @change="handleImageUpload" accept="image/*" class="hidden"
                                        ref="fileInput">
                                    <div v-if="!imagePreview" @click="$refs.fileInput.click()" class="cursor-pointer">
                                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs font-medium text-gray-900 mb-1">Click to upload invitation
                                            card</p>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                                    </div>
                                    <div v-else class="relative inline-block">
                                        <img :src="imagePreview" class="max-h-40 mx-auto rounded-lg shadow-lg">
                                        <button @click="removeImage" type="button"
                                            class="absolute -top-2 -right-2 p-1.5 bg-red-600 text-white rounded-full hover:bg-red-700 shadow-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <button @click="previousStep"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-white hover:border-gray-400 transition-all">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Back
                            </button>
                            <button @click="nextStep"
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm font-bold rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all shadow-md hover:shadow-lg">
                                Preview & Continue
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Simple Preview (2 columns) -->
                    <div
                        class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden sticky top-6">
                        <div class="bg-gradient-to-r from-pink-600 to-purple-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">Live Preview</h2>
                            <p class="text-pink-100 text-sm mt-1">How your invitation will look</p>
                        </div>

                        <div
                            class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 max-h-[calc(100vh-200px)] overflow-y-auto">
                            <!-- Simple Invitation Card Preview -->
                            <div
                                class="bg-white rounded-xl shadow-2xl overflow-hidden border-2 border-gray-200 max-w-md mx-auto flex flex-col">

                                <!-- Image Section - Takes 70% of the height -->
                                <div v-if="imagePreview" class="relative" style="flex: 7;">
                                    <img :src="imagePreview" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

                                    <!-- QR Code positioned at bottom right of image -->
                                    <div v-if="qrCodeUrl" class="absolute bottom-4 right-4 z-10">
                                        <div class="bg-white p-1">
                                            <img v-if="qrCodeBase64.startsWith('data:image')" :src="qrCodeBase64"
                                                alt="Event QR Code" class="w-20 h-20 object-cover" />
                                        </div>
                                    </div>
                                </div>
                                <div v-else
                                    class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center"
                                    style="flex: 7; min-height: 350px;">
                                    <div class="text-center text-white px-6">
                                        <svg class="w-20 h-20 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm font-medium opacity-90">Upload your invitation card image</p>
                                        <p class="text-xs opacity-75 mt-1">Image will appear here</p>
                                    </div>

                                    <!-- QR Code in placeholder (for preview) -->
                                    <div v-if="qrCodeUrl" class="absolute bottom-4 right-4 z-10">
                                        <div class="bg-white p-2">
                                            <img v-if="qrCodeBase64.startsWith('data:image')" :src="qrCodeBase64"
                                                alt="Event QR Code" class="w-20 h-20 object-cover" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Message Section (Bottom) - Takes 30% of the height -->
                                <div class="bg-white overflow-y-auto" style="flex: 3;">
                                    <div class="p-6">
                                        <div class="text-center mb-4">
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ event.title }}</h3>
                                            <div class="flex items-center justify-center gap-2 text-sm text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>{{ formatEventDate(event.event_date) }}</span>
                                            </div>
                                            <div v-if="event.city"
                                                class="flex items-center justify-center gap-2 text-sm text-gray-500 mt-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>{{ event.city }}, {{ event.country }}</span>
                                            </div>
                                        </div>

                                        <!-- Divider -->
                                        <div class="border-t-1 border-gray-100 my-2"></div>

                                        <!-- Message Content -->
                                        <div class="prose prose-sm max-w-none">
                                            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">
                                                {{ form.message || 'Your invitation message will appearhere...\n\nWritea personalized message to your guests using the form on the left.' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Helper Text -->
                            <p class="text-xs text-center text-gray-500 mt-4 italic">
                                This is how your invitation will appear to guests
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="currentStep === 3" class="max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left: Summary and Actions (2 columns) -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Header Card -->
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                                    <h2 class="text-xl font-bold text-white">Review & Send</h2>
                                    <p class="text-green-100 text-sm mt-1">Final check before sending</p>
                                </div>

                                <div class="p-6">
                                    <!-- Summary Stats -->
                                    <div class="grid grid-cols-3 gap-4 mb-6">
                                        <div
                                            class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4 border-2 border-indigo-200 text-center">
                                            <div
                                                class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                                </svg>
                                            </div>
                                            <div class="text-3xl font-black text-indigo-600">{{ selectedGuests.length }}
                                            </div>
                                            <div class="text-xs font-medium text-gray-600 mt-1">Recipients</div>
                                        </div>

                                        <div
                                            class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border-2 border-purple-200 text-center">
                                            <div
                                                class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="text-2xl font-black text-purple-600 uppercase">{{
                                                form.invitation_method
                                            }}</div>
                                            <div class="text-xs font-medium text-gray-600 mt-1">Method</div>
                                        </div>

                                        <div
                                            class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border-2 border-green-200 text-center">
                                            <div
                                                class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="text-2xl font-black text-green-600">Ready</div>
                                            <div class="text-xs font-medium text-gray-600 mt-1">Status</div>
                                        </div>
                                    </div>

                                    <!-- Recipients Section -->
                                    <div class="mb-6">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                                </svg>
                                                Recipients ({{ selectedGuests.length }})
                                            </h3>
                                            <button @click="currentStep = 1"
                                                class="text-xs font-medium text-indigo-600 hover:text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-all flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                                                <div v-for="guestId in selectedGuests" :key="guestId"
                                                    class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                                    <div
                                                        class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                                        {{ getGuestInitials(guestId) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-gray-900 truncate">{{
                                                            getGuestName(guestId) }}</p>
                                                        <p class="text-xs text-gray-500 truncate">{{
                                                            getGuestEmail(guestId) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Message Details -->
                                    <div class="mb-6">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                </svg>
                                                Message Details
                                            </h3>
                                            <button @click="currentStep = 2"
                                                class="text-xs font-medium text-indigo-600 hover:text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-all flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-3">
                                            <div v-if="form.invitation_method === 'email'">
                                                <div class="flex items-start gap-2 mb-2">
                                                    <svg class="w-4 h-4 text-gray-500 mt-0.5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                    <div class="flex-1">
                                                        <p class="text-xs font-medium text-gray-500 mb-1">Subject:</p>
                                                        <p class="text-sm font-semibold text-gray-900">{{ form.subject
                                                        }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-gray-500 mt-0.5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div class="flex-1">
                                                    <p class="text-xs font-medium text-gray-500 mb-2">Message:</p>
                                                    <div
                                                        class="text-sm text-gray-700 bg-white p-3 rounded-lg border border-gray-200 max-h-32 overflow-y-auto whitespace-pre-wrap">
                                                        {{ form.message }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="imagePreview"
                                                class="flex items-start gap-2 pt-3 border-t border-gray-200">
                                                <svg class="w-4 h-4 text-gray-500 mt-0.5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <div class="flex-1">
                                                    <p class="text-xs font-medium text-gray-500 mb-2">Attached Image:
                                                    </p>
                                                    <img :src="imagePreview"
                                                        class="h-20 rounded-lg shadow-md border border-gray-200">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div
                                        class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-6 border-t-2 border-gray-200">
                                        <button @click="previousStep"
                                            class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border-2 border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                            Back to Edit
                                        </button>
                                        <button @click="sendEmailInvitations" :disabled="sending" :class="[
                                            'w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 text-sm font-bold rounded-lg transition-all duration-200 shadow-xl',
                                            sending
                                                ? 'bg-gray-400 cursor-not-allowed'
                                                : 'bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 shadow-green-500/40 hover:shadow-2xl hover:scale-105'
                                        ]">
                                            <svg v-if="sending" class="animate-spin w-5 h-5 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                            {{ sending ? 'Sending Invitations...' : 'Send Invitations Now' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Visual Preview Card (1 column) -->
                        <div class="lg:col-span-1">
                            <div
                                class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden sticky top-6">
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                                    <h2 class="text-lg font-bold text-white">Final Preview</h2>
                                    <p class="text-indigo-100 text-xs mt-1">How guests will see it</p>
                                </div>

                                <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100">
                                    <!-- Invitation Card Preview -->
                                    <div
                                        class="bg-white rounded-lg shadow-2xl overflow-hidden border-2 border-gray-200">

                                        <!-- Image Section -->
                                        <div v-if="imagePreview" class="relative">
                                            <img :src="imagePreview" class="w-full h-48 object-cover">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent">
                                            </div>
                                        </div>
                                        <div v-else
                                            class="h-48 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                                            <div class="text-center text-white px-4">
                                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="text-xs opacity-90">No image</p>
                                            </div>
                                        </div>

                                        <!-- Content Section -->
                                        <div class="p-5">
                                            <!-- Event Header -->
                                            <div class="text-center mb-4">
                                                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ event.title }}</h3>
                                                <div
                                                    class="flex items-center justify-center gap-1.5 text-xs text-gray-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ formatEventDate(event.event_date) }}</span>
                                                </div>
                                                <div v-if="event.city"
                                                    class="flex items-center justify-center gap-1.5 text-xs text-gray-500 mt-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span>{{ event.city }}, {{ event.country }}</span>
                                                </div>
                                            </div>

                                            <!-- Divider -->
                                            <div class="border-t-2 border-gray-100 my-3"></div>

                                            <!-- Message Preview -->
                                            <div class="text-xs text-gray-700 leading-relaxed line-clamp-6 mb-4">
                                                {{ form.message }}
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Helper Text -->
                                    <p class="text-xs text-center text-gray-500 mt-3 italic">
                                        ✓ Ready to send to {{ selectedGuests.length }} guest{{ selectedGuests.length !==
                                            1 ? 's'
                                            : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
    event: Object,
    guests: Array,
    preselectedGuests: Array,
    qrCodeBase64: String
})

const currentStep = ref(1)
const selectedGuests = ref(props.preselectedGuests || [])
const sending = ref(false)
const imagePreview = ref(null)
const fileInput = ref(null)


// Updated form ref - Remove wedding-specific fields
const form = ref({
    invitation_method: 'email',
    subject: `You're Invited to ${props.event.title}!`,
    message: `Dear [Guest Name],

You are cordially invited to join us for ${props.event.title}.

Event Details:
📅 Date: ${formatEventDate(props.event.event_date)}
📍 Location: ${props.event.city ? `${props.event.city}, ${props.event.country}` : 'To be announced'}

We look forward to celebrating with you!

Warm regards,
${props.event.title} Team`,
    image: null
})



// Add QR Code computed property
const qrCodeUrl = computed(() => {
    return `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data`
})


const toggleGuest = (guestId) => {
    const index = selectedGuests.value.indexOf(guestId)
    if (index > -1) {
        selectedGuests.value.splice(index, 1)
    } else {
        selectedGuests.value.push(guestId)
    }
}

const nextStep = () => {
    if (currentStep.value < 3) {
        currentStep.value++
    }
}

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--
    }
}

const handleImageUpload = (event) => {
    const file = event.target.files[0]
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target.result
            form.value.image = file
        }
        reader.readAsDataURL(file)
    }
}

const removeImage = () => {
    imagePreview.value = null
    form.value.image = null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

const getGuestInitials = (guestId) => {
    const guest = props.guests.find(g => g.id === guestId)
    if (!guest) return '?'
    return `${guest.first_name?.[0] || ''}${guest.last_name?.[0] || ''}`.toUpperCase()
}

const getGuestName = (guestId) => {
    const guest = props.guests.find(g => g.id === guestId)
    if (!guest) return 'Unknown'
    return `${guest.first_name} ${guest.last_name}`
}

const getGuestEmail = (guestId) => {
    const guest = props.guests.find(g => g.id === guestId)
    return guest?.email || 'No email'
}

function formatEventDate(date) {
    if (!date) return 'Date TBA'
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const sendEmailInvitations = () => {
    sending.value = true

    const formData = new FormData()
    // formData.append('guest_ids', JSON.stringify(selectedGuests.value))

    selectedGuests.value.forEach(id => {
        formData.append('guest_ids[]', id)
    })
    formData.append('invitation_method', form.value.invitation_method)
    formData.append('subject', form.value.subject)
    formData.append('custom_message', form.value.message)
    if (form.value.image) {
        formData.append('invitation_image', form.value.image)
    }

    router.post(route('events.guests.bulk-invites', props.event.id), formData, {
        onSuccess: (response) => {
            sending.value = false
            const flashData = response.props?.flash?.data || {}

            Swal.fire({
                title: 'Success!',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Invitations sent successfully!</p>
                        <div class="space-y-2 text-sm">
                            ${flashData.invited_now > 0 ? `<p>✅ <strong>${flashData.invited_now}</strong> sent</p>` : ''}
                            ${flashData.skipped > 0 ? `<p>⚠️ <strong>${flashData.skipped}</strong> already invited</p>` : ''}
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonColor: '#4F46E5',
            }).then(() => {
                router.visit(route('events.guests.index', props.event.id))
            })
        },
        onError: () => {
            sending.value = false
            Swal.fire({
                title: 'Error!',
                text: 'Failed to send invitations. Please try again.',
                icon: 'error',
                confirmButtonColor: '#4F46E5',
            })
        }
    })
}

</script>