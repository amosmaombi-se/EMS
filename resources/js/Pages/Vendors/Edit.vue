<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('vendors.show', vendor.id)"
                    class="inline-flex items-center p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div class="flex-1 min-w-0">
                    <h2 class="font-bold text-2xl text-gray-900 truncate">Edit: {{ vendor.business_name }}</h2>
                    <p class="mt-1 text-sm text-gray-600">Update vendor details below</p>
                </div>
                <span v-if="form.isDirty"
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-medium rounded-full">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                    Unsaved changes
                </span>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Validation Summary -->
                <div v-if="showSummary && Object.keys(clientErrors).length > 0"
                    class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800">Please fix the following errors before
                                saving:</p>
                            <ul class="mt-1.5 space-y-0.5">
                                <li v-for="(msg, field) in clientErrors" :key="field"
                                    class="text-xs text-red-700 flex items-center gap-1.5">
                                    <span class="w-1 h-1 bg-red-400 rounded-full flex-shrink-0"></span>
                                    {{ msg }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" novalidate class="space-y-6">

                    <!-- ── Business Information ─── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Business Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Business Name <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.business_name" @blur="touch('business_name')" type="text"
                                    placeholder="e.g. Elite Photography Studio"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('business_name')">
                                <FE :msg="ge('business_name')" :show="he('business_name')" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.vendor_category_id" @blur="touch('vendor_category_id')"
                                    class="w-full px-3 py-2 rounded-lg border bg-white focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('vendor_category_id')">
                                    <option value="">Select Category</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}
                                    </option>
                                </select>
                                <FE :msg="ge('vendor_category_id')" :show="he('vendor_category_id')" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Website
                                    <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input v-model="form.website" @blur="touch('website')" type="text"
                                    placeholder="https://yourwebsite.com"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('website')">
                                <FE :msg="ge('website')" :show="he('website')" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                                <textarea v-model="form.description" rows="4"
                                    placeholder="Describe the vendor's services and expertise..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ── Contact Information ─── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Contact Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Contact Person <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.contact_person" @blur="touch('contact_person')" type="text"
                                    placeholder="Full name"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('contact_person')">
                                <FE :msg="ge('contact_person')" :show="he('contact_person')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.email" @blur="touch('email')" type="email"
                                    placeholder="vendor@example.com"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('email')">
                                <FE :msg="ge('email')" :show="he('email')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Phone <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.phone" @blur="touch('phone')" type="tel"
                                    placeholder="+255 712 345 678"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('phone')">
                                <FE :msg="ge('phone')" :show="he('phone')" />
                            </div>
                        </div>
                    </div>

                    <!-- ── Location ─── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Location
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Address <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.address" @blur="touch('address')" type="text"
                                    placeholder="Street address"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('address')">
                                <FE :msg="ge('address')" :show="he('address')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">City <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.city" @blur="touch('city')" type="text" placeholder="City"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('city')">
                                <FE :msg="ge('city')" :show="he('city')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">State / Region <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.state" @blur="touch('state')" type="text" placeholder="State"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('state')">
                                <FE :msg="ge('state')" :show="he('state')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Country <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.country" @blur="touch('country')" type="text"
                                    placeholder="Country"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('country')">
                                <FE :msg="ge('country')" :show="he('country')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Postal Code</label>
                                <input v-model="form.postal_code" type="text" placeholder="Postal Code"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- ── Business Details ─── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Business Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Registration
                                    Number</label>
                                <input v-model="form.business_registration_number" type="text"
                                    placeholder="e.g. TZ123456"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tax ID</label>
                                <input v-model="form.tax_id" type="text" placeholder="e.g. TIN123456789"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Min. Order Value
                                    (TZS)</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs pointer-events-none">TZS</span>
                                    <input v-model="form.minimum_order_value" @blur="touch('minimum_order_value')"
                                        type="number" min="0" step="1000" placeholder="0"
                                        class="w-full pl-11 pr-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                        :class="fc('minimum_order_value')">
                                </div>
                                <FE :msg="ge('minimum_order_value')" :show="he('minimum_order_value')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Years of
                                    Experience</label>
                                <input v-model="form.years_of_experience" @blur="touch('years_of_experience')"
                                    type="number" min="0" placeholder="e.g. 5"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('years_of_experience')">
                                <FE :msg="ge('years_of_experience')" :show="he('years_of_experience')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Team Size</label>
                                <input v-model="form.team_size" @blur="touch('team_size')" type="number" min="1"
                                    placeholder="e.g. 10"
                                    class="w-full px-3 py-2 rounded-lg border focus:outline-none focus:ring-1 text-sm transition-colors"
                                    :class="fc('team_size')">
                                <FE :msg="ge('team_size')" :show="he('team_size')" />
                            </div>
                        </div>
                    </div>

                    <!-- ── Verification (admin) ─── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Verification
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Verification
                                    Status</label>
                                <select v-model="form.verification_status"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-sm">
                                    <option value="pending">Pending</option>
                                    <option value="verified">Verified</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Verification
                                    Notes</label>
                                <input v-model="form.verification_notes" type="text"
                                    placeholder="Optional notes for the vendor..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- ── Service Areas & Specializations ─── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Service Areas & Specializations
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Service Areas</label>
                                <div class="flex gap-2 mb-2">
                                    <input v-model="newServiceArea" @keydown.enter.prevent="addServiceArea"
                                        type="text" placeholder="Add area and press Enter"
                                        class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                                    <button type="button" @click="addServiceArea"
                                        class="px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 text-sm font-medium transition-colors">
                                        Add
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="(area, i) in form.service_areas" :key="i"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 text-xs rounded-full border border-blue-200">
                                        {{ area }}
                                        <button type="button" @click="removeServiceArea(i)"
                                            class="hover:text-blue-900 ml-0.5">✕</button>
                                    </span>
                                    <span v-if="!form.service_areas.length" class="text-xs text-gray-400">No areas
                                        added</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Specializations</label>
                                <div class="flex gap-2 mb-2">
                                    <input v-model="newSpecialization" @keydown.enter.prevent="addSpecialization"
                                        type="text" placeholder="Add specialization and press Enter"
                                        class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none text-sm">
                                    <button type="button" @click="addSpecialization"
                                        class="px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 text-sm font-medium transition-colors">
                                        Add
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="(spec, i) in form.specializations" :key="i"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 text-purple-700 text-xs rounded-full border border-purple-200">
                                        {{ spec }}
                                        <button type="button" @click="removeSpecialization(i)"
                                            class="hover:text-purple-900 ml-0.5">✕</button>
                                    </span>
                                    <span v-if="!form.specializations.length" class="text-xs text-gray-400">No
                                        specializations added</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Settings ─── -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 pb-3 border-b border-gray-100">
                            Settings
                        </h3>
                        <div class="flex flex-col sm:flex-row gap-6">
                            <label v-for="toggle in toggleSettings" :key="toggle.key"
                                class="flex items-center gap-3 cursor-pointer">
                                <div class="relative" @click="form[toggle.key] = !form[toggle.key]">
                                    <div class="w-10 h-6 rounded-full transition-colors duration-200"
                                        :class="form[toggle.key] ? 'bg-indigo-600' : 'bg-gray-200'">
                                        <div class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200"
                                            :class="form[toggle.key] ? 'translate-x-4 left-0.5' : 'left-0.5'"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ toggle.label }}</p>
                                    <p class="text-xs text-gray-500">{{ toggle.description }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- ── Actions ─── -->
                    <div class="flex flex-col sm:flex-row justify-between gap-3 pb-6">
                        <div class="flex gap-2">
                            <Link :href="route('vendors.show', vendor.id)"
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Discard & View
                            </Link>
                            <button v-if="form.isDirty" type="button" @click="resetForm"
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Changes
                            </button>
                        </div>
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, reactive, computed, defineComponent, h } from 'vue'

const props = defineProps({ vendor: Object, categories: Array })

const FE = defineComponent({
    props: { msg: String, show: Boolean },
    setup(p) {
        return () => p.show && p.msg
            ? h('p', { class: 'mt-1 text-xs text-red-600 flex items-center gap-1' }, [
                h('svg', { class: 'w-3.5 h-3.5 flex-shrink-0', fill: 'currentColor', viewBox: '0 0 20 20' }, [
                    h('path', { 'fill-rule': 'evenodd', 'clip-rule': 'evenodd', d: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' })
                ]),
                p.msg
            ]) : null
    }
})

const form = useForm({
    vendor_category_id:           props.vendor.vendor_category_id ?? '',
    business_name:                props.vendor.business_name      ?? '',
    description:                  props.vendor.description        ?? '',
    contact_person:               props.vendor.contact_person     ?? '',
    email:                        props.vendor.email              ?? '',
    phone:                        props.vendor.phone              ?? '',
    website:                      props.vendor.website            ?? '',
    address:                      props.vendor.address            ?? '',
    city:                         props.vendor.city               ?? '',
    state:                        props.vendor.state              ?? '',
    country:                      props.vendor.country            ?? '',
    postal_code:                  props.vendor.postal_code        ?? '',
    business_registration_number: props.vendor.business_registration_number ?? '',
    tax_id:                       props.vendor.tax_id             ?? '',
    minimum_order_value:          props.vendor.minimum_order_value ?? '',
    years_of_experience:          props.vendor.years_of_experience ?? '',
    team_size:                    props.vendor.team_size          ?? '',
    service_areas:                props.vendor.service_areas      ?? [],
    specializations:              props.vendor.specializations    ?? [],
    verification_status:          props.vendor.verification_status ?? 'pending',
    verification_notes:           props.vendor.verification_notes ?? '',
    is_active:                    props.vendor.is_active          ?? true,
    is_featured:                  props.vendor.is_featured        ?? false,
})

// ── Tag inputs ────────────────────────────────────────────────────────────────
const newServiceArea    = ref('')
const newSpecialization = ref('')

const addServiceArea = () => {
    const v = newServiceArea.value.trim()
    if (v && !form.service_areas.includes(v)) form.service_areas.push(v)
    newServiceArea.value = ''
}
const removeServiceArea = (i) => form.service_areas.splice(i, 1)

const addSpecialization = () => {
    const v = newSpecialization.value.trim()
    if (v && !form.specializations.includes(v)) form.specializations.push(v)
    newSpecialization.value = ''
}
const removeSpecialization = (i) => form.specializations.splice(i, 1)

// ── Validation ────────────────────────────────────────────────────────────────
const touched     = reactive({})
const showSummary = ref(false)

const rules = {
    vendor_category_id(v) { return !v ? 'Category is required.' : null },
    business_name(v) {
        if (!v?.trim()) return 'Business name is required.'
        if (v.trim().length > 255) return 'Business name must be 255 characters or fewer.'
        return null
    },
    contact_person(v) { return !v?.trim() ? 'Contact person is required.' : null },
    email(v) {
        if (!v?.trim()) return 'Email address is required.'
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Please enter a valid email address.'
        return null
    },
    phone(v) {
        if (!v?.trim()) return 'Phone number is required.'
        if (!/^[\d\s+\-()]{7,20}$/.test(v)) return 'Please enter a valid phone number (7–20 digits).'
        return null
    },
    address(v)  { return !v?.trim() ? 'Address is required.' : null },
    city(v)     { return !v?.trim() ? 'City is required.' : null },
    state(v)    { return !v?.trim() ? 'State / Region is required.' : null },
    country(v)  { return !v?.trim() ? 'Country is required.' : null },
    website(v) {
        if (!v?.trim()) return null
        try { new URL(v); return null } catch { return 'Please enter a valid URL (e.g. https://example.com).' }
    },
    minimum_order_value(v) {
        if (v === '' || v === null || v === undefined) return null
        if (Number(v) < 0) return 'Minimum order value cannot be negative.'
        return null
    },
    years_of_experience(v) {
        if (v === '' || v === null || v === undefined) return null
        if (!Number.isInteger(Number(v)) || Number(v) < 0) return 'Years of experience must be a non-negative integer.'
        return null
    },
    team_size(v) {
        if (v === '' || v === null || v === undefined) return null
        if (!Number.isInteger(Number(v)) || Number(v) < 1) return 'Team size must be at least 1.'
        return null
    },
}

const clientErrors = computed(() => {
    const e = {}
    for (const [f, r] of Object.entries(rules)) {
        const msg = r(form[f])
        if (msg) e[f] = msg
    }
    return e
})

const touch    = (f) => { touched[f] = true }
const touchAll = () => Object.keys(rules).forEach(f => { touched[f] = true })
const he = (f) => (touched[f] || showSummary.value) && !!clientErrors.value[f]
const ge = (f) => form.errors[f] || clientErrors.value[f]
const fc = (f) => he(f)
    ? 'border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50'
    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'

const submit = () => {
    touchAll()
    showSummary.value = true
    if (Object.keys(clientErrors.value).length > 0) {
        setTimeout(() => {
            const el = document.querySelector('.border-red-300')
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }, 50)
        return
    }
    form.put(route('vendors.update', props.vendor.id))
}

const resetForm = () => {
    form.reset()
    Object.keys(touched).forEach(k => delete touched[k])
    showSummary.value = false
}

const toggleSettings = [
    { key: 'is_active',   label: 'Active',   description: 'Vendor is visible and bookable' },
    { key: 'is_featured', label: 'Featured', description: 'Highlight in featured sections' },
]
</script>