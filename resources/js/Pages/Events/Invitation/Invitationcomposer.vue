<template>
    <AuthenticatedLayout>

        <!-- Confetti -->
        <div class="confetti" aria-hidden="true">
            <div class="cdot" v-for="n in 16" :key="n" :style="{
                width:(4+(n*3)%8)+'px', height:(4+(n*3)%8)+'px', left:(n*6.25%100)+'%',
                background:['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233'][n%6],
                animationDuration:(9+n*1.1)+'s', animationDelay:(n*.5)+'s',
                borderRadius:n%3===0?'3px':'50%',
            }"></div>
        </div>

        <div class="page-wrap">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('events.guests.index', event.id)" class="bc-link">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                            Guest List
                        </Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">Create Invitation</span>
                    </div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Invitation Composer</div>
                    <h1 class="page-title">{{ event.title }}</h1>
                </div>
            </div>

            <!-- Stepper -->
            <div class="stepper">
                <template v-for="(step, idx) in STEPS" :key="idx">
                    <div class="step-item">
                        <div :class="['step-circle', currentStep > idx+1 ? 'done' : currentStep===idx+1 ? 'active' : '']">
                            <svg v-if="currentStep > idx+1" width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            <span v-else>{{ idx+1 }}</span>
                        </div>
                        <span :class="['step-label', currentStep >= idx+1 ? 'step-label-active' : '']">{{ step }}</span>
                    </div>
                    <div v-if="idx < STEPS.length-1" :class="['step-line', currentStep > idx+1 ? 'step-line-filled' : '']"></div>
                </template>
            </div>

            <!-- ── STEP 1: Select Recipients ── -->
            <div v-if="currentStep === 1" class="card">
                <div class="card-head" style="background:linear-gradient(135deg,#C0170F 0%,#F05A00 55%,#F9B233 100%)">
                    <div>
                        <div class="ch-title">Select Recipients</div>
                        <div class="ch-sub">Choose which guests will receive this invitation</div>
                    </div>
                    <div v-if="selectedGuests.length" class="sel-badge">
                        {{ selectedGuests.length }} selected
                        <button @click="selectedGuests=[]" class="sel-clear">✕</button>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Search + filter -->
                    <div class="search-row">
                        <div class="search-wrap">
                            <svg class="search-ico" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input v-model="recipientSearch" type="search" placeholder="Search guests…" class="ep-input search-input">
                        </div>
                        <select v-model="recipientFilter" class="ep-select">
                            <option value="all">All Guests</option>
                            <option value="vip">VIP Only</option>
                            <option value="pending">RSVP Pending</option>
                            <option value="attending">Attending</option>
                        </select>
                    </div>

                    <!-- Selection banner -->
                    <div v-if="selectedGuests.length" class="sel-banner">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <strong>{{ selectedGuests.length }}</strong> guest{{ selectedGuests.length!==1?'s':'' }} selected
                        <button @click="selectedGuests=[]" class="sel-banner-clear">Clear All</button>
                    </div>

                    <!-- Guest grid -->
                    <div class="guest-grid">
                        <div v-for="guest in filteredGuests" :key="guest.id"
                             @click="toggleGuest(guest.id)"
                             :class="['guest-tile', selectedGuests.includes(guest.id)?'guest-tile-sel':'']">
                            <div :class="['tile-check', selectedGuests.includes(guest.id)?'tile-check-on':'']">
                                <svg v-if="selectedGuests.includes(guest.id)" width="9" height="9" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="tile-avatar" :style="{background:AVATAR_COLORS[guest.category]||'#9E9890'}">
                                {{ initials(guest) }}
                            </div>
                            <div class="tile-info">
                                <div class="tile-name">
                                    {{ guest.first_name }} {{ guest.last_name }}
                                    <span v-if="guest.is_vip" class="vip-badge">VIP</span>
                                </div>
                                <div class="tile-email">{{ guest.email||'No email' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-if="!filteredGuests.length" class="empty-state">
                        <div class="empty-icon">👥</div>
                        <div class="empty-title">No guests found</div>
                        <Link :href="route('events.guests.create', event.id)" class="btn-sm">Add Guests</Link>
                    </div>

                    <div class="step-footer">
                        <Link :href="route('events.guests.index', event.id)" class="btn-ghost">Cancel</Link>
                        <button @click="nextStep" :disabled="!selectedGuests.length" class="btn-cta">
                            Continue
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── STEP 2: Design Message ── -->
            <div v-if="currentStep === 2" class="two-col">

                <!-- Left: form -->
                <div class="card" style="overflow:visible">
                    <div class="card-head" style="background:linear-gradient(135deg,#1D5C96 0%,#C0170F 100%)">
                        <div>
                            <div class="ch-title">Design Message</div>
                            <div class="ch-sub">Craft a personalised invitation</div>
                        </div>
                    </div>
                    <div class="card-body" style="max-height:calc(100vh - 240px);overflow-y:auto">

                        <div class="field">
                            <label class="field-label">Delivery Method <span class="req">*</span></label>
                            <div class="method-row">
                                <div v-for="m in METHODS" :key="m.val"
                                     @click="form.invitation_method=m.val"
                                     :class="['method-chip', form.invitation_method===m.val?'method-chip-on':'']">
                                    {{ m.icon }} {{ m.label }}
                                </div>
                            </div>
                        </div>

                        <div v-if="form.invitation_method==='email'" class="field">
                            <label class="field-label">Email Subject <span class="req">*</span></label>
                            <input v-model="form.subject" type="text" class="ep-input" placeholder="You're Invited!">
                        </div>

                        <div class="field">
                            <label class="field-label">Invitation Message <span class="req">*</span></label>
                            <textarea v-model="form.message" rows="11" class="ep-input" style="resize:vertical" placeholder="Write your invitation…"></textarea>
                            <div class="var-strip">
                                <span class="var-label">Insert:</span>
                                <button v-for="v in VARS" :key="v" @click="form.message+=v" type="button" class="var-chip">{{ v }}</button>
                            </div>
                        </div>

                        <div class="field">
                            <label class="field-label">Invitation Card Image <span class="field-opt">(Optional)</span></label>
                            <div class="dropzone" @click="$refs.fileInput.click()">
                                <input ref="fileInput" type="file" accept="image/*" class="hidden-input" @change="handleImg">
                                <template v-if="!imagePreview">
                                    <div class="dz-icon">🖼️</div>
                                    <div class="dz-title">Click to upload</div>
                                    <div class="dz-sub">PNG · JPG · up to 5 MB</div>
                                </template>
                                <template v-else>
                                    <div class="img-wrap">
                                        <img :src="imagePreview" class="preview-img">
                                        <button @click.stop="clearImg" class="img-remove">
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="step-footer card-footer">
                        <button @click="previousStep" class="btn-ghost">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                            Back
                        </button>
                        <button @click="nextStep" class="btn-cta">
                            Preview &amp; Continue
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Right: live preview -->
                <div class="preview-panel">
                    <div class="preview-head">👁 Live Preview</div>
                    <div class="preview-body">
                        <div class="inv-card">
                            <div v-if="imagePreview" class="inv-img-wrap">
                                <img :src="imagePreview" class="inv-img">
                            </div>
                            <div v-else class="inv-placeholder">
                                <span style="font-size:38px;opacity:.35">🖼️</span>
                                <div class="inv-placeholder-txt">Upload invitation image</div>
                            </div>
                            <div class="inv-content">
                                <div class="inv-event-name">{{ event.title }}</div>
                                <div class="inv-meta">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    {{ fmtDate(event.event_date) }}
                                </div>
                                <div v-if="event.city" class="inv-meta">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ event.city }}, {{ event.country }}
                                </div>
                                <div class="inv-divider"></div>
                                <div class="inv-message">{{ form.message || 'Your message will appear here…' }}</div>
                            </div>
                        </div>
                        <div class="preview-note">This is how your invitation will appear to guests</div>
                    </div>
                </div>
            </div>

            <!-- ── STEP 3: Review & Send ── -->
            <div v-if="currentStep === 3" class="two-col two-col-3">

                <!-- Left: summary -->
                <div class="card">
                    <div class="card-head" style="background:linear-gradient(135deg,#16a34a 0%,#1D5C96 100%)">
                        <div>
                            <div class="ch-title">Review &amp; Send</div>
                            <div class="ch-sub">Final check before sending</div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="summary-stats">
                            <div class="ss-card" style="--c:#C0170F">
                                <div class="ss-num">{{ selectedGuests.length }}</div>
                                <div class="ss-lbl">Recipients</div>
                            </div>
                            <div class="ss-card" style="--c:#1D5C96">
                                <div class="ss-num" style="font-size:15px;text-transform:uppercase;letter-spacing:.06em">{{ form.invitation_method }}</div>
                                <div class="ss-lbl">Method</div>
                            </div>
                            <div class="ss-card" style="--c:#16a34a">
                                <div class="ss-num">✓</div>
                                <div class="ss-lbl">Ready</div>
                            </div>
                        </div>

                        <div class="review-sec">
                            <div class="review-sec-head">
                                <div class="rsec-title">👥 Recipients ({{ selectedGuests.length }})</div>
                                <button @click="currentStep=1" class="rsec-edit">Edit</button>
                            </div>
                            <div class="rec-grid">
                                <div v-for="gid in selectedGuests" :key="gid" class="rec-row">
                                    <div class="rec-av" :style="{background:guestColor(gid)}">{{ guestInitials(gid) }}</div>
                                    <div class="rec-info">
                                        <div class="rec-name">{{ guestName(gid) }}</div>
                                        <div class="rec-email">{{ guestEmail(gid) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="review-sec">
                            <div class="review-sec-head">
                                <div class="rsec-title">✉️ Message Details</div>
                                <button @click="currentStep=2" class="rsec-edit">Edit</button>
                            </div>
                            <div class="msg-detail">
                                <div v-if="form.invitation_method==='email'" class="msg-row">
                                    <span class="mr-lbl">Subject</span>
                                    <span class="mr-val">{{ form.subject }}</span>
                                </div>
                                <div class="msg-row">
                                    <span class="mr-lbl">Method</span>
                                    <span class="mr-val" style="text-transform:capitalize">{{ form.invitation_method }}</span>
                                </div>
                                <div class="msg-row" style="align-items:flex-start">
                                    <span class="mr-lbl" style="margin-top:2px">Message</span>
                                    <div class="mr-msg">{{ form.message }}</div>
                                </div>
                                <div v-if="imagePreview" class="msg-row" style="align-items:flex-start">
                                    <span class="mr-lbl" style="margin-top:4px">Image</span>
                                    <img :src="imagePreview" class="mr-img">
                                </div>
                            </div>
                        </div>

                        <div class="step-footer" style="border-top:none;padding-top:0">
                            <button @click="previousStep" class="btn-ghost">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                                Back
                            </button>
                            <button @click="sendInvitations" :disabled="sending" class="btn-send">
                                <svg v-if="sending" class="spin-ico" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                <svg v-else width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                {{ sending ? 'Sending…' : `Send to ${selectedGuests.length} Guest${selectedGuests.length!==1?'s':''}` }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: final preview -->
                <div class="preview-panel">
                    <div class="preview-head">📨 Final Preview</div>
                    <div class="preview-body">
                        <div class="inv-card">
                            <div v-if="imagePreview" class="inv-img-wrap"><img :src="imagePreview" class="inv-img"></div>
                            <div v-else class="inv-placeholder" style="height:220px">
                                <span style="font-size:28px;opacity:.35">🖼️</span>
                            </div>
                            <div class="inv-content">
                                <div class="inv-event-name">{{ event.title }}</div>
                                <div class="inv-meta">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    {{ fmtDate(event.event_date) }}
                                </div>
                                <div class="inv-divider"></div>
                                <div class="inv-message" style="max-height:100px;overflow:hidden;-webkit-mask-image:linear-gradient(#000 60%,transparent)">{{ form.message }}</div>
                            </div>
                        </div>
                        <div class="preview-note">✓ Ready to send to {{ selectedGuests.length }} guest{{ selectedGuests.length!==1?'s':'' }}</div>
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
    qrCodeBase64: String,
})

const STEPS   = ['Recipients', 'Message', 'Send']
const METHODS = [
    { val:'email',    icon:'📧', label:'Email'    },
    { val:'sms',      icon:'💬', label:'SMS'      },
    { val:'whatsapp', icon:'📱', label:'WhatsApp' },
]
const VARS = ['[Guest Name]','[Event Name]','[Event Date]','[Event Location]']
const AVATAR_COLORS = {
    vip:'#b45309', family:'#7c3aed', friends:'#1D5C96',
    colleagues:'#16a34a', business:'#1D5C96', media:'#db2777',
    sponsors:'#C0170F', other:'#9E9890',
}

const currentStep     = ref(1)
const selectedGuests  = ref([...(props.preselectedGuests||[])])
const sending         = ref(false)
const imagePreview    = ref(null)
const fileInput       = ref(null)
const recipientSearch = ref('')
const recipientFilter = ref('all')

const form = ref({
    invitation_method: 'email',
    subject: `You're Invited to ${props.event.title}!`,
    message: `Dear [Guest Name],

You are cordially invited to join us for ${props.event.title}.

📅 Date: ${fmtDate(props.event.event_date)}
${props.event.city ? `📍 Location: ${props.event.city}, ${props.event.country}` : ''}

We look forward to celebrating with you!

Warm regards,
${props.event.title} Team`,
    image: null,
})

const filteredGuests = computed(() => {
    let list = props.guests || []
    if (recipientFilter.value === 'vip')       list = list.filter(g => g.is_vip)
    if (recipientFilter.value === 'pending')   list = list.filter(g => g.rsvp_status === 'pending')
    if (recipientFilter.value === 'attending') list = list.filter(g => g.rsvp_status === 'attending')
    const q = recipientSearch.value.trim().toLowerCase()
    if (q) list = list.filter(g => `${g.first_name} ${g.last_name} ${g.email||''}`.toLowerCase().includes(q))
    return list
})

const toggleGuest  = id => { const i=selectedGuests.value.indexOf(id); i>-1 ? selectedGuests.value.splice(i,1) : selectedGuests.value.push(id) }
const nextStep     = () => { if (currentStep.value < 3) currentStep.value++ }
const previousStep = () => { if (currentStep.value > 1) currentStep.value-- }

const handleImg = e => {
    const file = e.target.files[0]
    if (!file || !file.type.startsWith('image/')) return
    const r = new FileReader()
    r.onload = ev => { imagePreview.value = ev.target.result; form.value.image = file }
    r.readAsDataURL(file)
}
const clearImg = () => { imagePreview.value = null; form.value.image = null; if (fileInput.value) fileInput.value.value = '' }

const initials      = g => `${g.first_name?.[0]||''}${g.last_name?.[0]||''}`.toUpperCase()
const findGuest     = id => (props.guests||[]).find(g => g.id === id)
const guestName     = id => { const g=findGuest(id); return g ? `${g.first_name} ${g.last_name}` : 'Unknown' }
const guestEmail    = id => findGuest(id)?.email || 'No email'
const guestColor    = id => AVATAR_COLORS[findGuest(id)?.category] || '#9E9890'
const guestInitials = id => { const g=findGuest(id); return g ? initials(g) : '?' }

function fmtDate(d) {
    if (!d) return 'Date TBA'
    return new Date(d).toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' })
}

const sendInvitations = () => {
    sending.value = true
    const fd = new FormData()
    selectedGuests.value.forEach(id => fd.append('guest_ids[]', id))
    fd.append('invitation_method', form.value.invitation_method)
    fd.append('subject', form.value.subject)
    fd.append('custom_message', form.value.message)
    if (form.value.image) fd.append('invitation_image', form.value.image)

    router.post(route('events.guests.bulk-invites', props.event.id), fd, {
        onSuccess: res => {
            sending.value = false
            const d = res.props?.flash?.data || {}
            Swal.fire({
                title: 'Invitations Sent! 🎉',
                html: `<div style="text-align:left;font-family:'DM Sans',sans-serif">
                    ${d.invited_now>0 ? `<p style="margin-bottom:6px">✅ <strong>${d.invited_now}</strong> invitation${d.invited_now!==1?'s':''} sent</p>` : ''}
                    ${d.skipped>0    ? `<p>⚠️ <strong>${d.skipped}</strong> already invited (skipped)</p>` : ''}
                </div>`,
                icon: 'success',
                confirmButtonColor: '#C0170F',
                confirmButtonText: 'View Guest List',
            }).then(() => router.visit(route('events.guests.index', props.event.id)))
        },
        onError: () => {
            sending.value = false
            Swal.fire({ title:'Error', text:'Failed to send invitations.', icon:'error', confirmButtonColor:'#C0170F' })
        },
    })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

.confetti{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.cdot{position:absolute;opacity:0;animation:rise linear infinite}
@keyframes rise{0%{transform:translateY(110vh) rotate(0deg);opacity:0}5%{opacity:.32}95%{opacity:.12}100%{transform:translateY(-80px) rotate(540deg);opacity:0}}

.page-wrap{position:relative;z-index:1;background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}

.page-header{margin-bottom:22px}
.breadcrumb{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:11px;margin-bottom:8px}
.bc-link{display:inline-flex;align-items:center;gap:4px;color:#9E9890;text-decoration:none;transition:color .15s}
.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C2BA}.bc-cur{color:#6B6560;font-weight:500}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(20px,3vw,27px);font-weight:900;color:#1A1410;line-height:1.15}

.stepper{display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:24px;max-width:520px}
.step-item{display:flex;align-items:center;gap:8px;flex-shrink:0}
.step-circle{width:34px;height:34px;border-radius:50%;border:2px solid #E8E2DA;background:#fff;display:flex;align-items:center;justify-content:center;font-family:'DM Mono',monospace;font-size:12px;font-weight:700;color:#9E9890;transition:all .25s;flex-shrink:0}
.step-circle.active{border-color:#C0170F;background:#C0170F;color:#fff;box-shadow:0 0 0 4px rgba(192,23,15,.14)}
.step-circle.done{border-color:#16a34a;background:#16a34a;color:#fff}
.step-label{font-family:'DM Mono',monospace;font-size:11px;color:#9E9890;white-space:nowrap;transition:color .25s}
.step-label-active{color:#1A1410;font-weight:700}
.step-line{flex:1 1 40px;height:2px;background:#E8E2DA;margin:0 10px;transition:background .35s}
.step-line-filled{background:linear-gradient(90deg,#16a34a,#C0170F)}

.card{background:#fff;border:1px solid #E8E2DA;border-radius:22px;box-shadow:0 2px 18px rgba(0,0,0,.06);overflow:hidden;margin-bottom:0}
.card-head{padding:18px 24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px}
.ch-title{font-family:'Playfair Display',serif;font-size:17px;font-weight:900}
.ch-sub{font-size:12px;opacity:.85;font-family:'DM Mono',monospace;margin-top:2px}
.card-body{padding:20px 24px;display:flex;flex-direction:column;gap:18px}
.card-footer{padding:14px 24px 20px;border-top:1px solid #F0EDE8;background:#FAFAF8}

.sel-badge{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.35);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;font-family:'DM Mono',monospace;flex-shrink:0}
.sel-clear{background:rgba(255,255,255,.3);border:none;border-radius:50%;width:18px;height:18px;cursor:pointer;font-size:10px;color:#fff;display:flex;align-items:center;justify-content:center;line-height:1}

.search-row{display:flex;gap:10px;flex-wrap:wrap}
.search-wrap{position:relative;flex:1;min-width:160px}
.search-ico{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9E9890;pointer-events:none}
.search-input{padding-left:34px !important}
.ep-input{width:100%;padding:9px 13px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;box-sizing:border-box}
.ep-input:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.ep-select{padding:9px 13px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:12px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;cursor:pointer;transition:border-color .15s}
.ep-select:focus{border-color:#C0170F}

.sel-banner{display:flex;align-items:center;gap:8px;padding:10px 14px;background:rgba(192,23,15,.06);border:1.5px solid rgba(192,23,15,.18);border-radius:11px;font-size:13px;color:#1A1410}
.sel-banner-clear{margin-left:auto;font-size:11px;font-weight:700;color:#C0170F;background:none;border:none;cursor:pointer;font-family:'DM Mono',monospace}
.sel-banner-clear:hover{text-decoration:underline}

.guest-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(218px,1fr));gap:10px;max-height:360px;overflow-y:auto;padding:2px}
.guest-tile{display:flex;align-items:center;gap:10px;padding:10px 13px;border:2px solid #E8E2DA;border-radius:13px;cursor:pointer;transition:all .17s;background:#fff;user-select:none}
.guest-tile:hover{border-color:#F05A00;background:#FAFAF8;transform:translateY(-1px)}
.guest-tile-sel{border-color:#C0170F;background:rgba(192,23,15,.04);box-shadow:0 0 0 3px rgba(192,23,15,.1)}
.tile-check{width:18px;height:18px;border-radius:5px;border:2px solid #E8E2DA;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s;background:#fff}
.tile-check-on{background:#C0170F;border-color:#C0170F;color:#fff}
.tile-avatar{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-family:'DM Mono',monospace;font-size:12px;font-weight:700;flex-shrink:0}
.tile-info{flex:1;min-width:0}
.tile-name{font-size:13px;font-weight:700;color:#1A1410;display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.tile-email{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.vip-badge{display:inline-flex;padding:1px 6px;background:rgba(249,178,51,.18);color:#b45309;border-radius:20px;font-size:10px;font-weight:700;border:1px solid rgba(249,178,51,.4)}

.empty-state{text-align:center;padding:40px 20px;background:#F7F5F2;border-radius:16px;border:2px dashed #E8E2DA}
.empty-icon{font-size:40px;margin-bottom:10px;opacity:.35}
.empty-title{font-family:'Playfair Display',serif;font-size:17px;font-weight:700;color:#1A1410;margin-bottom:10px}

.field{display:flex;flex-direction:column;gap:6px}
.field-label{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#6B6560;font-weight:500}
.field-opt{text-transform:none;letter-spacing:0;font-size:10px;color:#9E9890;font-style:italic}
.req{color:#C0170F}

.method-row{display:flex;gap:8px}
.method-chip{flex:1;padding:9px 10px;border:2px solid #E8E2DA;border-radius:11px;cursor:pointer;font-size:12px;font-weight:700;font-family:'DM Mono',monospace;color:#6B6560;background:#fff;transition:all .18s;text-align:center;user-select:none}
.method-chip:hover{border-color:#9E9890}
.method-chip-on{border-color:#C0170F;background:rgba(192,23,15,.06);color:#C0170F}

.var-strip{display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-top:5px}
.var-label{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;white-space:nowrap}
.var-chip{display:inline-flex;padding:3px 9px;background:#F0EDE8;border:1px solid #E8E2DA;border-radius:7px;font-size:11px;font-family:'DM Mono',monospace;color:#6B6560;cursor:pointer;transition:all .15s}
.var-chip:hover{background:rgba(192,23,15,.08);border-color:rgba(192,23,15,.25);color:#C0170F}

.dropzone{border:2px dashed #E8E2DA;border-radius:14px;padding:24px;text-align:center;cursor:pointer;transition:all .2s;background:#F7F5F2}
.dropzone:hover{border-color:#C0170F;background:rgba(192,23,15,.03)}
.dz-icon{font-size:30px;margin-bottom:8px;opacity:.45}
.dz-title{font-size:13px;font-weight:600;color:#1A1410;margin-bottom:3px}
.dz-sub{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace}
.hidden-input{display:none}
.img-wrap{position:relative;display:inline-block}
.preview-img{max-height:120px;border-radius:10px;box-shadow:0 4px 14px rgba(0,0,0,.1)}
.img-remove{position:absolute;top:-8px;right:-8px;width:22px;height:22px;border-radius:50%;background:#C0170F;border:2px solid #fff;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,.2)}

.two-col{display:grid;grid-template-columns:3fr 2fr;gap:18px;align-items:start}
.two-col-3{grid-template-columns:2fr 1fr}
@media(max-width:1024px){.two-col,.two-col-3{grid-template-columns:1fr}}

.preview-panel{background:#fff;border:1px solid #E8E2DA;border-radius:22px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.06);position:sticky;top:80px}
.preview-head{padding:10px 16px;background:#F0EDE8;font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#6B6560;letter-spacing:.12em;text-transform:uppercase;border-bottom:1px solid #E8E2DA}
.preview-body{padding:10px;background:#F7F5F2}
.inv-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1);border:1px solid #E8E2DA}
.inv-img-wrap{background:#0a0504}
.inv-img{width:100%;max-height:600px;object-fit:contain;display:block}
.inv-placeholder{background:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px}
.inv-placeholder-txt{font-size:12px;color:rgba(255,255,255,.75);font-family:'DM Mono',monospace}
.inv-content{padding:8px 12px 12px}
.inv-event-name{font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:#1A1410;margin-bottom:4px;text-align:center}
.inv-meta{display:flex;align-items:center;justify-content:center;gap:5px;font-size:10px;color:#6B6560;font-family:'DM Mono',monospace;margin-bottom:3px}
.inv-divider{height:1px;background:#F0EDE8;margin:8px 0}
.inv-message{font-size:11px;color:#6B6560;line-height:1.6;white-space:pre-wrap;max-height:80px;overflow:hidden;-webkit-mask-image:linear-gradient(#000 50%,transparent)}
.preview-note{text-align:center;font-size:10px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:8px;font-style:italic}

.summary-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.ss-card{background:#fff;border:2px solid #E8E2DA;border-top:4px solid var(--c);border-radius:14px;padding:14px;text-align:center}
.ss-num{font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:var(--c);line-height:1}
.ss-lbl{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;text-transform:uppercase;letter-spacing:.12em;margin-top:4px}

.review-sec{background:#F7F5F2;border:1px solid #E8E2DA;border-radius:14px;overflow:hidden}
.review-sec-head{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid #E8E2DA;background:#F0EDE8}
.rsec-title{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#1A1410;letter-spacing:.1em;text-transform:uppercase}
.rsec-edit{font-size:11px;font-weight:700;font-family:'DM Mono',monospace;color:#C0170F;background:none;border:none;cursor:pointer}
.rsec-edit:hover{text-decoration:underline}
.rec-grid{padding:10px;display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:200px;overflow-y:auto}
@media(max-width:640px){.rec-grid{grid-template-columns:1fr}}
.rec-row{display:flex;align-items:center;gap:8px;padding:7px 10px;background:#fff;border-radius:10px;border:1px solid #E8E2DA}
.rec-av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-family:'DM Mono',monospace;font-size:11px;font-weight:700;flex-shrink:0}
.rec-name{font-size:12px;font-weight:700;color:#1A1410}
.rec-email{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.msg-detail{padding:14px;display:flex;flex-direction:column;gap:10px}
.msg-row{display:flex;gap:10px;align-items:baseline}
.mr-lbl{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;color:#9E9890;font-weight:700;white-space:nowrap;letter-spacing:.1em;flex-shrink:0}
.mr-val{font-size:13px;font-weight:600;color:#1A1410}
.mr-msg{font-size:12px;color:#6B6560;line-height:1.7;white-space:pre-wrap;max-height:120px;overflow-y:auto;background:#fff;border:1px solid #E8E2DA;border-radius:8px;padding:8px 10px;flex:1}
.mr-img{max-height:80px;border-radius:8px;border:1px solid #E8E2DA}

.step-footer{display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid #F0EDE8}
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;border-radius:11px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-cta:disabled{opacity:.5;cursor:not-allowed;transform:none;animation:none}
.btn-sm{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;border:none;cursor:pointer;background:#C0170F;color:#fff;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;text-decoration:none;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-send{display:inline-flex;align-items:center;gap:8px;padding:12px 26px;border-radius:13px;border:none;cursor:pointer;background:linear-gradient(135deg,#16a34a,#1D5C96);color:#fff;font-size:14px;font-weight:700;font-family:'DM Sans',sans-serif;box-shadow:0 4px 16px rgba(22,163,74,.3);transition:all .2s}
.btn-send:hover{transform:translateY(-1px);box-shadow:0 6px 24px rgba(22,163,74,.4)}
.btn-send:disabled{opacity:.5;cursor:not-allowed;transform:none}
.spin-ico{animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
</style>