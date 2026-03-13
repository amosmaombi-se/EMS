<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('venues.index')" class="bc-link">Venues</Link>
                        <span class="bc-sep">›</span>
                        <Link :href="route('venues.show', venue.id)" class="bc-link">{{ venue.name }}</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">Edit</span>
                    </div>
                    <div class="page-eyebrow"><span class="eyebrow-dot" style="background:#F05A00"></span>Edit Venue</div>
                    <h1 class="page-title">{{ venue.name }}</h1>
                    <p class="page-sub">Update the venue details below</p>
                </div>
                <div class="header-actions">
                    <span v-if="form.isDirty" class="dirty-badge">
                        <span class="dirty-dot"></span>Unsaved changes
                    </span>
                    <Link :href="route('venues.show', venue.id)" class="btn-ghost">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                        Back to Venue
                    </Link>
                </div>
            </div>

            <!-- ── Validation Banner ── -->
            <div v-if="showValidationSummary && Object.keys(clientErrors).length > 0" class="error-banner">
                <div class="error-banner-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div>
                    <div class="error-banner-title">Please fix the following errors before saving:</div>
                    <ul class="error-banner-list">
                        <li v-for="(msg, field) in clientErrors" :key="field">
                            <span class="error-dot"></span>{{ msg }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- ── Layout ── -->
            <form @submit.prevent="submit" novalidate class="form-layout">

                <!-- ═══════════ MAIN ═══════════ -->
                <div class="main-col">

                    <!-- Basic Information -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(29,92,150,.1)">🏛️</span>
                            <div>
                                <div class="card-title">Basic Information</div>
                                <div class="card-sub">Name, type and description</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-grid">
                                <div class="col-span-2">
                                    <label class="ep-label">Venue Name <span class="req">*</span></label>
                                    <input v-model="form.name" @blur="touch('name')" type="text" placeholder="e.g. Grand Ballroom"
                                        class="ep-input" :class="fieldClass('name')">
                                    <FieldError :message="getError('name')" :show="hasError('name')" />
                                </div>
                                <div>
                                    <label class="ep-label">Venue Type <span class="req">*</span></label>
                                    <select v-model="form.type" @blur="touch('type')" class="ep-select" :class="fieldClass('type')">
                                        <option value="">Select Type</option>
                                        <option v-for="t in venueTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                                    </select>
                                    <FieldError :message="getError('type')" :show="hasError('type')" />
                                </div>
                                <div>
                                    <label class="ep-label">Virtual Tour URL <span class="opt">(optional)</span></label>
                                    <input v-model="form.virtual_tour_url" @blur="touch('virtual_tour_url')" type="text" placeholder="https://…"
                                        class="ep-input" :class="fieldClass('virtual_tour_url')">
                                    <FieldError :message="getError('virtual_tour_url')" :show="hasError('virtual_tour_url')" />
                                </div>
                                <div class="col-span-2">
                                    <label class="ep-label">Description</label>
                                    <textarea v-model="form.description" rows="4" placeholder="Describe the venue…"
                                        class="ep-input ep-textarea"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(249,178,51,.12)">📍</span>
                            <div>
                                <div class="card-title">Location</div>
                                <div class="card-sub">Address and map coordinates</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-grid">
                                <div class="col-span-2">
                                    <label class="ep-label">Street Address <span class="req">*</span></label>
                                    <input v-model="form.address" @blur="touch('address')" type="text" placeholder="123 Main Street"
                                        class="ep-input" :class="fieldClass('address')">
                                    <FieldError :message="getError('address')" :show="hasError('address')" />
                                </div>
                                <div>
                                    <label class="ep-label">City <span class="req">*</span></label>
                                    <input v-model="form.city" @blur="touch('city')" type="text" placeholder="City"
                                        class="ep-input" :class="fieldClass('city')">
                                    <FieldError :message="getError('city')" :show="hasError('city')" />
                                </div>
                                <div>
                                    <label class="ep-label">State / Province <span class="req">*</span></label>
                                    <input v-model="form.state" @blur="touch('state')" type="text" placeholder="State"
                                        class="ep-input" :class="fieldClass('state')">
                                    <FieldError :message="getError('state')" :show="hasError('state')" />
                                </div>
                                <div>
                                    <label class="ep-label">Country <span class="req">*</span></label>
                                    <input v-model="form.country" @blur="touch('country')" type="text" placeholder="Country"
                                        class="ep-input" :class="fieldClass('country')">
                                    <FieldError :message="getError('country')" :show="hasError('country')" />
                                </div>
                                <div>
                                    <label class="ep-label">Postal Code <span class="opt">(optional)</span></label>
                                    <input v-model="form.postal_code" type="text" placeholder="Postal Code" class="ep-input">
                                </div>
                                <div>
                                    <label class="ep-label">Latitude <span class="opt">(optional)</span></label>
                                    <input v-model="form.latitude" @blur="touch('latitude')" type="number" step="any" placeholder="e.g. -6.7924"
                                        class="ep-input" :class="fieldClass('latitude')">
                                    <FieldError :message="getError('latitude')" :show="hasError('latitude')" />
                                </div>
                                <div>
                                    <label class="ep-label">Longitude <span class="opt">(optional)</span></label>
                                    <input v-model="form.longitude" @blur="touch('longitude')" type="number" step="any" placeholder="e.g. 39.2083"
                                        class="ep-input" :class="fieldClass('longitude')">
                                    <FieldError :message="getError('longitude')" :show="hasError('longitude')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Capacity & Pricing -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(22,163,74,.1)">💰</span>
                            <div>
                                <div class="card-title">Capacity &amp; Pricing</div>
                                <div class="card-sub">Guest limits and rates in TZS</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-grid form-grid-3">
                                <div>
                                    <label class="ep-label">Min Capacity <span class="req">*</span></label>
                                    <input v-model="form.capacity_min" @blur="touch('capacity_min')" type="number" min="1" placeholder="e.g. 50"
                                        class="ep-input" :class="fieldClass('capacity_min')">
                                    <FieldError :message="getError('capacity_min')" :show="hasError('capacity_min')" />
                                </div>
                                <div>
                                    <label class="ep-label">Max Capacity <span class="req">*</span></label>
                                    <input v-model="form.capacity_max" @blur="touch('capacity_max'); touch('capacity_min')" type="number" min="1" placeholder="e.g. 500"
                                        class="ep-input" :class="fieldClass('capacity_max')">
                                    <FieldError :message="getError('capacity_max')" :show="hasError('capacity_max')" />
                                </div>
                                <div>
                                    <label class="ep-label">Area (sq ft) <span class="opt">(optional)</span></label>
                                    <input v-model="form.area_sqft" @blur="touch('area_sqft')" type="number" min="0" step="0.01" placeholder="e.g. 5000"
                                        class="ep-input" :class="fieldClass('area_sqft')">
                                    <FieldError :message="getError('area_sqft')" :show="hasError('area_sqft')" />
                                </div>
                                <div>
                                    <label class="ep-label">Price Per Day (TZS) <span class="req">*</span></label>
                                    <div class="prefix-wrap">
                                        <span class="prefix-txt">TZS</span>
                                        <input v-model="form.base_price_per_day" @blur="touch('base_price_per_day')" type="number" min="0" step="0.01" placeholder="0.00"
                                            class="ep-input prefix-input" :class="fieldClass('base_price_per_day')">
                                    </div>
                                    <FieldError :message="getError('base_price_per_day')" :show="hasError('base_price_per_day')" />
                                </div>
                                <div>
                                    <label class="ep-label">Price Per Hour (TZS) <span class="opt">(optional)</span></label>
                                    <div class="prefix-wrap">
                                        <span class="prefix-txt">TZS</span>
                                        <input v-model="form.base_price_per_hour" @blur="touch('base_price_per_hour')" type="number" min="0" step="0.01" placeholder="0.00"
                                            class="ep-input prefix-input" :class="fieldClass('base_price_per_hour')">
                                    </div>
                                    <FieldError :message="getError('base_price_per_hour')" :show="hasError('base_price_per_hour')" />
                                </div>
                                <div>
                                    <label class="ep-label">Security Deposit (TZS) <span class="opt">(optional)</span></label>
                                    <div class="prefix-wrap">
                                        <span class="prefix-txt">TZS</span>
                                        <input v-model="form.security_deposit" @blur="touch('security_deposit')" type="number" min="0" step="0.01" placeholder="0.00"
                                            class="ep-input prefix-input" :class="fieldClass('security_deposit')">
                                    </div>
                                    <FieldError :message="getError('security_deposit')" :show="hasError('security_deposit')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(192,23,15,.08)">👤</span>
                            <div>
                                <div class="card-title">Contact Information</div>
                                <div class="card-sub">Who manages this venue</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-grid form-grid-3">
                                <div>
                                    <label class="ep-label">Contact Person <span class="req">*</span></label>
                                    <input v-model="form.contact_person" @blur="touch('contact_person')" type="text" placeholder="Full name"
                                        class="ep-input" :class="fieldClass('contact_person')">
                                    <FieldError :message="getError('contact_person')" :show="hasError('contact_person')" />
                                </div>
                                <div>
                                    <label class="ep-label">Email <span class="req">*</span></label>
                                    <input v-model="form.email" @blur="touch('email')" type="email" placeholder="venue@example.com"
                                        class="ep-input" :class="fieldClass('email')">
                                    <FieldError :message="getError('email')" :show="hasError('email')" />
                                </div>
                                <div>
                                    <label class="ep-label">Phone <span class="req">*</span></label>
                                    <input v-model="form.phone" @blur="touch('phone')" type="tel" placeholder="+255 700 000 000"
                                        class="ep-input" :class="fieldClass('phone')">
                                    <FieldError :message="getError('phone')" :show="hasError('phone')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(22,163,74,.1)">✅</span>
                            <div>
                                <div class="card-title">Amenities</div>
                                <div class="card-sub">{{ form.amenities.length }} selected</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="amenities-grid">
                                <label v-for="amenity in availableAmenities" :key="amenity.value"
                                    class="amenity-chip" :class="form.amenities.includes(amenity.value) ? 'amenity-active' : ''">
                                    <input type="checkbox" :value="amenity.value" v-model="form.amenities" class="amenity-cb">
                                    <span class="amenity-check">
                                        <svg v-if="form.amenities.includes(amenity.value)" width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                    </span>
                                    <span class="amenity-label">{{ amenity.label }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Policies -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(107,101,96,.1)">📋</span>
                            <div>
                                <div class="card-title">Policies</div>
                                <div class="card-sub">Terms and cancellation rules</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-grid">
                                <div>
                                    <label class="ep-label">Terms &amp; Conditions</label>
                                    <textarea v-model="form.terms_and_conditions" rows="5" placeholder="Enter terms and conditions…"
                                        class="ep-input ep-textarea"></textarea>
                                </div>
                                <div>
                                    <label class="ep-label">Cancellation Policy</label>
                                    <textarea v-model="form.cancellation_policy" rows="5" placeholder="Enter cancellation policy…"
                                        class="ep-input ep-textarea"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ═══════════ SIDEBAR ═══════════ -->
                <div class="sidebar-col">
                    <div class="ep-card sidebar-sticky">

                        <!-- Live Summary -->
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(240,90,0,.1)">✏️</span>
                            <div>
                                <div class="card-title">Editing Venue</div>
                                <div class="card-sub" style="color:#F05A00">{{ form.isDirty ? 'Unsaved changes' : 'No changes yet' }}</div>
                            </div>
                        </div>
                        <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
                            <div class="summary-row">
                                <span class="summary-lbl">Name</span>
                                <span class="summary-val">{{ form.name || '—' }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">Type</span>
                                <span class="summary-val">{{ form.type ? formatType(form.type) : '—' }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">City</span>
                                <span class="summary-val">{{ form.city || '—' }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">Capacity</span>
                                <span class="summary-val">
                                    <span v-if="form.capacity_min || form.capacity_max">{{ form.capacity_min || '?' }}–{{ form.capacity_max || '?' }}</span>
                                    <span v-else>—</span>
                                </span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">Price/Day</span>
                                <span class="summary-val" style="color:#C0170F;font-weight:800">
                                    {{ form.base_price_per_day ? 'TZS ' + fmtNum(form.base_price_per_day) : '—' }}
                                </span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">Amenities</span>
                                <span class="summary-val">{{ form.amenities.length }} selected</span>
                            </div>
                        </div>

                        <!-- Settings / Toggles -->
                        <div style="border-top:1px solid #E8E2DA;padding:14px 16px;display:flex;flex-direction:column;gap:12px">
                            <div class="sidebar-section-lbl">Settings</div>
                            <div v-for="toggle in toggleSettings" :key="toggle.key" class="toggle-row">
                                <div>
                                    <div class="toggle-label">{{ toggle.label }}</div>
                                    <div class="toggle-desc">{{ toggle.description }}</div>
                                </div>
                                <button type="button" @click="form[toggle.key] = !form[toggle.key]"
                                    class="toggle-switch" :class="form[toggle.key] ? 'toggle-on' : 'toggle-off'">
                                    <span class="toggle-knob" :class="form[toggle.key] ? 'knob-on' : 'knob-off'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="padding:14px 16px;border-top:1px solid #E8E2DA;display:flex;flex-direction:column;gap:8px">
                            <button type="submit" :disabled="form.processing" class="btn-cta w-full">
                                <svg v-if="form.processing" class="spin" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <svg v-else width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                {{ form.processing ? 'Saving…' : 'Save Changes' }}
                            </button>
                            <button v-if="form.isDirty" type="button" @click="resetForm" class="btn-ghost-warn w-full">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reset Changes
                            </button>
                            <Link :href="route('venues.show', venue.id)" class="btn-ghost w-full" style="justify-content:center">
                                Discard &amp; View
                            </Link>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, reactive, computed, defineComponent, h } from 'vue'

const props = defineProps({ venue: Object })

// ─── Inline FieldError ───────────────────────────────────────────────────────
const FieldError = defineComponent({
    props: { message: String, show: Boolean },
    setup(p) {
        return () => p.show && p.message
            ? h('p', { style: 'margin-top:4px;font-size:11px;color:#C0170F;display:flex;align-items:center;gap:4px;font-family:DM Mono,monospace' }, [
                h('svg', { width:11, height:11, fill:'currentColor', viewBox:'0 0 20 20' }, [
                    h('path', { 'fill-rule':'evenodd', d:'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z', 'clip-rule':'evenodd' })
                ]),
                p.message
            ]) : null
    }
})

// ─── Form – pre-populated ────────────────────────────────────────────────────
const form = useForm({
    name: props.venue.name ?? '', type: props.venue.type ?? '',
    description: props.venue.description ?? '',
    address: props.venue.address ?? '', city: props.venue.city ?? '',
    state: props.venue.state ?? '', country: props.venue.country ?? '',
    postal_code: props.venue.postal_code ?? '',
    latitude: props.venue.latitude ?? '', longitude: props.venue.longitude ?? '',
    contact_person: props.venue.contact_person ?? '',
    email: props.venue.email ?? '', phone: props.venue.phone ?? '',
    capacity_min: props.venue.capacity_min ?? '', capacity_max: props.venue.capacity_max ?? '',
    area_sqft: props.venue.area_sqft ?? '',
    base_price_per_day: props.venue.base_price_per_day ?? '',
    base_price_per_hour: props.venue.base_price_per_hour ?? '',
    security_deposit: props.venue.security_deposit ?? '',
    amenities: props.venue.amenities ?? [],
    virtual_tour_url: props.venue.virtual_tour_url ?? '',
    terms_and_conditions: props.venue.terms_and_conditions ?? '',
    cancellation_policy: props.venue.cancellation_policy ?? '',
    is_active: props.venue.is_active ?? true,
    is_featured: props.venue.is_featured ?? false,
    is_verified: props.venue.is_verified ?? false,
})

// ─── Validation ──────────────────────────────────────────────────────────────
const touched = reactive({})
const showValidationSummary = ref(false)
const VALID_TYPES = ['indoor','outdoor','banquet_hall','garden','rooftop','beach','hotel','restaurant','other']

const rules = {
    name(v) { if (!v?.trim()) return 'Venue name is required.'; if (v.trim().length > 255) return 'Venue name must be 255 characters or fewer.'; return null },
    type(v) { if (!v) return 'Venue type is required.'; if (!VALID_TYPES.includes(v)) return 'Please select a valid venue type.'; return null },
    address(v) { return !v?.trim() ? 'Street address is required.' : null },
    city(v) { if (!v?.trim()) return 'City is required.'; if (v.trim().length > 100) return 'City must be 100 characters or fewer.'; return null },
    state(v) { if (!v?.trim()) return 'State / Province is required.'; if (v.trim().length > 100) return 'State must be 100 characters or fewer.'; return null },
    country(v) { if (!v?.trim()) return 'Country is required.'; if (v.trim().length > 100) return 'Country must be 100 characters or fewer.'; return null },
    contact_person(v) { return !v?.trim() ? 'Contact person is required.' : null },
    email(v) { if (!v?.trim()) return 'Email address is required.'; if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Please enter a valid email address.'; return null },
    phone(v) { if (!v?.trim()) return 'Phone number is required.'; if (!/^[\d\s+\-()]{7,20}$/.test(v)) return 'Please enter a valid phone number (7–20 digits).'; return null },
    capacity_min(v) { if (v===''||v===null||v===undefined) return 'Minimum capacity is required.'; if (Number(v)<1) return 'Minimum capacity must be at least 1.'; return null },
    capacity_max(v) { if (v===''||v===null||v===undefined) return 'Maximum capacity is required.'; if (Number(v)<1) return 'Maximum capacity must be at least 1.'; if (form.capacity_min!==''&&Number(v)<=Number(form.capacity_min)) return 'Maximum capacity must be greater than minimum.'; return null },
    base_price_per_day(v) { if (v===''||v===null||v===undefined) return 'Base price per day is required.'; if (Number(v)<0) return 'Price cannot be negative.'; return null },
    latitude(v) { if (v===''||v===null||v===undefined) return null; const n=Number(v); if (isNaN(n)||n<-90||n>90) return 'Latitude must be between -90 and 90.'; return null },
    longitude(v) { if (v===''||v===null||v===undefined) return null; const n=Number(v); if (isNaN(n)||n<-180||n>180) return 'Longitude must be between -180 and 180.'; return null },
    area_sqft(v) { if (v===''||v===null||v===undefined) return null; if (Number(v)<0) return 'Area cannot be negative.'; return null },
    base_price_per_hour(v) { if (v===''||v===null||v===undefined) return null; if (Number(v)<0) return 'Price per hour cannot be negative.'; return null },
    security_deposit(v) { if (v===''||v===null||v===undefined) return null; if (Number(v)<0) return 'Security deposit cannot be negative.'; return null },
    virtual_tour_url(v) { if (!v?.trim()) return null; try { new URL(v); return null } catch { return 'Please enter a valid URL (e.g. https://example.com).' } },
}

const clientErrors = computed(() => {
    const e = {}
    for (const [field, rule] of Object.entries(rules)) { const msg = rule(form[field]); if (msg) e[field] = msg }
    return e
})

const touch    = f => { touched[f] = true }
const touchAll = () => Object.keys(rules).forEach(f => { touched[f] = true })
const hasError = f => (touched[f] || showValidationSummary.value) && !!clientErrors.value[f]
const getError = f => form.errors[f] || clientErrors.value[f]
const fieldClass = f => hasError(f) ? 'ep-input-err' : ''
const isFormValid = computed(() => Object.keys(clientErrors.value).length === 0)

const submit = () => {
    touchAll()
    showValidationSummary.value = true
    if (!isFormValid.value) {
        setTimeout(() => { const el = document.querySelector('.ep-input-err'); if (el) el.scrollIntoView({ behavior:'smooth', block:'center' }) }, 50)
        return
    }
    form.put(route('venues.update', props.venue.id))
}

const resetForm = () => {
    form.reset()
    Object.keys(touched).forEach(k => delete touched[k])
    showValidationSummary.value = false
}

const formatType = t => t ? t.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : ''
const fmtNum = n => n ? Number(n).toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:0}) : ''

const venueTypes = [
    { value:'indoor', label:'Indoor' }, { value:'outdoor', label:'Outdoor' },
    { value:'banquet_hall', label:'Banquet Hall' }, { value:'garden', label:'Garden' },
    { value:'rooftop', label:'Rooftop' }, { value:'beach', label:'Beach' },
    { value:'hotel', label:'Hotel' }, { value:'restaurant', label:'Restaurant' },
    { value:'other', label:'Other' },
]
const availableAmenities = [
    { value:'wifi', label:'Wi-Fi' }, { value:'parking', label:'Parking' },
    { value:'air_conditioning', label:'Air Conditioning' }, { value:'catering', label:'Catering' },
    { value:'av_equipment', label:'AV Equipment' }, { value:'stage', label:'Stage' },
    { value:'dance_floor', label:'Dance Floor' }, { value:'outdoor_space', label:'Outdoor Space' },
    { value:'bridal_suite', label:'Bridal Suite' }, { value:'bar', label:'Bar' },
    { value:'kitchen', label:'Kitchen' }, { value:'wheelchair_accessible', label:'Wheelchair Accessible' },
    { value:'security', label:'Security' }, { value:'valet_parking', label:'Valet Parking' },
    { value:'generator', label:'Generator' }, { value:'swimming_pool', label:'Swimming Pool' },
]
const toggleSettings = [
    { key:'is_active',   label:'Active',   description:'Venue is available for booking' },
    { key:'is_featured', label:'Featured', description:'Show venue in featured sections' },
    { key:'is_verified', label:'Verified', description:'Mark venue as verified' },
]
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}

/* ── Header ── */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap}
.header-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex-shrink:0}
.breadcrumb{display:flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;margin-bottom:5px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C0B8}.bc-cur{color:#6B6560}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(22px,3vw,28px);font-weight:900;color:#1A1410;line-height:1.15;margin-bottom:4px}
.page-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}
.dirty-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:20px;background:rgba(249,178,51,.12);border:1px solid rgba(249,178,51,.35);font-family:'DM Mono',monospace;font-size:10px;font-weight:700;color:#b45309}
.dirty-dot{width:6px;height:6px;border-radius:50%;background:#F9B233;animation:blink .7s ease-in-out infinite}

/* ── Error banner ── */
.error-banner{display:flex;align-items:flex-start;gap:12px;background:rgba(192,23,15,.06);border:1.5px solid rgba(192,23,15,.25);border-radius:14px;padding:14px 16px;margin-bottom:18px}
.error-banner-icon{width:32px;height:32px;border-radius:8px;background:rgba(192,23,15,.1);color:#C0170F;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.error-banner-title{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#C0170F;margin-bottom:6px}
.error-banner-list{display:flex;flex-direction:column;gap:3px;list-style:none;padding:0;margin:0}
.error-banner-list li{display:flex;align-items:center;gap:6px;font-size:12px;color:#6B6560}
.error-dot{width:4px;height:4px;border-radius:50%;background:#C0170F;flex-shrink:0}

/* ── Layout ── */
.form-layout{display:grid;grid-template-columns:1fr 280px;gap:18px;align-items:start}
@media(max-width:1024px){.form-layout{grid-template-columns:1fr}}
.main-col{display:flex;flex-direction:column;gap:16px}
.sidebar-col{display:flex;flex-direction:column;gap:14px}
.sidebar-sticky{position:sticky;top:20px}

/* ── Cards ── */
.ep-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04)}
.card-head{display:flex;align-items:center;gap:12px;padding:13px 18px;background:#F0EDE8;border-bottom:1px solid #E8E2DA}
.card-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.card-title{font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:#1A1410;line-height:1.2}
.card-sub{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:1px}
.card-body{padding:18px}

/* ── Form grids ── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-grid-3{grid-template-columns:1fr 1fr 1fr}
@media(max-width:700px){.form-grid,.form-grid-3{grid-template-columns:1fr}}
.col-span-2{grid-column:span 2}
@media(max-width:700px){.col-span-2{grid-column:span 1}}

/* ── Form elements ── */
.ep-label{display:block;font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#6B6560;font-weight:700;margin-bottom:5px}
.req{color:#C0170F;font-size:10px}
.opt{color:#9E9890;font-size:9px;text-transform:none;letter-spacing:0;font-weight:400}
.ep-input,.ep-select{width:100%;padding:9px 12px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
.ep-input:focus,.ep-select:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.ep-input-err{border-color:rgba(192,23,15,.5) !important;background:rgba(192,23,15,.03) !important}
.ep-input-err:focus{border-color:#C0170F !important;box-shadow:0 0 0 3px rgba(192,23,15,.12) !important}
.ep-textarea{resize:vertical;min-height:100px}
.prefix-wrap{position:relative}
.prefix-txt{position:absolute;left:11px;top:50%;transform:translateY(-50%);font-family:'DM Mono',monospace;font-size:9px;font-weight:700;color:#9E9890;pointer-events:none;letter-spacing:.06em}
.prefix-input{padding-left:38px !important}

/* ── Amenities ── */
.amenities-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
@media(max-width:900px){.amenities-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.amenities-grid{grid-template-columns:repeat(2,1fr)}}
.amenity-chip{display:flex;align-items:center;gap:7px;padding:8px 10px;border:1.5px solid #E8E2DA;border-radius:10px;cursor:pointer;transition:all .15s;background:#fff}
.amenity-chip:hover{border-color:rgba(192,23,15,.3);background:rgba(192,23,15,.03)}
.amenity-active{border-color:rgba(192,23,15,.5);background:rgba(192,23,15,.05) !important}
.amenity-cb{display:none}
.amenity-check{width:16px;height:16px;border-radius:5px;border:1.5px solid #E8E2DA;background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s;color:#C0170F}
.amenity-active .amenity-check{background:#C0170F;border-color:#C0170F;color:#fff}
.amenity-label{font-size:11px;font-family:'DM Mono',monospace;font-weight:600;color:#1A1410}

/* ── Sidebar ── */
.summary-row{display:flex;align-items:center;justify-content:space-between;gap:8px}
.summary-lbl{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890}
.summary-val{font-family:'DM Mono',monospace;font-size:12px;font-weight:700;color:#1A1410;text-align:right;max-width:60%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sidebar-section-lbl{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.14em;color:#9E9890;font-weight:700}
.toggle-row{display:flex;align-items:center;justify-content:space-between;gap:8px}
.toggle-label{font-size:13px;font-weight:600;color:#1A1410}
.toggle-desc{font-family:'DM Mono',monospace;font-size:9px;color:#9E9890;margin-top:1px}
.toggle-switch{position:relative;width:40px;height:22px;border-radius:20px;border:none;cursor:pointer;transition:background .2s;flex-shrink:0;padding:0}
.toggle-on{background:linear-gradient(135deg,#C0170F,#F05A00)}
.toggle-off{background:#E8E2DA}
.toggle-knob{position:absolute;top:3px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.18);transition:left .2s}
.knob-on{left:21px}
.knob-off{left:3px}

/* ── Buttons ── */
.btn-cta{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:11px 20px;border-radius:12px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-cta:disabled{opacity:.6;cursor:not-allowed;animation:none;transform:none}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.w-full{width:100%}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;text-decoration:none}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-ghost-warn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid rgba(249,178,51,.4);background:rgba(249,178,51,.08);color:#b45309;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s}
.btn-ghost-warn:hover{background:rgba(249,178,51,.14);border-color:rgba(249,178,51,.6)}
.spin{animation:spinAnim .7s linear infinite}
@keyframes spinAnim{to{transform:rotate(360deg)}}
</style>