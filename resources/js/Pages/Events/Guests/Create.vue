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

                <!-- ══════════════════════════════════════════════════
                     IMPORT METHOD TABS
                ══════════════════════════════════════════════════ -->
                <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-xl w-fit">
                    <button @click="activeTab = 'manual'"
                            :class="activeTab === 'manual'
                                ? 'bg-white shadow-sm text-indigo-700 font-semibold'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Manual Entry
                    </button>
                    <button @click="activeTab = 'excel'"
                            :class="activeTab === 'excel'
                                ? 'bg-white shadow-sm text-indigo-700 font-semibold'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Import from Excel / CSV
                    </button>
                </div>

                <!-- ══════════════════════════════════════════════════
                     MANUAL ENTRY TAB
                ══════════════════════════════════════════════════ -->
                <template v-if="activeTab === 'manual'">
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
                                    <input type="checkbox" id="bulk_is_vip" v-model="bulkSettings.is_vip"
                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <label for="bulk_is_vip" class="ml-2 block text-sm text-gray-700">Mark as VIP</label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" id="bulk_plus_one_allowed" v-model="bulkSettings.plus_one_allowed"
                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <label for="bulk_plus_one_allowed" class="ml-2 block text-sm text-gray-700">Allow Plus One</label>
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
                                    <input type="number" v-model="bulkSettings.plus_ones" min="0" max="10"
                                           class="w-full rounded-lg border border-gray-300 py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                </div>
                            </div>
                            
                            <!-- Add Multiple Form -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Add Guest(s)</h4>
                                <div class="space-y-3">
                                    <div v-for="(guest, index) in tempGuests" :key="index" 
                                         class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                        <div>
                                            <input type="text" v-model="guest.first_name" placeholder="First name"
                                                   class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <input type="text" v-model="guest.last_name" placeholder="Last name"
                                                   class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <input type="email" v-model="guest.email" placeholder="Email (optional)"
                                                   class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="text" v-model="guest.phone" placeholder="Phone (optional)"
                                                   class="flex-1 rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                            <button v-if="index > 0" @click="removeTempGuest(index)" type="button"
                                                    class="p-2 text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <button @click="addTempGuest" type="button"
                                            class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add another guest
                                    </button>
                                </div>
                            </div>
                            
                            <button @click="addGuestsToList" type="button"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add to Guest List
                            </button>
                        </div>
                    </div>
                </template>

                <!-- ══════════════════════════════════════════════════
                     EXCEL / CSV IMPORT TAB
                ══════════════════════════════════════════════════ -->
                <template v-if="activeTab === 'excel'">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Import from Excel or CSV</h3>
                                    <p class="text-sm text-gray-500">Upload a spreadsheet with your guest list. Supports <strong>.xlsx</strong>, <strong>.xls</strong>, and <strong>.csv</strong> formats.</p>
                                </div>
                                <!-- Download template buttons -->
                                <div class="flex gap-2 shrink-0 ml-4">
                                    <a :href="`/events/${event.id}/guests/import-template?format=xlsx`"
                                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download .xlsx Template
                                    </a>
                                    <a :href="`/events/${event.id}/guests/import-template?format=csv`"
                                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download .csv Template
                                    </a>
                                </div>
                            </div>

                            <!-- Column reference card -->
                            <div class="bg-indigo-50 rounded-lg p-4 mb-6 border border-indigo-100">
                                <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wider mb-2">Expected Columns</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs text-indigo-900">
                                    <div><span class="font-semibold text-red-600">first_name *</span></div>
                                    <div><span class="font-semibold text-red-600">last_name *</span></div>
                                    <div>email</div>
                                    <div>phone</div>
                                    <div>category</div>
                                    <div>guest_type</div>
                                    <div>rsvp_status</div>
                                    <div>is_vip</div>
                                    <div>plus_one_allowed</div>
                                    <div>plus_ones</div>
                                    <div>language_preference</div>
                                    <div>notes</div>
                                </div>
                                <p class="text-xs text-indigo-500 mt-2">* Required &nbsp;·&nbsp; All other columns are optional and will use defaults if omitted.</p>
                            </div>

                            <!-- Drop zone -->
                            <div @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop.prevent="handleFileDrop"
                                 :class="isDragging
                                     ? 'border-indigo-500 bg-indigo-50'
                                     : excelFile
                                         ? 'border-emerald-400 bg-emerald-50'
                                         : 'border-gray-300 bg-gray-50 hover:border-indigo-400 hover:bg-indigo-50'"
                                 class="border-2 border-dashed rounded-xl p-8 text-center transition-all cursor-pointer mb-4"
                                 @click="$refs.fileInput.click()">

                                <input ref="fileInput" type="file"
                                       accept=".xlsx,.xls,.csv"
                                       class="hidden"
                                       @change="handleFileSelect">

                                <!-- No file yet -->
                                <template v-if="!excelFile">
                                    <div class="flex justify-center mb-3">
                                        <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Drop your file here, or <span class="text-indigo-600">browse</span></p>
                                    <p class="text-xs text-gray-400 mt-1">Supports .xlsx, .xls, .csv · Max 5 MB</p>
                                </template>

                                <!-- File selected -->
                                <template v-else>
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                             :class="excelFile.name.endsWith('.csv') ? 'bg-blue-100' : 'bg-emerald-100'">
                                            <svg class="w-5 h-5"
                                                 :class="excelFile.name.endsWith('.csv') ? 'text-blue-600' : 'text-emerald-600'"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-sm font-semibold text-gray-800">{{ excelFile.name }}</p>
                                            <p class="text-xs text-gray-500">{{ formatFileSize(excelFile.size) }}</p>
                                        </div>
                                        <button @click.stop="clearExcelFile"
                                                class="ml-2 p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Parse errors -->
                            <div v-if="importErrors.length" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm font-semibold text-red-700 mb-2">Import issues found:</p>
                                <ul class="text-sm text-red-600 space-y-1">
                                    <li v-for="(err, i) in importErrors" :key="i" class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ err }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Preview table -->
                            <div v-if="importPreview.length" class="mb-5">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-sm font-semibold text-gray-800">
                                        Preview — {{ importPreview.length }} guest{{ importPreview.length !== 1 ? 's' : '' }} ready to import
                                        <span v-if="importSkipped" class="ml-2 text-yellow-600">({{ importSkipped }} row{{ importSkipped !== 1 ? 's' : '' }} skipped — missing name)</span>
                                    </p>
                                    <span class="text-xs text-gray-400">Showing first 10</span>
                                </div>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">RSVP</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">VIP</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            <tr v-for="(g, i) in importPreview.slice(0, 10)" :key="i" class="hover:bg-gray-50">
                                                <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap">{{ g.first_name }} {{ g.last_name }}</td>
                                                <td class="px-3 py-2 text-gray-500 whitespace-nowrap">{{ g.email || '—' }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                                                          :class="getCategoryClass(g.category)">{{ g.category }}</span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                                                          :class="getRsvpStatusClass(g.rsvp_status)">{{ g.rsvp_status }}</span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span v-if="g.is_vip" class="px-1.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">VIP</span>
                                                    <span v-else class="text-gray-400">—</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p v-if="importPreview.length > 10" class="text-xs text-gray-400 mt-1 text-right">
                                    + {{ importPreview.length - 10 }} more not shown
                                </p>
                            </div>

                            <!-- Import action -->
                            <div class="flex items-center gap-3">
                                <button @click="parseExcelFile"
                                        :disabled="!excelFile || isParsing"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-indigo-300 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                    <svg v-if="isParsing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ isParsing ? 'Parsing…' : 'Preview File' }}
                                </button>

                                <button v-if="importPreview.length"
                                        @click="addImportedGuestsToList"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add {{ importPreview.length }} Guest{{ importPreview.length !== 1 ? 's' : '' }} to List
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- ══════════════════════════════════════════════════
                     GUEST LIST PREVIEW (shared between tabs)
                ══════════════════════════════════════════════════ -->
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
                                            <div class="text-sm font-medium text-gray-900">{{ guest.first_name }} {{ guest.last_name }}</div>
                                            <div v-if="guest.phone" class="text-xs text-gray-500">{{ guest.phone }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ guest.email || 'No email' }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                                  :class="getCategoryClass(guest.category)">{{ guest.category }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                                  :class="getRsvpStatusClass(guest.rsvp_status)">{{ guest.rsvp_status }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span v-if="guest.is_vip" class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">VIP</span>
                                            <span v-else class="text-xs text-gray-500">-</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <button @click="editGuest(index)" class="text-indigo-600 hover:text-indigo-900 text-sm mr-3">Edit</button>
                                            <button @click="removeGuest(index)" class="text-red-600 hover:text-red-900 text-sm">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════
                     EDIT MODAL
                ══════════════════════════════════════════════════ -->
                <Modal :show="showEditModal" @close="showEditModal = false" max-width="lg">
                    <div class="p-6" v-if="editingGuest !== null">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Edit Guest</h2>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" v-model="editingGuest.first_name"
                                       class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" v-model="editingGuest.last_name"
                                       class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" v-model="editingGuest.email"
                                       class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="tel" v-model="editingGuest.phone"
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
                                        <input type="checkbox" id="edit_is_vip" v-model="editingGuest.is_vip"
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="edit_is_vip" class="ml-2 block text-sm text-gray-700">VIP Guest</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="edit_plus_one_allowed" v-model="editingGuest.plus_one_allowed"
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="edit_plus_one_allowed" class="ml-2 block text-sm text-gray-700">Allow Plus One</label>
                                    </div>
                                    <div v-if="editingGuest.plus_one_allowed" class="flex items-center">
                                        <label class="text-sm text-gray-700 mr-2">Count:</label>
                                        <input type="number" v-model="editingGuest.plus_ones" min="0" max="10"
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

                <!-- ══════════════════════════════════════════════════
                     FINALIZE
                ══════════════════════════════════════════════════ -->
                <div v-if="form.guests.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Finalize Guest List</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center mb-4">
                                <input type="checkbox" id="send_invitations" v-model="form.send_invitations"
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
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                            <textarea v-model="form.notes" rows="3"
                                      class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                      placeholder="Any notes that apply to all guests..."></textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button @click="clearAllGuests" type="button"
                                    class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-lg hover:bg-red-50">
                                Clear All Guests
                            </button>
                            <button @click="submit" :disabled="form.processing || isSubmitting"
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
import * as XLSX from 'xlsx'   // npm install xlsx

const props = defineProps({ event: Object })

// ── UI state ──────────────────────────────────────────────────────────────────
const activeTab       = ref('manual')
const showEditModal   = ref(false)
const editingIndex    = ref(null)
const editingGuest    = ref(null)
const isSubmitting    = ref(false)

// ── Excel import state ────────────────────────────────────────────────────────
const fileInput      = ref(null)
const excelFile      = ref(null)
const isDragging     = ref(false)
const isParsing      = ref(false)
const importPreview  = ref([])
const importErrors   = ref([])
const importSkipped  = ref(0)

// ── Manual entry state ────────────────────────────────────────────────────────
const bulkSettings = ref({
    category: 'friends',
    guest_type: 'primary',
    rsvp_status: 'pending',
    is_vip: false,
    plus_one_allowed: false,
    plus_ones: 0,
    language_preference: 'en'
})

const tempGuests = ref([{ first_name: '', last_name: '', email: '', phone: '' }])

const form = ref({
    guests: [],
    send_invitations: false,
    invitation_method: 'email',
    notes: ''
})

// ── Computed ──────────────────────────────────────────────────────────────────
const guestsWithEmail = computed(() =>
    form.value.guests.filter(g => g.email && g.email.trim()).length
)

// ── Excel helpers ─────────────────────────────────────────────────────────────
const VALID_CATEGORIES   = ['family','friends','colleagues','business','vip','sponsors','media','other']
const VALID_GUEST_TYPES  = ['primary','plus_one','child','vendor','staff','speaker','performer']
const VALID_RSVP         = ['pending','attending','not_attending','maybe']
const VALID_LANGUAGES    = ['en','es','fr','de']

const normalizeBoolean = (val) => {
    if (val === true || String(val).toLowerCase() === 'true' || val === 1) return true
    return false
}

const normalizeRow = (row) => ({
    first_name:          String(row.first_name || row['first_name *'] || '').trim(),
    last_name:           String(row.last_name  || row['last_name *']  || '').trim(),
    email:               String(row.email       || '').trim(),
    phone:               String(row.phone       || '').trim(),
    category:            VALID_CATEGORIES.includes(String(row.category || '').toLowerCase())
                            ? String(row.category).toLowerCase() : 'friends',
    guest_type:          VALID_GUEST_TYPES.includes(String(row.guest_type || '').toLowerCase())
                            ? String(row.guest_type).toLowerCase() : 'primary',
    rsvp_status:         VALID_RSVP.includes(String(row.rsvp_status || '').toLowerCase())
                            ? String(row.rsvp_status).toLowerCase() : 'pending',
    is_vip:              normalizeBoolean(row.is_vip),
    plus_one_allowed:    normalizeBoolean(row.plus_one_allowed),
    plus_ones:           Math.min(10, Math.max(0, parseInt(row.plus_ones) || 0)),
    language_preference: VALID_LANGUAGES.includes(String(row.language_preference || '').toLowerCase())
                            ? String(row.language_preference).toLowerCase() : 'en',
    dietary_preference:  String(row.dietary_preference || '').trim(),
    allergies:           String(row.allergies || '').trim(),
    special_requirements: String(row.special_requirements || '').trim(),
    accessibility_needs: String(row.accessibility_needs || '').trim(),
    accommodation_needs: String(row.accommodation_needs || '').trim(),
    transportation_needs: String(row.transportation_needs || '').trim(),
    notes:               String(row.notes || '').trim(),
})

const handleFileDrop = (e) => {
    isDragging.value = false
    const file = e.dataTransfer.files[0]
    if (file) setExcelFile(file)
}

const handleFileSelect = (e) => {
    const file = e.target.files[0]
    if (file) setExcelFile(file)
}

const setExcelFile = (file) => {
    const ext = file.name.split('.').pop().toLowerCase()
    if (!['xlsx','xls','csv'].includes(ext)) {
        Swal.fire({ title: 'Unsupported File', text: 'Please upload an .xlsx, .xls, or .csv file.', icon: 'warning', confirmButtonColor: '#4f46e5' })
        return
    }
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({ title: 'File Too Large', text: 'Maximum file size is 5 MB.', icon: 'warning', confirmButtonColor: '#4f46e5' })
        return
    }
    excelFile.value = file
    importPreview.value = []
    importErrors.value  = []
    importSkipped.value = 0
}

const clearExcelFile = () => {
    excelFile.value = null
    importPreview.value = []
    importErrors.value  = []
    importSkipped.value = 0
    if (fileInput.value) fileInput.value.value = ''
}

const formatFileSize = (bytes) => {
    if (bytes < 1024)       return bytes + ' B'
    if (bytes < 1024*1024)  return (bytes/1024).toFixed(1) + ' KB'
    return (bytes/1024/1024).toFixed(1) + ' MB'
}

const parseExcelFile = async () => {
    if (!excelFile.value) return
    isParsing.value    = true
    importErrors.value = []

    try {
        const buffer = await excelFile.value.arrayBuffer()
        const wb     = XLSX.read(buffer, { type: 'array' })
        const ws     = wb.Sheets[wb.SheetNames[0]]
        const rows   = XLSX.utils.sheet_to_json(ws, { defval: '' })

        if (!rows.length) {
            importErrors.value.push('The file appears to be empty.')
            return
        }

        // Check for required columns
        const firstRow = rows[0]
        const keys = Object.keys(firstRow).map(k => k.toLowerCase().replace(' *','').trim())
        if (!keys.includes('first_name') && !keys.includes('first_name *')) {
            importErrors.value.push('Missing required column: first_name')
        }
        if (!keys.includes('last_name') && !keys.includes('last_name *')) {
            importErrors.value.push('Missing required column: last_name')
        }
        if (importErrors.value.length) return

        // Filter out hint row (row 2 with "Required" / "Optional" text)
        const dataRows = rows.filter(r => {
            const fn = String(r.first_name || r['first_name *'] || '').trim().toLowerCase()
            return fn !== 'required' && fn !== ''
        })

        let skipped = 0
        const parsed = []
        for (const row of dataRows) {
            const fn = String(row.first_name || row['first_name *'] || '').trim()
            const ln = String(row.last_name  || row['last_name *']  || '').trim()
            if (!fn || !ln) { skipped++; continue }
            parsed.push(normalizeRow(row))
        }

        if (!parsed.length) {
            importErrors.value.push('No valid guests found. Make sure first_name and last_name are filled in.')
            return
        }

        importPreview.value = parsed
        importSkipped.value = skipped

    } catch (err) {
        importErrors.value.push('Could not read file: ' + err.message)
    } finally {
        isParsing.value = false
    }
}

const addImportedGuestsToList = () => {
    if (!importPreview.value.length) return
    importPreview.value.forEach(g => form.value.guests.push(g))

    Swal.fire({
        title: 'Imported!',
        text: `${importPreview.value.length} guest(s) added to the list.`,
        icon: 'success', timer: 2000, showConfirmButton: false
    })

    clearExcelFile()
    activeTab.value = 'manual'  // switch to preview tab
}

// ── Manual entry helpers ──────────────────────────────────────────────────────
const addTempGuest = () => {
    tempGuests.value.push({ first_name: '', last_name: '', email: '', phone: '' })
}

const removeTempGuest = async (index) => {
    if (tempGuests.value.length <= 1) {
        Swal.fire({ title: 'Cannot Remove', text: 'You must have at least one guest entry.', icon: 'warning', confirmButtonColor: '#4f46e5' })
        return
    }
    const result = await Swal.fire({
        title: 'Remove Guest Entry?', text: 'Are you sure?', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove it!'
    })
    if (result.isConfirmed) {
        tempGuests.value.splice(index, 1)
        Swal.fire({ title: 'Removed!', icon: 'success', timer: 1500, showConfirmButton: false })
    }
}

const addGuestsToList = async () => {
    const valid = tempGuests.value.filter(g => g.first_name.trim() && g.last_name.trim())
    if (!valid.length) {
        Swal.fire({ title: 'No Guests Added', text: 'Please add at least one guest with first and last name.', icon: 'warning', confirmButtonColor: '#4f46e5' })
        return
    }
    const missing = tempGuests.value.length - valid.length
    if (missing > 0) {
        const r = await Swal.fire({
            title: 'Incomplete Guests',
            text: `${missing} guest(s) are missing names. Only complete guests will be added. Continue?`,
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#4f46e5', cancelButtonColor: '#6b7280',
            confirmButtonText: 'Add Complete Guests'
        })
        if (!r.isConfirmed) return
    }
    valid.forEach(g => form.value.guests.push({
        first_name: g.first_name.trim(), last_name: g.last_name.trim(),
        email: g.email?.trim() || '', phone: g.phone?.trim() || '',
        category: bulkSettings.value.category, guest_type: bulkSettings.value.guest_type,
        rsvp_status: bulkSettings.value.rsvp_status, is_vip: bulkSettings.value.is_vip,
        plus_one_allowed: bulkSettings.value.plus_one_allowed,
        plus_ones: bulkSettings.value.plus_one_allowed ? bulkSettings.value.plus_ones : 0,
        language_preference: bulkSettings.value.language_preference,
        dietary_preference: '', allergies: '', special_requirements: '',
        accessibility_needs: '', accommodation_needs: '', transportation_needs: '', notes: ''
    }))
    Swal.fire({ title: 'Guests Added!', text: `${valid.length} guest(s) added.`, icon: 'success', timer: 2000, showConfirmButton: false })
    tempGuests.value = [{ first_name: '', last_name: '', email: '', phone: '' }]
}

const removeGuest = async (index) => {
    const name = `${form.value.guests[index].first_name} ${form.value.guests[index].last_name}`
    const r = await Swal.fire({
        title: 'Remove Guest?', html: `Remove <strong>${name}</strong> from the list?`, icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Yes, remove it!'
    })
    if (r.isConfirmed) {
        form.value.guests.splice(index, 1)
        Swal.fire({ title: 'Removed!', text: `${name} removed.`, icon: 'success', timer: 1500, showConfirmButton: false })
    }
}

const editGuest = (index) => {
    editingIndex.value = index
    editingGuest.value = { ...form.value.guests[index] }
    showEditModal.value = true
}

const saveGuestEdit = async () => {
    if (!editingGuest.value.first_name.trim() || !editingGuest.value.last_name.trim()) {
        Swal.fire({ title: 'Missing Information', text: 'Please enter both first and last name.', icon: 'warning', confirmButtonColor: '#4f46e5' })
        return
    }
    form.value.guests[editingIndex.value] = editingGuest.value
    showEditModal.value = false; editingIndex.value = null; editingGuest.value = null
    Swal.fire({ title: 'Updated!', text: 'Guest information updated.', icon: 'success', timer: 1500, showConfirmButton: false })
}

const clearAllGuests = async () => {
    if (!form.value.guests.length) return
    const r = await Swal.fire({
        title: 'Clear All Guests?',
        html: `Remove all <strong>${form.value.guests.length}</strong> guests?<br><span class="text-sm text-gray-500">This cannot be undone.</span>`,
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, clear all!', reverseButtons: true
    })
    if (r.isConfirmed) {
        form.value.guests = []
        Swal.fire({ title: 'Cleared!', text: 'All guests removed.', icon: 'success', timer: 2000, showConfirmButton: false })
    }
}

const submit = async () => {
    if (!form.value.guests.length) {
        Swal.fire({ title: 'No Guests', text: 'Please add at least one guest before saving.', icon: 'warning', confirmButtonColor: '#4f46e5' })
        return
    }
    const r = await Swal.fire({
        title: 'Ready to Save?',
        html: `<div class="text-left"><p>You are about to add <strong>${form.value.guests.length} guest(s)</strong> to the event.</p></div>`,
        icon: 'question', showCancelButton: true, confirmButtonColor: '#4f46e5', cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, save ${form.value.guests.length} guest(s)`
    })
    if (!r.isConfirmed) return

    isSubmitting.value = true
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        const response = await fetch(route('events.guests.bulk-store', props.event.id), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                guests: form.value.guests,
                send_invitations: form.value.send_invitations || false,
                invitation_method: form.value.invitation_method || 'email',
                notes: form.value.notes || ''
            })
        })
        const data = await response.json()
        if (response.ok && data.success) {
            await Swal.fire({
                title: 'Success!',
                html: `<div class="text-left"><p class="text-green-600 font-semibold">${data.created_count} guest(s) created successfully</p>${data.skipped_count ? `<p class="text-yellow-600">${data.skipped_count} skipped (duplicate)</p>` : ''}${data.invitations_sent ? `<p class="text-blue-600">${data.invitations_sent} invitation(s) sent</p>` : ''}</div>`,
                icon: 'success', confirmButtonColor: '#4f46e5', confirmButtonText: 'View Guests'
            }).then(res => { if (res.isConfirmed) window.location.href = route('events.guests.index', props.event.id) })
            form.value = { guests: [], send_invitations: false, invitation_method: 'email', notes: '' }
        } else {
            let msg = 'Failed to save guests.'
            if (data.message) msg += `<br><span class="text-sm text-gray-600">${data.message}</span>`
            await Swal.fire({ title: 'Error!', html: msg, icon: 'error', confirmButtonColor: '#ef4444' })
        }
    } catch (err) {
        await Swal.fire({ title: 'Network Error!', text: 'Could not connect to the server.', icon: 'error', confirmButtonColor: '#ef4444' })
    } finally {
        isSubmitting.value = false
    }
}

// ── Style helpers ─────────────────────────────────────────────────────────────
const getCategoryClass = (c) => ({
    vip: 'bg-yellow-100 text-yellow-800', family: 'bg-purple-100 text-purple-800',
    friends: 'bg-blue-100 text-blue-800', colleagues: 'bg-green-100 text-green-800',
    business: 'bg-indigo-100 text-indigo-800', media: 'bg-pink-100 text-pink-800',
    sponsors: 'bg-red-100 text-red-800', other: 'bg-gray-100 text-gray-800'
})[c] || 'bg-gray-100 text-gray-800'

const getRsvpStatusClass = (s) => ({
    pending: 'bg-yellow-100 text-yellow-800', attending: 'bg-green-100 text-green-800',
    not_attending: 'bg-red-100 text-red-800', maybe: 'bg-blue-100 text-blue-800'
})[s] || 'bg-gray-100 text-gray-800'
</script>