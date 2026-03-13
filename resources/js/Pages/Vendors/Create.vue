<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('vendors.index')" class="bc-link">Vendors</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">New Vendor</span>
                    </div>
                    <div class="eyebrow-row"><span class="eyebrow-dot"></span>Create</div>
                    <h1 class="page-title">Add New Vendor</h1>
                    <p class="page-sub">Fill in the details below to register a vendor</p>
                </div>
            </div>

            <!-- ── Validation Summary ── -->
            <div v-if="showSummary && Object.keys(clientErrors).length > 0" class="val-summary">
                <div class="val-icon">
                    <svg width="16" height="16" fill="none" stroke="#C0170F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <p class="val-title">Please fix the following errors:</p>
                    <ul class="val-list">
                        <li v-for="(msg, field) in clientErrors" :key="field" class="val-item">
                            <span class="val-dot"></span>{{ msg }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- ── Layout ── -->
            <div class="form-layout">

                <!-- ═══ MAIN FORM ═══ -->
                <div class="form-main">
                    <form @submit.prevent="submit" novalidate>

                        <!-- Business Information -->
                        <div class="ep-card form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(29,92,150,.1)">🏢</span>
                                <div>
                                    <div class="card-title">Business Information</div>
                                    <div class="card-sub">Core details about the vendor</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field-grid">
                                    <div class="col-span-2">
                                        <label class="field-label">Business Name <span class="req">*</span></label>
                                        <input v-model="form.business_name" @blur="touch('business_name')" type="text" placeholder="e.g. Elite Photography Studio" class="ep-input" :class="fc('business_name')">
                                        <FE :msg="ge('business_name')" :show="he('business_name')" />
                                    </div>
                                    <div>
                                        <label class="field-label">Category <span class="req">*</span></label>
                                        <select v-model="form.vendor_category_id" @blur="touch('vendor_category_id')" class="ep-input ep-select" :class="fc('vendor_category_id')">
                                            <option value="">Select Category</option>
                                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                        </select>
                                        <FE :msg="ge('vendor_category_id')" :show="he('vendor_category_id')" />
                                    </div>
                                    <div>
                                        <label class="field-label">Website <span class="opt">(optional)</span></label>
                                        <input v-model="form.website" @blur="touch('website')" type="text" placeholder="https://yourwebsite.com" class="ep-input" :class="fc('website')">
                                        <FE :msg="ge('website')" :show="he('website')" />
                                    </div>
                                    <div class="col-span-2">
                                        <label class="field-label">Description <span class="opt">(optional)</span></label>
                                        <textarea v-model="form.description" rows="4" placeholder="Describe the vendor's services and expertise…" class="ep-input ep-textarea"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="ep-card form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(240,90,0,.1)">📞</span>
                                <div>
                                    <div class="card-title">Contact Information</div>
                                    <div class="card-sub">Who to reach for this vendor</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field-grid col-3">
                                    <div>
                                        <label class="field-label">Contact Person <span class="req">*</span></label>
                                        <input v-model="form.contact_person" @blur="touch('contact_person')" type="text" placeholder="Full name" class="ep-input" :class="fc('contact_person')">
                                        <FE :msg="ge('contact_person')" :show="he('contact_person')" />
                                    </div>
                                    <div>
                                        <label class="field-label">Email <span class="req">*</span></label>
                                        <input v-model="form.email" @blur="touch('email')" type="email" placeholder="vendor@example.com" class="ep-input" :class="fc('email')">
                                        <FE :msg="ge('email')" :show="he('email')" />
                                    </div>
                                    <div>
                                        <label class="field-label">Phone <span class="req">*</span></label>
                                        <input v-model="form.phone" @blur="touch('phone')" type="tel" placeholder="+255 712 345 678" class="ep-input" :class="fc('phone')">
                                        <FE :msg="ge('phone')" :show="he('phone')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="ep-card form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(249,178,51,.12)">📍</span>
                                <div>
                                    <div class="card-title">Location</div>
                                    <div class="card-sub">Where this vendor is based</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field-grid">
                                    <div class="col-span-2">
                                        <label class="field-label">Address <span class="req">*</span></label>
                                        <input v-model="form.address" @blur="touch('address')" type="text" placeholder="Street address" class="ep-input" :class="fc('address')">
                                        <FE :msg="ge('address')" :show="he('address')" />
                                    </div>
                                    <div>
                                        <label class="field-label">City <span class="req">*</span></label>
                                        <input v-model="form.city" @blur="touch('city')" type="text" placeholder="City" class="ep-input" :class="fc('city')">
                                        <FE :msg="ge('city')" :show="he('city')" />
                                    </div>
                                    <div>
                                        <label class="field-label">State / Region <span class="req">*</span></label>
                                        <input v-model="form.state" @blur="touch('state')" type="text" placeholder="State" class="ep-input" :class="fc('state')">
                                        <FE :msg="ge('state')" :show="he('state')" />
                                    </div>
                                    <div>
                                        <label class="field-label">Country <span class="req">*</span></label>
                                        <input v-model="form.country" @blur="touch('country')" type="text" placeholder="Country" class="ep-input" :class="fc('country')">
                                        <FE :msg="ge('country')" :show="he('country')" />
                                    </div>
                                    <div>
                                        <label class="field-label">Postal Code <span class="opt">(optional)</span></label>
                                        <input v-model="form.postal_code" type="text" placeholder="Postal Code" class="ep-input ep-input-plain">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Details -->
                        <div class="ep-card form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(22,163,74,.1)">📄</span>
                                <div>
                                    <div class="card-title">Business Details</div>
                                    <div class="card-sub">Registration, financials and team info</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field-grid col-3">
                                    <div>
                                        <label class="field-label">Reg. Number <span class="opt">(optional)</span></label>
                                        <input v-model="form.business_registration_number" type="text" placeholder="e.g. TZ123456" class="ep-input ep-input-plain">
                                    </div>
                                    <div>
                                        <label class="field-label">Tax ID <span class="opt">(optional)</span></label>
                                        <input v-model="form.tax_id" type="text" placeholder="e.g. TIN123456789" class="ep-input ep-input-plain">
                                    </div>
                                    <div>
                                        <label class="field-label">Min. Order Value <span class="opt">(TZS)</span></label>
                                        <div class="prefix-wrap">
                                            <span class="prefix-label">TZS</span>
                                            <input v-model="form.minimum_order_value" @blur="touch('minimum_order_value')" type="number" min="0" step="1000" placeholder="0" class="ep-input ep-input-prefix" :class="fc('minimum_order_value')">
                                        </div>
                                        <FE :msg="ge('minimum_order_value')" :show="he('minimum_order_value')" />
                                    </div>
                                    <div>
                                        <label class="field-label">Years of Experience <span class="opt">(optional)</span></label>
                                        <input v-model="form.years_of_experience" @blur="touch('years_of_experience')" type="number" min="0" placeholder="e.g. 5" class="ep-input" :class="fc('years_of_experience')">
                                        <FE :msg="ge('years_of_experience')" :show="he('years_of_experience')" />
                                    </div>
                                    <div>
                                        <label class="field-label">Team Size <span class="opt">(optional)</span></label>
                                        <input v-model="form.team_size" @blur="touch('team_size')" type="number" min="1" placeholder="e.g. 10" class="ep-input" :class="fc('team_size')">
                                        <FE :msg="ge('team_size')" :show="he('team_size')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Service Areas & Specializations -->
                        <div class="ep-card form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(192,23,15,.08)">🗺️</span>
                                <div>
                                    <div class="card-title">Service Areas &amp; Specializations</div>
                                    <div class="card-sub">Where they operate and what they do best</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field-grid">
                                    <!-- Service Areas -->
                                    <div>
                                        <label class="field-label">Service Areas</label>
                                        <div class="tag-input-row">
                                            <input v-model="newServiceArea" @keydown.enter.prevent="addServiceArea" type="text" placeholder="Add area, press Enter" class="ep-input ep-input-plain tag-input">
                                            <button type="button" @click="addServiceArea" class="tag-add-btn">Add</button>
                                        </div>
                                        <div class="tag-cloud">
                                            <span v-for="(area, i) in form.service_areas" :key="i" class="tag-navy">
                                                {{ area }}
                                                <button type="button" @click="removeServiceArea(i)" class="tag-rm">✕</button>
                                            </span>
                                            <span v-if="!form.service_areas.length" class="tag-empty">None added yet</span>
                                        </div>
                                    </div>
                                    <!-- Specializations -->
                                    <div>
                                        <label class="field-label">Specializations</label>
                                        <div class="tag-input-row">
                                            <input v-model="newSpecialization" @keydown.enter.prevent="addSpecialization" type="text" placeholder="Add specialization, press Enter" class="ep-input ep-input-plain tag-input">
                                            <button type="button" @click="addSpecialization" class="tag-add-btn">Add</button>
                                        </div>
                                        <div class="tag-cloud">
                                            <span v-for="(spec, i) in form.specializations" :key="i" class="tag-amber">
                                                {{ spec }}
                                                <button type="button" @click="removeSpecialization(i)" class="tag-rm">✕</button>
                                            </span>
                                            <span v-if="!form.specializations.length" class="tag-empty">None added yet</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- ═══ STICKY SIDEBAR ═══ -->
                <div class="form-sidebar">

                    <!-- Vendor Summary -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(192,23,15,.08)">📋</span>
                            <div><div class="card-title">Vendor Summary</div></div>
                        </div>
                        <div class="dl-list">
                            <div class="dl-row">
                                <span class="dl-label">Name</span>
                                <span class="dl-value">{{ form.business_name || '—' }}</span>
                            </div>
                            <div class="dl-row">
                                <span class="dl-label">Contact</span>
                                <span class="dl-value">{{ form.contact_person || '—' }}</span>
                            </div>
                            <div class="dl-row">
                                <span class="dl-label">City</span>
                                <span class="dl-value">{{ form.city || '—' }}</span>
                            </div>
                            <div class="dl-row">
                                <span class="dl-label">Min. Order</span>
                                <span class="dl-value" style="color:#C0170F">{{ form.minimum_order_value ? 'TZS ' + formatPrice(form.minimum_order_value) : '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Settings toggles -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(29,92,150,.1)">⚙️</span>
                            <div><div class="card-title">Settings</div></div>
                        </div>
                        <div style="padding:14px 16px;display:flex;flex-direction:column;gap:12px">
                            <label v-for="toggle in toggleSettings" :key="toggle.key" class="toggle-row">
                                <div class="toggle-track" @click="form[toggle.key] = !form[toggle.key]" :class="form[toggle.key] ? 'track-on' : 'track-off'">
                                    <div class="toggle-thumb" :class="form[toggle.key] ? 'thumb-on' : 'thumb-off'"></div>
                                </div>
                                <div>
                                    <div class="toggle-label">{{ toggle.label }}</div>
                                    <div class="toggle-desc">{{ toggle.description }}</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="sidebar-actions">
                        <button type="button" @click="submit" :disabled="form.processing" class="btn-cta btn-full">
                            <svg v-if="form.processing" class="spin-icon" width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,.3)" stroke-width="3"/><path d="M12 2a10 10 0 0 1 10 10" stroke="white" stroke-width="3" stroke-linecap="round"/></svg>
                            <svg v-else width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            {{ form.processing ? 'Creating…' : 'Create Vendor' }}
                        </button>
                        <Link :href="route('vendors.index')" class="btn-ghost btn-full">Cancel</Link>
                    </div>

                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, reactive, computed, defineComponent, h } from 'vue'

const props = defineProps({ categories: Array })

// ── Inline error component ────────────────────────────────────────────────────
const FE = defineComponent({
    props: { msg: String, show: Boolean },
    setup(p) {
        return () => p.show && p.msg
            ? h('p', { style: 'margin-top:4px;font-size:11px;color:#C0170F;display:flex;align-items:center;gap:4px;font-family:DM Mono,monospace' }, [
                h('svg', { width: '11', height: '11', fill: 'currentColor', viewBox: '0 0 20 20', style:'flex-shrink:0' }, [
                    h('path', { 'fill-rule': 'evenodd', 'clip-rule': 'evenodd', d: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' })
                ]),
                p.msg
            ]) : null
    }
})

// ── Form ─────────────────────────────────────────────────────────────────────
const form = useForm({
    vendor_category_id:           '',
    business_name:                '',
    description:                  '',
    contact_person:               '',
    email:                        '',
    phone:                        '',
    website:                      '',
    address:                      '',
    city:                         '',
    state:                        '',
    country:                      '',
    postal_code:                  '',
    business_registration_number: '',
    tax_id:                       '',
    minimum_order_value:          '',
    years_of_experience:          '',
    team_size:                    '',
    service_areas:                [],
    specializations:              [],
    is_active:                    true,
    is_featured:                  false,
})

// ── Tag inputs ────────────────────────────────────────────────────────────────
const newServiceArea    = ref('')
const newSpecialization = ref('')
const addServiceArea    = () => { const v = newServiceArea.value.trim(); if (v && !form.service_areas.includes(v)) form.service_areas.push(v); newServiceArea.value = '' }
const removeServiceArea = (i) => form.service_areas.splice(i, 1)
const addSpecialization = () => { const v = newSpecialization.value.trim(); if (v && !form.specializations.includes(v)) form.specializations.push(v); newSpecialization.value = '' }
const removeSpecialization = (i) => form.specializations.splice(i, 1)

// ── Validation ────────────────────────────────────────────────────────────────
const touched     = reactive({})
const showSummary = ref(false)

const rules = {
    vendor_category_id: v => !v ? 'Category is required.' : null,
    business_name: v => { if (!v?.trim()) return 'Business name is required.'; if (v.trim().length > 255) return 'Business name must be 255 characters or fewer.'; return null },
    contact_person: v => !v?.trim() ? 'Contact person is required.' : null,
    email: v => { if (!v?.trim()) return 'Email address is required.'; if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Please enter a valid email address.'; return null },
    phone: v => { if (!v?.trim()) return 'Phone number is required.'; if (!/^[\d\s+\-()]{7,20}$/.test(v)) return 'Please enter a valid phone number.'; return null },
    address: v => !v?.trim() ? 'Address is required.' : null,
    city:    v => !v?.trim() ? 'City is required.' : null,
    state:   v => !v?.trim() ? 'State / Region is required.' : null,
    country: v => !v?.trim() ? 'Country is required.' : null,
    website: v => { if (!v?.trim()) return null; try { new URL(v); return null } catch { return 'Please enter a valid URL (e.g. https://example.com).' } },
    minimum_order_value: v => (v === '' || v === null || v === undefined) ? null : Number(v) < 0 ? 'Minimum order value cannot be negative.' : null,
    years_of_experience: v => (v === '' || v === null || v === undefined) ? null : (!Number.isInteger(Number(v)) || Number(v) < 0) ? 'Years of experience must be a non-negative integer.' : null,
    team_size: v => (v === '' || v === null || v === undefined) ? null : (!Number.isInteger(Number(v)) || Number(v) < 1) ? 'Team size must be at least 1.' : null,
}

const clientErrors = computed(() => { const e = {}; for (const [f, r] of Object.entries(rules)) { const m = r(form[f]); if (m) e[f] = m }; return e })
const touch    = f => { touched[f] = true }
const touchAll = () => Object.keys(rules).forEach(f => { touched[f] = true })
const he = f => (touched[f] || showSummary.value) && !!clientErrors.value[f]
const ge = f => form.errors[f] || clientErrors.value[f]
const fc = f => he(f) ? 'ep-input-err' : ''

const submit = () => {
    touchAll()
    showSummary.value = true
    if (Object.keys(clientErrors.value).length > 0) {
        setTimeout(() => { const el = document.querySelector('.ep-input-err'); if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' }) }, 50)
        return
    }
    form.post(route('vendors.store'))
}

const toggleSettings = [
    { key: 'is_active',   label: 'Active',   description: 'Vendor is visible and bookable' },
    { key: 'is_featured', label: 'Featured', description: 'Highlight in featured sections' },
]

const formatPrice = n => n ? Number(n).toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:0}) : '0'
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}
.page-header{margin-bottom:22px}
.breadcrumb{display:flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;margin-bottom:6px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C0B8}.bc-cur{color:#6B6560}
.eyebrow-row{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(22px,3vw,28px);font-weight:900;color:#1A1410;line-height:1.15;margin-bottom:4px}
.page-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}

/* ── Validation summary ── */
.val-summary{display:flex;align-items:flex-start;gap:12px;padding:14px 18px;background:rgba(192,23,15,.05);border:1px solid rgba(192,23,15,.25);border-radius:14px;margin-bottom:18px}
.val-icon{width:32px;height:32px;border-radius:9px;background:rgba(192,23,15,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.val-title{font-size:12px;font-weight:700;color:#C0170F;margin-bottom:6px}
.val-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:3px}
.val-item{display:flex;align-items:center;gap:6px;font-family:'DM Mono',monospace;font-size:10px;color:#C0170F}
.val-dot{width:4px;height:4px;border-radius:50%;background:#C0170F;flex-shrink:0}

/* ── Layout ── */
.form-layout{display:grid;grid-template-columns:1fr 280px;gap:18px;align-items:start}
@media(max-width:1024px){.form-layout{grid-template-columns:1fr}}
.form-main{display:flex;flex-direction:column;gap:16px}
.form-sidebar{position:sticky;top:24px;display:flex;flex-direction:column;gap:14px}

/* ── Cards ── */
.ep-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04)}
.form-card{}
.card-head{display:flex;align-items:center;gap:12px;padding:13px 18px;background:#F0EDE8;border-bottom:1px solid #E8E2DA}
.card-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.card-title{font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:#1A1410;line-height:1.2}
.card-sub{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:1px}
.card-body{padding:18px}

/* ── Fields ── */
.field-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.col-3{grid-template-columns:1fr 1fr 1fr}
@media(max-width:700px){.field-grid,.col-3{grid-template-columns:1fr}}
.col-span-2{grid-column:span 2}
@media(max-width:700px){.col-span-2{grid-column:span 1}}
.field-label{display:block;font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:#6B6560;font-weight:700;margin-bottom:6px}
.req{color:#C0170F;font-style:normal}
.opt{color:#C8C0B8;font-style:normal;text-transform:none;letter-spacing:0;font-weight:400;font-size:10px}
.ep-input{width:100%;padding:9px 12px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:12px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
.ep-input:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.ep-input::placeholder{color:#C8C0B8}
.ep-input-plain:focus{border-color:#1D5C96;box-shadow:0 0 0 3px rgba(29,92,150,.09)}
.ep-input-err{border-color:rgba(192,23,15,.5)!important;background:rgba(192,23,15,.03)!important}
.ep-input-err:focus{border-color:#C0170F!important;box-shadow:0 0 0 3px rgba(192,23,15,.12)!important}
.ep-select{cursor:pointer}
.ep-textarea{resize:none;line-height:1.6}
.prefix-wrap{position:relative}
.prefix-label{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;pointer-events:none;font-weight:700}
.ep-input-prefix{padding-left:38px}

/* ── Tags ── */
.tag-input-row{display:flex;gap:8px;margin-bottom:8px}
.tag-input{flex:1}
.tag-add-btn{padding:9px 14px;border-radius:10px;border:1.5px solid rgba(192,23,15,.3);background:rgba(192,23,15,.06);color:#C0170F;font-family:'DM Mono',monospace;font-size:10px;font-weight:700;cursor:pointer;transition:all .15s;white-space:nowrap}
.tag-add-btn:hover{background:rgba(192,23,15,.1)}
.tag-cloud{display:flex;flex-wrap:wrap;gap:5px;min-height:24px}
.tag-navy{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;background:rgba(29,92,150,.1);color:#1D5C96;border:1px solid rgba(29,92,150,.25);font-family:'DM Mono',monospace;font-size:9px;font-weight:700}
.tag-amber{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;background:rgba(249,178,51,.12);color:#b45309;border:1px solid rgba(249,178,51,.3);font-family:'DM Mono',monospace;font-size:9px;font-weight:700}
.tag-rm{background:none;border:none;cursor:pointer;font-size:9px;opacity:.6;padding:0;line-height:1;transition:opacity .15s}
.tag-rm:hover{opacity:1}
.tag-empty{font-family:'DM Mono',monospace;font-size:10px;color:#C8C0B8}

/* ── Sidebar DL ── */
.dl-list{padding:0 16px;display:flex;flex-direction:column}
.dl-row{display:flex;align-items:flex-start;justify-content:space-between;gap:8px;padding:8px 0;border-bottom:1px solid #F7F5F2}
.dl-row:last-child{border-bottom:none}
.dl-label{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890;font-weight:700;flex-shrink:0;padding-top:1px}
.dl-value{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#1A1410;text-align:right;word-break:break-word;max-width:140px}

/* ── Toggles ── */
.toggle-row{display:flex;align-items:center;gap:10px;cursor:pointer}
.toggle-track{width:40px;height:22px;border-radius:11px;position:relative;cursor:pointer;transition:background .2s;flex-shrink:0}
.track-on{background:linear-gradient(135deg,#C0170F,#F05A00)}
.track-off{background:#E8E2DA}
.toggle-thumb{position:absolute;top:2px;width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.18);transition:left .2s}
.thumb-on{left:20px}
.thumb-off{left:2px}
.toggle-label{font-size:12px;font-weight:700;color:#1A1410}
.toggle-desc{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:1px}

/* ── Buttons ── */
.btn-cta{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:11px 18px;border-radius:12px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-cta:disabled{opacity:.55;cursor:not-allowed;transform:none}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-full{width:100%;text-align:center}
.btn-ghost{display:inline-flex;align-items:center;justify-content:center;padding:10px 18px;border-radius:12px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;text-decoration:none;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.sidebar-actions{display:flex;flex-direction:column;gap:8px}
.spin-icon{animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
</style>