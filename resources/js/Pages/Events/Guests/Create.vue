<template>
    <AuthenticatedLayout>

        <div class="confetti" aria-hidden="true">
            <div class="cdot" v-for="n in 14" :key="n" :style="{
                width:(3+(n*3)%9)+'px',height:(3+(n*3)%9)+'px',left:(n*7.1%100)+'%',
                background:['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233'][n%6],
                animationDuration:(10+n*1.1)+'s',animationDelay:(n*.6)+'s',
                borderRadius:n%3===0?'2px':'50%',
            }"></div>
        </div>

        <div class="page-wrap">

            <!-- header -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('events.guests.index',event.id)" class="bc-link">Guests</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">Add Guests</span>
                    </div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Add Guests</div>
                    <h1 class="page-title">{{ event.title }}</h1>
                </div>
                <div class="header-right">
                    <div v-if="form.guests.length" class="guest-count-pill">
                        {{ form.guests.length }} guest{{ form.guests.length!==1?'s':'' }} ready
                    </div>
                    <Link :href="route('events.guests.index',event.id)" class="btn-ghost">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                        Back
                    </Link>
                </div>
            </div>

            <!-- tab bar -->
            <div class="tab-bar">
                <button @click="activeTab='manual'" :class="['tab-btn', activeTab==='manual'?'active':'']">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Manual Entry
                </button>
                <button @click="activeTab='excel'" :class="['tab-btn', activeTab==='excel'?'active':'']">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Import Excel / CSV
                </button>
            </div>

            <!-- ── MANUAL TAB ── -->
            <template v-if="activeTab==='manual'">
                <div class="form-card">
                    <div class="form-card-head">
                        <div class="card-icon-wrap" style="background:rgba(192,23,15,.1)">⚙️</div>
                        <div>
                            <div class="card-title">Bulk Settings</div>
                            <div class="card-sub">These settings apply to all guests you add below</div>
                        </div>
                    </div>
                    <div class="fields">
                        <div class="field-row-3">
                            <div class="field">
                                <label class="field-label">Category</label>
                                <select v-model="bulkSettings.category" class="field-input">
                                    <option value="family">Family</option><option value="friends">Friends</option>
                                    <option value="colleagues">Colleagues</option><option value="business">Business</option>
                                    <option value="vip">VIP</option><option value="sponsors">Sponsors</option>
                                    <option value="media">Media</option><option value="other">Other</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">Guest Type</label>
                                <select v-model="bulkSettings.guest_type" class="field-input">
                                    <option value="primary">Primary Guest</option><option value="plus_one">Plus One</option>
                                    <option value="child">Child</option><option value="vendor">Vendor</option>
                                    <option value="staff">Staff</option><option value="speaker">Speaker</option>
                                    <option value="performer">Performer</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">RSVP Status</label>
                                <select v-model="bulkSettings.rsvp_status" class="field-input">
                                    <option value="pending">Pending</option><option value="attending">Attending</option>
                                    <option value="not_attending">Not Attending</option><option value="maybe">Maybe</option>
                                </select>
                            </div>
                        </div>
                        <div class="field-row-4">
                            <div class="toggle-row">
                                <span class="toggle-lbl">Mark as VIP</span>
                                <div @click="bulkSettings.is_vip=!bulkSettings.is_vip" class="toggle-wrap">
                                    <div :class="['toggle', bulkSettings.is_vip?'on':'']"><div class="toggle-knob"></div></div>
                                </div>
                            </div>
                            <div class="toggle-row">
                                <span class="toggle-lbl">Allow Plus One</span>
                                <div @click="bulkSettings.plus_one_allowed=!bulkSettings.plus_one_allowed" class="toggle-wrap">
                                    <div :class="['toggle', bulkSettings.plus_one_allowed?'on':'']"><div class="toggle-knob"></div></div>
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-label">Language</label>
                                <select v-model="bulkSettings.language_preference" class="field-input">
                                    <option value="en">English</option><option value="es">Spanish</option>
                                    <option value="fr">French</option><option value="de">German</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">Plus Ones</label>
                                <input type="number" v-model="bulkSettings.plus_ones" min="0" max="10" class="field-input">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-head">
                        <div class="card-icon-wrap" style="background:rgba(29,92,150,.1)">👤</div>
                        <div>
                            <div class="card-title">Guest Names</div>
                            <div class="card-sub">Add first name, last name and optionally email &amp; phone</div>
                        </div>
                    </div>
                    <div class="fields">
                        <div class="guest-rows">
                            <div v-for="(guest,idx) in tempGuests" :key="idx" class="guest-row">
                                <div class="row-num">{{ idx+1 }}</div>
                                <input type="text" v-model="guest.first_name" placeholder="First name" class="field-input">
                                <input type="text" v-model="guest.last_name"  placeholder="Last name"  class="field-input">
                                <input type="email" v-model="guest.email"    placeholder="Email (optional)" class="field-input">
                                <input type="text" v-model="guest.phone"     placeholder="Phone (optional)" class="field-input">
                                <button v-if="idx>0" @click="removeTempGuest(idx)" type="button" class="row-remove">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                </button>
                                <div v-else style="width:28px"></div>
                            </div>
                        </div>
                        <div class="row-actions">
                            <button @click="addTempGuest" type="button" class="btn-add-row">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                Add another row
                            </button>
                            <button @click="addGuestsToList" type="button" class="btn-cta">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                Add to Guest List
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ── EXCEL TAB ── -->
            <template v-if="activeTab==='excel'">
                <div class="form-card">
                    <div class="form-card-head">
                        <div class="card-icon-wrap" style="background:rgba(22,163,74,.1)">📊</div>
                        <div>
                            <div class="card-title">Import from Excel or CSV</div>
                            <div class="card-sub">Upload .xlsx, .xls, or .csv · Max 5 MB</div>
                        </div>
                        <div style="margin-left:auto;display:flex;gap:8px;flex-shrink:0">
                            <a :href="`/events/${event.id}/guests/import-template?format=xlsx`" class="btn-template green">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                .xlsx Template
                            </a>
                            <a :href="`/events/${event.id}/guests/import-template?format=csv`" class="btn-template blue">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                .csv Template
                            </a>
                        </div>
                    </div>
                    <div class="fields">
                        <!-- column reference -->
                        <div class="col-ref">
                            <div class="col-ref-title">Expected Columns</div>
                            <div class="col-ref-grid">
                                <span><strong class="required">first_name *</strong></span>
                                <span><strong class="required">last_name *</strong></span>
                                <span>email</span><span>phone</span><span>category</span>
                                <span>guest_type</span><span>rsvp_status</span><span>is_vip</span>
                                <span>plus_one_allowed</span><span>plus_ones</span>
                                <span>language_preference</span><span>notes</span>
                            </div>
                            <div class="col-ref-note">* Required · All others optional</div>
                        </div>

                        <!-- drop zone -->
                        <div @dragover.prevent="isDragging=true" @dragleave.prevent="isDragging=false"
                             @drop.prevent="handleFileDrop" @click="$refs.fileInput.click()"
                             :class="['drop-zone', isDragging?'dragging':excelFile?'has-file':'']">
                            <input ref="fileInput" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="handleFileSelect">
                            <template v-if="!excelFile">
                                <div class="drop-icon">📁</div>
                                <div class="drop-title">Drop your file here, or <span class="drop-link">browse</span></div>
                                <div class="drop-sub">Supports .xlsx, .xls, .csv · Max 5 MB</div>
                            </template>
                            <template v-else>
                                <div class="file-preview">
                                    <div class="file-icon">{{ excelFile.name.endsWith('.csv')?'📄':'📊' }}</div>
                                    <div>
                                        <div class="file-name">{{ excelFile.name }}</div>
                                        <div class="file-size">{{ fmtSize(excelFile.size) }}</div>
                                    </div>
                                    <button @click.stop="clearExcelFile" class="file-clear">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- errors -->
                        <div v-if="importErrors.length" class="import-errors">
                            <div class="ie-title">⚠️ Import issues:</div>
                            <div v-for="(e,i) in importErrors" :key="i" class="ie-row">• {{ e }}</div>
                        </div>

                        <!-- preview table -->
                        <div v-if="importPreview.length">
                            <div class="preview-header">
                                <span class="preview-title">{{ importPreview.length }} guest{{ importPreview.length!==1?'s':'' }} ready
                                    <span v-if="importSkipped" class="skip-note">({{ importSkipped }} skipped – missing name)</span>
                                </span>
                                <span class="preview-sub">Showing first 10</span>
                            </div>
                            <div class="preview-table-wrap">
                                <table class="preview-table">
                                    <thead><tr><th>Name</th><th>Email</th><th>Category</th><th>RSVP</th><th>VIP</th></tr></thead>
                                    <tbody>
                                        <tr v-for="(g,i) in importPreview.slice(0,10)" :key="i">
                                            <td class="font-semibold">{{ g.first_name }} {{ g.last_name }}</td>
                                            <td>{{ g.email||'—' }}</td>
                                            <td><span class="cat-chip capitalize" :style="CAT_STYLE[g.category]||CAT_STYLE.other">{{ g.category }}</span></td>
                                            <td><span class="cat-chip capitalize" :style="RSVP_STYLE[g.rsvp_status]||RSVP_STYLE.pending">{{ g.rsvp_status }}</span></td>
                                            <td><span v-if="g.is_vip" class="vip-chip">VIP</span><span v-else style="color:#C8C2BA">—</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="importPreview.length>10" class="preview-more">+ {{ importPreview.length-10 }} more not shown</div>
                        </div>

                        <div class="import-actions">
                            <button @click="parseExcelFile" :disabled="!excelFile||isParsing" class="btn-secondary">
                                <svg v-if="isParsing" class="spin-icon" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                <svg v-else width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ isParsing?'Parsing…':'Preview File' }}
                            </button>
                            <button v-if="importPreview.length" @click="addImportedGuestsToList" class="btn-cta">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                Add {{ importPreview.length }} Guest{{ importPreview.length!==1?'s':'' }} to List
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ── GUEST LIST PREVIEW ── -->
            <div v-if="form.guests.length" class="form-card">
                <div class="form-card-head">
                    <div class="card-icon-wrap" style="background:rgba(249,178,51,.15)">📋</div>
                    <div>
                        <div class="card-title">Guest List Preview</div>
                        <div class="card-sub">Review before saving</div>
                    </div>
                    <span class="guest-count-pill ml-auto">{{ form.guests.length }} guest{{ form.guests.length!==1?'s':'' }} ready</span>
                </div>
                <div class="table-scroll">
                    <table class="ep-table">
                        <thead><tr>
                            <th>Name</th><th>Email</th><th>Category</th><th>RSVP</th><th>VIP</th><th class="th-actions">Actions</th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="(g,idx) in form.guests" :key="idx" class="tr-row">
                                <td>
                                    <div class="font-semibold" style="font-size:13px;color:#1A1410">{{ g.first_name }} {{ g.last_name }}</div>
                                    <div v-if="g.phone" style="font-size:11px;color:#9E9890;font-family:'DM Mono',monospace">{{ g.phone }}</div>
                                </td>
                                <td style="font-size:13px;color:#6B6560">{{ g.email||'No email' }}</td>
                                <td><span class="cat-chip capitalize" :style="CAT_STYLE[g.category]||CAT_STYLE.other">{{ g.category }}</span></td>
                                <td><span class="cat-chip capitalize" :style="RSVP_STYLE[g.rsvp_status]||RSVP_STYLE.pending">{{ g.rsvp_status }}</span></td>
                                <td>
                                    <span v-if="g.is_vip" class="vip-chip">VIP</span>
                                    <span v-else style="color:#C8C2BA;font-size:12px">—</span>
                                </td>
                                <td class="td-actions">
                                    <button @click="editGuest(idx)" class="action-btn edit">Edit</button>
                                    <button @click="removeGuest(idx)" class="action-btn del">Remove</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- edit modal -->
            <Modal :show="showEditModal" @close="showEditModal=false" max-width="lg">
                <div class="modal-wrap" v-if="editingGuest!==null">
                    <div class="modal-header">
                        <h2 class="modal-title">Edit Guest</h2>
                        <button @click="showEditModal=false" class="modal-close">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="fields">
                        <div class="field-row">
                            <div class="field"><label class="field-label">First Name</label><input type="text" v-model="editingGuest.first_name" class="field-input"></div>
                            <div class="field"><label class="field-label">Last Name</label><input type="text" v-model="editingGuest.last_name" class="field-input"></div>
                        </div>
                        <div class="field-row">
                            <div class="field"><label class="field-label">Email</label><input type="email" v-model="editingGuest.email" class="field-input"></div>
                            <div class="field"><label class="field-label">Phone</label><input type="tel" v-model="editingGuest.phone" class="field-input"></div>
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label class="field-label">Category</label>
                                <select v-model="editingGuest.category" class="field-input">
                                    <option value="family">Family</option><option value="friends">Friends</option>
                                    <option value="colleagues">Colleagues</option><option value="business">Business</option>
                                    <option value="vip">VIP</option><option value="sponsors">Sponsors</option>
                                    <option value="media">Media</option><option value="other">Other</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">RSVP Status</label>
                                <select v-model="editingGuest.rsvp_status" class="field-input">
                                    <option value="pending">Pending</option><option value="attending">Attending</option>
                                    <option value="not_attending">Not Attending</option><option value="maybe">Maybe</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer" style="margin-top:8px">
                            <button @click="showEditModal=false" class="btn-ghost">Cancel</button>
                            <button @click="saveGuestEdit" class="btn-cta">Save Changes</button>
                        </div>
                    </div>
                </div>
            </Modal>

            <!-- ── FINALIZE ── -->
            <div v-if="form.guests.length" class="form-card">
                <div class="form-card-head">
                    <div class="card-icon-wrap" style="background:rgba(192,23,15,.08)">🚀</div>
                    <div>
                        <div class="card-title">Finalize &amp; Save</div>
                        <div class="card-sub">Configure options then save all {{ form.guests.length }} guests</div>
                    </div>
                </div>
                <div class="fields">
                    <div class="toggle-row full">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#1A1410">Send invitations immediately</div>
                            <div style="font-size:11px;color:#9E9890;font-family:'DM Mono',monospace">
                                Will send to {{ guestsWithEmail }} guest{{ guestsWithEmail!==1?'s':'' }} with email addresses
                            </div>
                        </div>
                        <div @click="form.send_invitations=!form.send_invitations" class="toggle-wrap">
                            <div :class="['toggle', form.send_invitations?'on':'']"><div class="toggle-knob"></div></div>
                        </div>
                    </div>
                    <div v-if="form.send_invitations" class="field" style="max-width:260px">
                        <label class="field-label">Invitation Method</label>
                        <select v-model="form.invitation_method" class="field-input">
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field-label">Notes (applies to all guests)</label>
                        <textarea v-model="form.notes" rows="2" class="field-input" placeholder="Any shared notes…"></textarea>
                    </div>
                    <div class="finalize-actions">
                        <button @click="clearAllGuests" type="button" class="btn-danger-outline">
                            🗑️ Clear All Guests
                        </button>
                        <button @click="submit" :disabled="isSubmitting" class="btn-cta">
                            <svg v-if="isSubmitting" class="spin-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                            <svg v-else width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            {{ isSubmitting?'Saving…':`Save ${form.guests.length} Guest${form.guests.length!==1?'s':''}` }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'
import * as XLSX from 'xlsx'

const props = defineProps({ event: Object })
const page  = usePage()

const activeTab      = ref('manual')
const showEditModal  = ref(false)
const editingIndex   = ref(null)
const editingGuest   = ref(null)
const isSubmitting   = ref(false)
const fileInput      = ref(null)
const excelFile      = ref(null)
const isDragging     = ref(false)
const isParsing      = ref(false)
const importPreview  = ref([])
const importErrors   = ref([])
const importSkipped  = ref(0)

const bulkSettings = ref({
    category:'friends', guest_type:'primary', rsvp_status:'pending',
    is_vip:false, plus_one_allowed:false, plus_ones:0, language_preference:'en'
})
const tempGuests = ref([{ first_name:'', last_name:'', email:'', phone:'' }])
const form = ref({ guests:[], send_invitations:false, invitation_method:'email', notes:'' })

const guestsWithEmail = computed(() => form.value.guests.filter(g=>g.email?.trim()).length)

const CAT_STYLE = {
    vip:{background:'rgba(249,178,51,.15)',color:'#b45309',border:'1px solid rgba(249,178,51,.4)'},
    family:{background:'rgba(124,58,237,.1)',color:'#7c3aed',border:'1px solid rgba(124,58,237,.25)'},
    friends:{background:'rgba(29,92,150,.1)',color:'#1D5C96',border:'1px solid rgba(29,92,150,.25)'},
    colleagues:{background:'rgba(22,163,74,.1)',color:'#16a34a',border:'1px solid rgba(22,163,74,.25)'},
    business:{background:'rgba(29,92,150,.1)',color:'#1D5C96',border:'1px solid rgba(29,92,150,.25)'},
    media:{background:'rgba(219,39,119,.1)',color:'#db2777',border:'1px solid rgba(219,39,119,.25)'},
    sponsors:{background:'rgba(192,23,15,.08)',color:'#C0170F',border:'1px solid rgba(192,23,15,.2)'},
    other:{background:'rgba(158,152,144,.12)',color:'#6B6560',border:'1px solid rgba(158,152,144,.3)'},
}
const RSVP_STYLE = {
    pending:{background:'rgba(249,178,51,.12)',color:'#b45309',border:'1px solid rgba(249,178,51,.3)'},
    attending:{background:'rgba(22,163,74,.1)',color:'#16a34a',border:'1px solid rgba(22,163,74,.25)'},
    not_attending:{background:'rgba(192,23,15,.08)',color:'#C0170F',border:'1px solid rgba(192,23,15,.2)'},
    maybe:{background:'rgba(29,92,150,.1)',color:'#1D5C96',border:'1px solid rgba(29,92,150,.2)'},
}

const VALID_CATEGORIES  = ['family','friends','colleagues','business','vip','sponsors','media','other']
const VALID_GUEST_TYPES = ['primary','plus_one','child','vendor','staff','speaker','performer']
const VALID_RSVP        = ['pending','attending','not_attending','maybe']
const VALID_LANGUAGES   = ['en','es','fr','de']

const normalizeBoolean = v => v===true||String(v).toLowerCase()==='true'||v===1
const normalizeRow = row => ({
    first_name: String(row.first_name||row['first_name *']||'').trim(),
    last_name:  String(row.last_name ||row['last_name *'] ||'').trim(),
    email:      String(row.email||'').trim(), phone: String(row.phone||'').trim(),
    category:   VALID_CATEGORIES.includes(String(row.category||'').toLowerCase())?String(row.category).toLowerCase():'friends',
    guest_type: VALID_GUEST_TYPES.includes(String(row.guest_type||'').toLowerCase())?String(row.guest_type).toLowerCase():'primary',
    rsvp_status:VALID_RSVP.includes(String(row.rsvp_status||'').toLowerCase())?String(row.rsvp_status).toLowerCase():'pending',
    is_vip:     normalizeBoolean(row.is_vip), plus_one_allowed: normalizeBoolean(row.plus_one_allowed),
    plus_ones:  Math.min(10,Math.max(0,parseInt(row.plus_ones)||0)),
    language_preference: VALID_LANGUAGES.includes(String(row.language_preference||'').toLowerCase())?String(row.language_preference).toLowerCase():'en',
    notes: String(row.notes||'').trim(),
})

const handleFileDrop = e => { isDragging.value=false; const f=e.dataTransfer.files[0]; if(f)setExcelFile(f) }
const handleFileSelect = e => { const f=e.target.files[0]; if(f)setExcelFile(f) }
const setExcelFile = f => {
    const ext=f.name.split('.').pop().toLowerCase()
    if(!['xlsx','xls','csv'].includes(ext)){ Swal.fire({title:'Unsupported',text:'Upload .xlsx, .xls or .csv',icon:'warning',confirmButtonColor:'#C0170F'}); return }
    if(f.size>5*1024*1024){ Swal.fire({title:'Too Large',text:'Max 5 MB',icon:'warning',confirmButtonColor:'#C0170F'}); return }
    excelFile.value=f; importPreview.value=[]; importErrors.value=[]; importSkipped.value=0
}
const clearExcelFile = () => { excelFile.value=null; importPreview.value=[]; importErrors.value=[]; importSkipped.value=0; if(fileInput.value)fileInput.value.value='' }
const fmtSize = b => b<1024?b+' B':b<1024*1024?(b/1024).toFixed(1)+' KB':(b/1024/1024).toFixed(1)+' MB'

const parseExcelFile = async () => {
    if(!excelFile.value) return
    isParsing.value=true; importErrors.value=[]
    try {
        const buf=await excelFile.value.arrayBuffer()
        const wb=XLSX.read(buf,{type:'array'})
        const ws=wb.Sheets[wb.SheetNames[0]]
        const rows=XLSX.utils.sheet_to_json(ws,{defval:''})
        if(!rows.length){ importErrors.value.push('File appears empty.'); return }
        const keys=Object.keys(rows[0]).map(k=>k.toLowerCase().replace(' *','').trim())
        if(!keys.includes('first_name'))importErrors.value.push('Missing: first_name')
        if(!keys.includes('last_name'))importErrors.value.push('Missing: last_name')
        if(importErrors.value.length) return
        const dataRows=rows.filter(r=>{ const fn=String(r.first_name||r['first_name *']||'').trim().toLowerCase(); return fn!=='required'&&fn!=='' })
        let skipped=0; const parsed=[]
        for(const row of dataRows){
            const fn=String(row.first_name||row['first_name *']||'').trim()
            const ln=String(row.last_name ||row['last_name *'] ||'').trim()
            if(!fn||!ln){ skipped++; continue }
            parsed.push(normalizeRow(row))
        }
        if(!parsed.length){ importErrors.value.push('No valid guests found.'); return }
        importPreview.value=parsed; importSkipped.value=skipped
    } catch(e){ importErrors.value.push('Could not read file: '+e.message) }
    finally{ isParsing.value=false }
}

const addImportedGuestsToList = () => {
    importPreview.value.forEach(g=>form.value.guests.push(g))
    Swal.fire({title:'Imported!',text:`${importPreview.value.length} guest(s) added.`,icon:'success',timer:2000,showConfirmButton:false})
    clearExcelFile(); activeTab.value='manual'
}

const addTempGuest = () => tempGuests.value.push({first_name:'',last_name:'',email:'',phone:''})

const removeTempGuest = async idx => {
    if(tempGuests.value.length<=1) return
    const r=await Swal.fire({title:'Remove?',icon:'question',showCancelButton:true,confirmButtonColor:'#C0170F',cancelButtonColor:'#9E9890',confirmButtonText:'Remove'})
    if(r.isConfirmed) tempGuests.value.splice(idx,1)
}

const addGuestsToList = async () => {
    const valid=tempGuests.value.filter(g=>g.first_name.trim()&&g.last_name.trim())
    if(!valid.length){ Swal.fire({title:'No Valid Guests',text:'Add at least one guest with first and last name.',icon:'warning',confirmButtonColor:'#C0170F'}); return }
    const missing=tempGuests.value.length-valid.length
    if(missing>0){
        const r=await Swal.fire({title:'Incomplete Guests',text:`${missing} guest(s) missing names will be skipped. Continue?`,icon:'warning',showCancelButton:true,confirmButtonColor:'#C0170F',cancelButtonColor:'#9E9890',confirmButtonText:'Add Valid Guests'})
        if(!r.isConfirmed) return
    }
    valid.forEach(g=>form.value.guests.push({
        first_name:g.first_name.trim(), last_name:g.last_name.trim(),
        email:g.email?.trim()||'', phone:g.phone?.trim()||'',
        ...bulkSettings.value, notes:''
    }))
    Swal.fire({title:'Added!',text:`${valid.length} guest(s) added.`,icon:'success',timer:2000,showConfirmButton:false})
    tempGuests.value=[{first_name:'',last_name:'',email:'',phone:''}]
}

const removeGuest = async idx => {
    const name=`${form.value.guests[idx].first_name} ${form.value.guests[idx].last_name}`
    const r=await Swal.fire({title:'Remove?',html:`Remove <strong>${name}</strong>?`,icon:'warning',showCancelButton:true,confirmButtonColor:'#C0170F',cancelButtonColor:'#9E9890'})
    if(r.isConfirmed) form.value.guests.splice(idx,1)
}

const editGuest = idx => { editingIndex.value=idx; editingGuest.value={...form.value.guests[idx]}; showEditModal.value=true }

const saveGuestEdit = () => {
    if(!editingGuest.value.first_name.trim()||!editingGuest.value.last_name.trim()){
        Swal.fire({title:'Missing Name',text:'Please enter first and last name.',icon:'warning',confirmButtonColor:'#C0170F'}); return
    }
    form.value.guests[editingIndex.value]=editingGuest.value
    showEditModal.value=false; editingIndex.value=null; editingGuest.value=null
}

const clearAllGuests = async () => {
    if(!form.value.guests.length) return
    const r=await Swal.fire({title:'Clear All?',html:`Remove all <strong>${form.value.guests.length}</strong> guests?`,icon:'warning',showCancelButton:true,confirmButtonColor:'#C0170F',cancelButtonColor:'#9E9890',confirmButtonText:'Clear All'})
    if(r.isConfirmed){ form.value.guests=[]; Swal.fire({title:'Cleared!',icon:'success',timer:1500,showConfirmButton:false}) }
}

const submit = async () => {
    if(!form.value.guests.length){ Swal.fire({title:'No Guests',text:'Add at least one guest first.',icon:'warning',confirmButtonColor:'#C0170F'}); return }
    const r=await Swal.fire({title:'Save Guests?',html:`Add <strong>${form.value.guests.length} guest(s)</strong> to this event?`,icon:'question',showCancelButton:true,confirmButtonColor:'#C0170F',cancelButtonColor:'#9E9890',confirmButtonText:`Save ${form.value.guests.length} Guests`})
    if(!r.isConfirmed) return
    isSubmitting.value=true
    try {
        const csrf=page.props.csrf_token??document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        const res=await fetch(route('events.guests.bulk-store',props.event.id),{
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':csrf},
            body:JSON.stringify({ guests:form.value.guests, send_invitations:form.value.send_invitations||false, invitation_method:form.value.invitation_method||'email', notes:form.value.notes||'' })
        })
        const data=await res.json()
        if(res.ok&&data.success){
            await Swal.fire({title:'Success!',html:`<p style="color:#16a34a;font-weight:600">${data.created_count} guest(s) created</p>${data.skipped_count?`<p style="color:#b45309">${data.skipped_count} skipped (duplicate)</p>`:''}`,icon:'success',confirmButtonColor:'#C0170F',confirmButtonText:'View Guests'})
            window.location.href=route('events.guests.index',props.event.id)
        } else {
            Swal.fire({title:'Error',text:data.message||'Failed to save guests.',icon:'error',confirmButtonColor:'#C0170F'})
        }
    } catch(e){ Swal.fire({title:'Network Error',text:'Could not connect to server.',icon:'error',confirmButtonColor:'#C0170F'}) }
    finally{ isSubmitting.value=false }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
.confetti{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.cdot{position:absolute;opacity:0;animation:rise linear infinite}
@keyframes rise{0%{transform:translateY(110vh) rotate(0deg);opacity:0}5%{opacity:.35}95%{opacity:.15}100%{transform:translateY(-80px) rotate(540deg);opacity:0}}
.page-wrap{position:relative;z-index:1;background:#F7F5F2;min-height:100vh;padding:28px 24px 64px;font-family:'DM Sans',sans-serif;color:#1A1410}
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:22px}
.breadcrumb{display:flex;align-items:center;gap:6px;font-family:'DM Mono',monospace;font-size:11px;margin-bottom:6px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C2BA}.bc-cur{color:#6B6560;font-weight:500}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(20px,3vw,26px);font-weight:900;color:#1A1410;line-height:1.15}
.header-right{display:flex;align-items:center;gap:10px;flex-shrink:0}
.guest-count-pill{padding:6px 14px;background:rgba(192,23,15,.1);color:#C0170F;border:1px solid rgba(192,23,15,.2);border-radius:20px;font-size:12px;font-weight:700;font-family:'DM Mono',monospace;white-space:nowrap}
.ml-auto{margin-left:auto}
/* tabs */
.tab-bar{display:flex;gap:4px;background:#fff;border:1px solid #E8E2DA;border-radius:14px;padding:5px;width:fit-content;margin-bottom:18px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
.tab-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:10px;border:none;background:transparent;color:#9E9890;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s}
.tab-btn:hover{color:#1A1410}
.tab-btn.active{background:linear-gradient(135deg,#C0170F,#F05A00);color:#fff;box-shadow:0 3px 10px rgba(192,23,15,.25)}
/* cards */
.form-card{background:#fff;border:1px solid #E8E2DA;border-radius:20px;box-shadow:0 2px 14px rgba(0,0,0,.05);overflow:hidden;margin-bottom:16px}
.form-card-head{display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #E8E2DA;background:#F0EDE8}
.card-icon-wrap{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.card-title{font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:#1A1410}
.card-sub{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:1px}
.fields{padding:16px 20px;display:flex;flex-direction:column;gap:14px}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:640px){.field-row{grid-template-columns:1fr}}
.field-row-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
@media(max-width:900px){.field-row-3{grid-template-columns:1fr 1fr}}
@media(max-width:640px){.field-row-3{grid-template-columns:1fr}}
.field-row-4{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:14px;align-items:end}
@media(max-width:900px){.field-row-4{grid-template-columns:1fr 1fr}}
@media(max-width:640px){.field-row-4{grid-template-columns:1fr}}
.field{display:flex;flex-direction:column;gap:5px}
.field-label{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#6B6560;font-weight:500}
.field-input{padding:10px 13px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .18s,box-shadow .18s;width:100%}
.field-input:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.1)}
textarea.field-input{resize:vertical;min-height:60px}
.required{color:#C0170F}
/* toggle */
.toggle-row{display:flex;align-items:center;justify-content:space-between;gap:8px;background:#F7F5F2;padding:10px 14px;border-radius:11px;border:1px solid #E8E2DA}
.toggle-row.full{padding:14px 0;background:none;border:none;border-top:1px solid #F0EDE8}
.toggle-lbl{font-size:13px;font-weight:600;color:#1A1410}
.toggle-wrap{cursor:pointer;flex-shrink:0}
.toggle{width:40px;height:22px;border-radius:11px;background:#E8E2DA;position:relative;transition:background .2s}
.toggle.on{background:linear-gradient(135deg,#C0170F,#F05A00)}
.toggle-knob{position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .2s}
.toggle.on .toggle-knob{transform:translateX(18px)}
/* guest rows */
.guest-rows{display:flex;flex-direction:column;gap:8px}
.guest-row{display:grid;grid-template-columns:24px 1fr 1fr 1fr 1fr 28px;gap:8px;align-items:center}
@media(max-width:900px){.guest-row{grid-template-columns:24px 1fr 1fr;gap:8px}}
.row-num{font-family:'DM Mono',monospace;font-size:11px;color:#9E9890;text-align:right;flex-shrink:0}
.row-remove{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1.5px solid rgba(192,23,15,.2);background:rgba(192,23,15,.04);color:#C0170F;cursor:pointer;transition:all .15s;flex-shrink:0}
.row-remove:hover{background:rgba(192,23,15,.12);border-color:#C0170F}
.row-actions{display:flex;align-items:center;justify-content:space-between;padding-top:6px;border-top:1px solid #F0EDE8}
.btn-add-row{display:inline-flex;align-items:center;gap:6px;color:#C0170F;font-size:13px;font-weight:600;font-family:'DM Mono',monospace;background:none;border:none;cursor:pointer;padding:0;transition:opacity .15s}
.btn-add-row:hover{opacity:.7}
/* col ref */
.col-ref{background:rgba(29,92,150,.06);border:1px solid rgba(29,92,150,.15);border-radius:12px;padding:14px}
.col-ref-title{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#1D5C96;font-weight:700;margin-bottom:8px}
.col-ref-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:4px 12px}
.col-ref-grid span{font-size:12px;color:#1D5C96;font-family:'DM Mono',monospace}
.col-ref-note{font-size:11px;color:#6B7C9E;margin-top:8px;font-family:'DM Mono',monospace}
/* drop zone */
.drop-zone{border:2px dashed #E8E2DA;border-radius:16px;padding:40px;text-align:center;cursor:pointer;transition:all .2s;background:#F7F5F2}
.drop-zone:hover,.drop-zone.dragging{border-color:#C0170F;background:rgba(192,23,15,.04)}
.drop-zone.has-file{border-color:#16a34a;background:rgba(22,163,74,.04);border-style:solid}
.drop-icon{font-size:40px;margin-bottom:10px;opacity:.6}
.drop-title{font-size:14px;font-weight:600;color:#1A1410;margin-bottom:4px}
.drop-link{color:#C0170F;text-decoration:underline}
.drop-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}
.file-preview{display:inline-flex;align-items:center;gap:12px}
.file-icon{font-size:32px}
.file-name{font-size:14px;font-weight:700;color:#1A1410;text-align:left}
.file-size{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace}
.file-clear{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #E8E2DA;background:#fff;color:#9E9890;cursor:pointer;transition:all .15s}
.file-clear:hover{border-color:#C0170F;color:#C0170F}
/* import errors */
.import-errors{background:rgba(192,23,15,.06);border:1px solid rgba(192,23,15,.2);border-radius:11px;padding:12px 16px}
.ie-title{font-size:12px;font-weight:700;color:#C0170F;margin-bottom:6px}
.ie-row{font-size:12px;color:#8B0000;font-family:'DM Mono',monospace;margin-bottom:2px}
/* preview */
.preview-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.preview-title{font-size:13px;font-weight:700;color:#1A1410}
.skip-note{font-size:11px;color:#b45309;font-family:'DM Mono',monospace;margin-left:6px}
.preview-sub{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace}
.preview-table-wrap{overflow-x:auto;border-radius:12px;border:1px solid #E8E2DA}
.preview-table{width:100%;border-collapse:collapse;font-size:12px}
.preview-table thead tr{background:#F0EDE8}
.preview-table th{padding:8px 12px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:#6B6560;text-align:left}
.preview-table tbody tr{border-top:1px solid #F0EDE8;transition:background .1s}
.preview-table tbody tr:hover{background:#FAFAF8}
.preview-table td{padding:8px 12px;color:#1A1410}
.preview-more{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:4px;text-align:right}
.import-actions{display:flex;align-items:center;gap:10px}
/* chips */
.cat-chip,.vip-chip{display:inline-flex;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;font-family:'DM Mono',monospace}
.vip-chip{background:rgba(249,178,51,.18);color:#b45309;border:1px solid rgba(249,178,51,.4)}
/* table (reused) */
.table-scroll{overflow-x:auto}
.ep-table{width:100%;border-collapse:collapse}
.ep-table thead tr{background:#F0EDE8;border-bottom:2px solid #E8E2DA}
.ep-table th{padding:11px 16px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#6B6560;text-align:left}
.th-actions{text-align:right}
.ep-table tbody tr{border-bottom:1px solid #F0EDE8}
.tr-row:hover{background:#FAFAF8}
.ep-table td{padding:12px 16px;vertical-align:middle}
.td-actions{text-align:right;display:flex;justify-content:flex-end;align-items:center;gap:6px}
.action-btn{padding:5px 12px;border-radius:8px;border:1.5px solid;font-size:12px;font-weight:700;font-family:'DM Mono',monospace;cursor:pointer;transition:all .15s}
.action-btn.edit{border-color:rgba(29,92,150,.3);color:#1D5C96;background:rgba(29,92,150,.06)}.action-btn.edit:hover{background:rgba(29,92,150,.12)}
.action-btn.del{border-color:rgba(192,23,15,.2);color:#C0170F;background:rgba(192,23,15,.05)}.action-btn.del:hover{background:rgba(192,23,15,.1)}
/* finalize */
.finalize-actions{display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid #F0EDE8}
/* buttons */
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:11px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-cta:disabled{opacity:.5;cursor:not-allowed;transform:none;animation:none}
.btn-secondary{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Mono',monospace;cursor:pointer;transition:all .18s}
.btn-secondary:hover{border-color:#9E9890;color:#1A1410}
.btn-secondary:disabled{opacity:.4;cursor:not-allowed}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;text-decoration:none;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-danger-outline{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid rgba(192,23,15,.3);background:rgba(192,23,15,.05);color:#C0170F;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s}
.btn-danger-outline:hover{background:rgba(192,23,15,.1);border-color:#C0170F}
.btn-template{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:700;font-family:'DM Mono',monospace;text-decoration:none;transition:all .15s;border:1.5px solid}
.btn-template.green{color:#16a34a;background:rgba(22,163,74,.08);border-color:rgba(22,163,74,.25)}.btn-template.green:hover{background:rgba(22,163,74,.14)}
.btn-template.blue{color:#1D5C96;background:rgba(29,92,150,.08);border-color:rgba(29,92,150,.25)}.btn-template.blue:hover{background:rgba(29,92,150,.14)}
.spin-icon{animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.hidden{display:none}
/* modal */
.modal-wrap{padding:24px;font-family:'DM Sans',sans-serif}
.modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.modal-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#1A1410}
.modal-close{width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1.5px solid #E8E2DA;background:#fff;color:#9E9890;cursor:pointer;transition:all .15s}
.modal-close:hover{border-color:#C0170F;color:#C0170F}
.modal-footer{display:flex;justify-content:flex-end;gap:10px;padding-top:14px;border-top:1px solid #F0EDE8}
</style>