<template>
    <AuthenticatedLayout>

        <div class="confetti" aria-hidden="true">
            <div class="cdot" v-for="n in 12" :key="n" :style="{
                width:(3+(n*3)%9)+'px',height:(3+(n*3)%9)+'px',left:(n*8.2%100)+'%',
                background:['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233'][n%6],
                animationDuration:(10+n*1.1)+'s',animationDelay:(n*.7)+'s',
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
                        <span class="bc-cur">Edit Guest</span>
                    </div>
                    <div class="guest-header-row">
                        <div class="guest-avatar-lg" :style="{background:AVATAR_COLORS[form.category]||'#9E9890'}">
                            {{ getInitials(form.first_name,form.last_name) }}
                        </div>
                        <div>
                            <div class="page-eyebrow"><span class="eyebrow-dot"></span>Editing Guest</div>
                            <h1 class="page-title">{{ form.first_name||'New' }} {{ form.last_name||'Guest' }}</h1>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <span class="rsvp-pill" :style="RSVP_STYLE[form.rsvp_status]||RSVP_STYLE.pending">
                        {{ RSVP_LABEL[form.rsvp_status]||form.rsvp_status }}
                    </span>
                    <span v-if="form.is_vip" class="vip-pill">⭐ VIP</span>
                    <Link :href="route('events.guests.index',event.id)" class="btn-ghost">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                        Back
                    </Link>
                </div>
            </div>

            <div class="layout">
                <!-- ── Main form ── -->
                <div class="layout-main">
                    <form @submit.prevent="submit">

                        <!-- Basic Info -->
                        <div class="form-card">
                            <div class="form-card-head">
                                <div class="card-icon-wrap" style="background:rgba(29,92,150,.1)">👤</div>
                                <div>
                                    <div class="card-title">Basic Information</div>
                                    <div class="card-sub">Name, contact details</div>
                                </div>
                            </div>
                            <div class="fields">
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">First Name <span class="required">*</span></label>
                                        <input type="text" v-model="form.first_name" class="field-input" :class="form.errors.first_name?'err-border':''">
                                        <p v-if="form.errors.first_name" class="err-msg">{{ form.errors.first_name }}</p>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Last Name <span class="required">*</span></label>
                                        <input type="text" v-model="form.last_name" class="field-input" :class="form.errors.last_name?'err-border':''">
                                        <p v-if="form.errors.last_name" class="err-msg">{{ form.errors.last_name }}</p>
                                    </div>
                                </div>
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Email Address</label>
                                        <input type="email" v-model="form.email" class="field-input" :class="form.errors.email?'err-border':''" placeholder="john.doe@example.com">
                                        <p v-if="form.errors.email" class="err-msg">{{ form.errors.email }}</p>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Phone Number</label>
                                        <input type="tel" v-model="form.phone" class="field-input" placeholder="+255 700 000 000">
                                        <p v-if="form.errors.phone" class="err-msg">{{ form.errors.phone }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Classification -->
                        <div class="form-card">
                            <div class="form-card-head">
                                <div class="card-icon-wrap" style="background:rgba(192,23,15,.1)">🏷️</div>
                                <div>
                                    <div class="card-title">Guest Classification</div>
                                    <div class="card-sub">Category, type, RSVP and preferences</div>
                                </div>
                            </div>
                            <div class="fields">
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Category <span class="required">*</span></label>
                                        <select v-model="form.category" class="field-input">
                                            <option value="vip">VIP</option><option value="family">Family</option>
                                            <option value="friends">Friends</option><option value="colleagues">Colleagues</option>
                                            <option value="business">Business</option><option value="media">Media</option>
                                            <option value="sponsors">Sponsors</option><option value="other">Other</option>
                                        </select>
                                        <p v-if="form.errors.category" class="err-msg">{{ form.errors.category }}</p>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Guest Type</label>
                                        <select v-model="form.guest_type" class="field-input">
                                            <option value="primary">Primary Guest</option><option value="plus_one">Plus One</option>
                                            <option value="child">Child</option><option value="vendor">Vendor</option>
                                            <option value="staff">Staff</option><option value="speaker">Speaker</option>
                                            <option value="performer">Performer</option><option value="sponsor">Sponsor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">RSVP Status <span class="required">*</span></label>
                                        <div class="rsvp-chips">
                                            <div v-for="s in RSVP_OPTIONS" :key="s.value"
                                                 @click="form.rsvp_status=s.value"
                                                 :class="['rsvp-chip', form.rsvp_status===s.value?'selected':'']"
                                                 :style="form.rsvp_status===s.value?{borderColor:s.color,background:s.bg,color:s.color}:{}">
                                                <span class="rsvp-dot" :style="{background:s.color}"></span>
                                                {{ s.label }}
                                            </div>
                                        </div>
                                        <p v-if="form.errors.rsvp_status" class="err-msg">{{ form.errors.rsvp_status }}</p>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Language Preference</label>
                                        <select v-model="form.language_preference" class="field-input">
                                            <option value="en">English</option><option value="es">Spanish</option>
                                            <option value="fr">French</option><option value="de">German</option>
                                            <option value="it">Italian</option><option value="pt">Portuguese</option>
                                            <option value="ar">Arabic</option><option value="sw">Swahili</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="toggle-grid">
                                    <div class="toggle-item" @click="form.is_vip=!form.is_vip" :class="form.is_vip?'active-amber':''">
                                        <span class="ti-icon">⭐</span>
                                        <div>
                                            <div class="ti-label">VIP Guest</div>
                                            <div class="ti-sub">Priority treatment</div>
                                        </div>
                                        <div :class="['toggle', form.is_vip?'on':'']"><div class="toggle-knob"></div></div>
                                    </div>
                                    <div class="toggle-item" @click="form.plus_one_allowed=!form.plus_one_allowed" :class="form.plus_one_allowed?'active-navy':''">
                                        <span class="ti-icon">👥</span>
                                        <div>
                                            <div class="ti-label">Allow Plus One(s)</div>
                                            <div class="ti-sub">Can bring additional guests</div>
                                        </div>
                                        <div :class="['toggle', form.plus_one_allowed?'on':'']"><div class="toggle-knob"></div></div>
                                    </div>
                                </div>
                                <div v-if="form.plus_one_allowed" class="field" style="max-width:180px">
                                    <label class="field-label">Plus Ones Count</label>
                                    <input type="number" v-model="form.plus_ones" min="0" max="10" class="field-input">
                                </div>
                            </div>
                        </div>

                        <!-- Special Requirements -->
                        <div class="form-card">
                            <div class="form-card-head">
                                <div class="card-icon-wrap" style="background:rgba(22,163,74,.1)">🛡️</div>
                                <div>
                                    <div class="card-title">Special Requirements</div>
                                    <div class="card-sub">Dietary, accessibility and logistics needs</div>
                                </div>
                            </div>
                            <div class="fields">
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Dietary Preference</label>
                                        <select v-model="form.dietary_preference" class="field-input">
                                            <option value="">None</option><option value="Vegetarian">Vegetarian</option>
                                            <option value="Vegan">Vegan</option><option value="Gluten-Free">Gluten-Free</option>
                                            <option value="Dairy-Free">Dairy-Free</option><option value="Kosher">Kosher</option>
                                            <option value="Halal">Halal</option><option value="Pescatarian">Pescatarian</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Allergies</label>
                                        <input type="text" v-model="form.allergies" class="field-input" placeholder="e.g. Peanuts, Shellfish">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="field-label">Special Requirements</label>
                                    <textarea v-model="form.special_requirements" rows="2" class="field-input" placeholder="Any other special requirements…"></textarea>
                                </div>
                                <div class="field">
                                    <label class="field-label">Accessibility Needs</label>
                                    <textarea v-model="form.accessibility_needs" rows="2" class="field-input" placeholder="e.g. Wheelchair access, hearing assistance…"></textarea>
                                </div>
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Accommodation Needs</label>
                                        <textarea v-model="form.accommodation_needs" rows="2" class="field-input" placeholder="Hotel, room requirements…"></textarea>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Transportation Needs</label>
                                        <textarea v-model="form.transportation_needs" rows="2" class="field-input" placeholder="Airport pickup, parking…"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-card">
                            <div class="form-card-head">
                                <div class="card-icon-wrap" style="background:rgba(158,152,144,.15)">📝</div>
                                <div>
                                    <div class="card-title">Notes</div>
                                    <div class="card-sub">Internal notes about this guest</div>
                                </div>
                            </div>
                            <div class="fields">
                                <div class="field">
                                    <textarea v-model="form.notes" rows="3" class="field-input" placeholder="Any internal notes or comments about this guest…"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Form actions -->
                        <div class="form-actions-bar">
                            <div class="form-actions-left">
                                <Link :href="route('events.guests.index',event.id)" class="btn-ghost">Cancel</Link>
                                <button type="button" @click="showDeleteModal=true" class="btn-danger-outline">
                                    🗑️ Delete Guest
                                </button>
                            </div>
                            <button type="submit" :disabled="form.processing" class="btn-cta">
                                <svg v-if="form.processing" class="spin-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                <svg v-else width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                {{ form.processing?'Saving…':'Update Guest' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ── Sidebar ── -->
                <div class="layout-side">

                    <!-- Invitation status -->
                    <div class="side-card">
                        <div class="side-card-title">✉️ Invitation</div>
                        <div class="side-rows">
                            <div class="side-row">
                                <span class="sr-lbl">Status</span>
                                <span :class="['sr-val', guest.invitation_sent?'green':'muted']">
                                    {{ guest.invitation_sent?'✓ Sent':'Not Sent' }}
                                </span>
                            </div>
                            <div v-if="guest.invitation_sent" class="side-row">
                                <span class="sr-lbl">Method</span>
                                <span class="sr-val capitalize">{{ guest.invitation_method||'email' }}</span>
                            </div>
                            <div v-if="guest.invitation_sent_at" class="side-row">
                                <span class="sr-lbl">Sent on</span>
                                <span class="sr-val">{{ fmtDate(guest.invitation_sent_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Check-in status -->
                    <div class="side-card">
                        <div class="side-card-title">🎫 Check-in</div>
                        <div v-if="guest.check_in_time" class="side-rows">
                            <div class="side-row">
                                <span class="sr-lbl">Checked In</span>
                                <span class="sr-val green">{{ fmtDateTime(guest.check_in_time) }}</span>
                            </div>
                            <div v-if="guest.check_out_time" class="side-row">
                                <span class="sr-lbl">Checked Out</span>
                                <span class="sr-val">{{ fmtDateTime(guest.check_out_time) }}</span>
                            </div>
                            <div class="side-row">
                                <span class="sr-lbl">Duration</span>
                                <span class="sr-val">{{ getDuration(guest.check_in_time,guest.check_out_time) }}</span>
                            </div>
                        </div>
                        <div v-else class="empty-checkin">
                            <div style="font-size:28px;margin-bottom:6px;opacity:.4">⏳</div>
                            <div style="font-size:12px;color:#9E9890">Not checked in yet</div>
                        </div>
                    </div>

                    <!-- System info -->
                    <div class="side-card">
                        <div class="side-card-title">🔧 System Info</div>
                        <div class="side-rows">
                            <div class="side-row">
                                <span class="sr-lbl">Guest ID</span>
                                <span class="sr-val mono">#{{ guest.id }}</span>
                            </div>
                            <div class="side-row">
                                <span class="sr-lbl">Created</span>
                                <span class="sr-val">{{ fmtDate(guest.created_at) }}</span>
                            </div>
                            <div class="side-row">
                                <span class="sr-lbl">Updated</span>
                                <span class="sr-val">{{ fmtDate(guest.updated_at) }}</span>
                            </div>
                            <div v-if="guest.rsvp_responded_at" class="side-row">
                                <span class="sr-lbl">RSVP At</span>
                                <span class="sr-val">{{ fmtDate(guest.rsvp_responded_at) }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal=false">
            <div class="modal-wrap">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title" style="color:#C0170F">Delete Guest</h2>
                        <p class="modal-sub">This action cannot be undone</p>
                    </div>
                    <button @click="showDeleteModal=false" class="modal-close">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="delete-body">
                    <div class="delete-icon-wrap">🗑️</div>
                    <p style="font-size:14px;color:#1A1410;line-height:1.7">
                        Are you sure you want to delete <strong>{{ guest.first_name }} {{ guest.last_name }}</strong>?
                        This will remove all their information including RSVP responses and check-in records.
                    </p>
                </div>
                <div class="modal-footer">
                    <button @click="showDeleteModal=false" class="btn-ghost">Cancel</button>
                    <button @click="confirmDelete" :disabled="deleteForm.processing" class="btn-cta" style="background-image:linear-gradient(135deg,#C0170F,#8B0000)">
                        {{ deleteForm.processing?'Deleting…':'Delete Guest' }}
                    </button>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({ event:Object, guest:Object })
const showDeleteModal = ref(false)

const form = useForm({
    first_name:           props.guest.first_name,
    last_name:            props.guest.last_name,
    email:                props.guest.email||'',
    phone:                props.guest.phone||'',
    category:             props.guest.category,
    guest_type:           props.guest.guest_type||'primary',
    rsvp_status:          props.guest.rsvp_status,
    plus_ones:            props.guest.plus_ones||0,
    plus_one_allowed:     props.guest.plus_one_allowed||false,
    dietary_preference:   props.guest.dietary_preference||'',
    allergies:            props.guest.allergies||'',
    special_requirements: props.guest.special_requirements||'',
    accessibility_needs:  props.guest.accessibility_needs||'',
    accommodation_needs:  props.guest.accommodation_needs||'',
    transportation_needs: props.guest.transportation_needs||'',
    is_vip:               props.guest.is_vip||false,
    language_preference:  props.guest.language_preference||'en',
    notes:                props.guest.notes||'',
})
const deleteForm = useForm({})

const submit = () => {
    form.put(route('events.guests.update',[props.event.id,props.guest.id]), {
        preserveScroll:true,
        onSuccess:()=>Swal.fire({title:'Saved!',text:'Guest updated successfully.',icon:'success',timer:2000,showConfirmButton:false,confirmButtonColor:'#C0170F'}),
        onError:()=>Swal.fire({title:'Error',text:'Failed to update. Please check the form.',icon:'error',confirmButtonColor:'#C0170F'}),
    })
}

const confirmDelete = async () => {
    const r=await Swal.fire({
        title:'Delete Guest?', html:`Delete <strong>${props.guest.first_name} ${props.guest.last_name}</strong>? Cannot be undone.`,
        icon:'warning', showCancelButton:true, confirmButtonColor:'#C0170F', cancelButtonColor:'#9E9890',
        confirmButtonText:'Delete', cancelButtonText:'Cancel', reverseButtons:true
    })
    if(!r.isConfirmed){ showDeleteModal.value=false; return }
    deleteForm.delete(route('events.guests.destroy',[props.event.id,props.guest.id]),{
        onSuccess:()=>{ showDeleteModal.value=false; Swal.fire({title:'Deleted!',icon:'success',timer:2000,showConfirmButton:false}).then(()=>router.visit(route('events.guests.index',props.event.id))) },
        onError:()=>Swal.fire({title:'Error',text:'Failed to delete.',icon:'error',confirmButtonColor:'#C0170F'})
    })
}

const getInitials = (f,l) => `${f?.[0]||''}${l?.[0]||''}`.toUpperCase()
const fmtDate = d => d?new Date(d).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}):'N/A'
const fmtTime = d => d?new Date(d).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}):'N/A'
const fmtDateTime = d => d?fmtDate(d)+' '+fmtTime(d):'N/A'
const getDuration = (ci,co) => {
    if(!ci) return 'N/A'
    const ms=(co?new Date(co):new Date())-new Date(ci)
    const h=Math.floor(ms/(1000*60*60)), m=Math.floor((ms%(1000*60*60))/(1000*60))
    return h>0?`${h}h ${m}m`:`${m}m`
}

const AVATAR_COLORS = { vip:'#b45309',family:'#7c3aed',friends:'#1D5C96',colleagues:'#16a34a',business:'#1D5C96',media:'#db2777',sponsors:'#C0170F',other:'#9E9890' }
const RSVP_OPTIONS = [
    {value:'pending',     label:'Pending',      color:'#b45309', bg:'rgba(249,178,51,.12)'},
    {value:'attending',   label:'Attending',    color:'#16a34a', bg:'rgba(22,163,74,.1)'},
    {value:'not_attending',label:'Not Attending',color:'#C0170F',bg:'rgba(192,23,15,.08)'},
    {value:'maybe',       label:'Maybe',        color:'#1D5C96', bg:'rgba(29,92,150,.1)'},
]
const RSVP_STYLE = {
    pending:{background:'rgba(249,178,51,.12)',color:'#b45309',border:'1px solid rgba(249,178,51,.3)'},
    attending:{background:'rgba(22,163,74,.1)',color:'#16a34a',border:'1px solid rgba(22,163,74,.25)'},
    not_attending:{background:'rgba(192,23,15,.08)',color:'#C0170F',border:'1px solid rgba(192,23,15,.2)'},
    maybe:{background:'rgba(29,92,150,.1)',color:'#1D5C96',border:'1px solid rgba(29,92,150,.2)'},
}
const RSVP_LABEL = {pending:'Pending',attending:'Attending',not_attending:'Not Attending',maybe:'Maybe'}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
.confetti{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.cdot{position:absolute;opacity:0;animation:rise linear infinite}
@keyframes rise{0%{transform:translateY(110vh) rotate(0deg);opacity:0}5%{opacity:.35}95%{opacity:.15}100%{transform:translateY(-80px) rotate(540deg);opacity:0}}
.page-wrap{position:relative;z-index:1;background:#F7F5F2;min-height:100vh;padding:28px 24px 64px;font-family:'DM Sans',sans-serif;color:#1A1410}
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px}
.breadcrumb{display:flex;align-items:center;gap:6px;font-family:'DM Mono',monospace;font-size:11px;margin-bottom:10px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C2BA}.bc-cur{color:#6B6560;font-weight:500}
.guest-header-row{display:flex;align-items:center;gap:14px}
.guest-avatar-lg{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-family:'DM Mono',monospace;font-size:18px;font-weight:700;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,.15)}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:4px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#F05A00;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(18px,3vw,24px);font-weight:900;color:#1A1410;line-height:1.2}
.header-right{display:flex;align-items:center;gap:8px;flex-shrink:0;flex-wrap:wrap}
.rsvp-pill,.vip-pill{display:inline-flex;padding:5px 13px;border-radius:20px;font-size:11px;font-weight:700;font-family:'DM Mono',monospace}
.vip-pill{background:rgba(249,178,51,.18);color:#b45309;border:1px solid rgba(249,178,51,.35)}
/* layout */
.layout{display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start}
@media(max-width:1024px){.layout{grid-template-columns:1fr}}
.layout-main{display:flex;flex-direction:column;gap:16px}
.layout-side{display:flex;flex-direction:column;gap:14px;position:sticky;top:80px}
/* form cards */
.form-card{background:#fff;border:1px solid #E8E2DA;border-radius:20px;box-shadow:0 2px 14px rgba(0,0,0,.05);overflow:hidden}
.form-card-head{display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #E8E2DA;background:#F0EDE8}
.card-icon-wrap{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.card-title{font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:#1A1410}
.card-sub{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:1px}
.fields{padding:18px 20px;display:flex;flex-direction:column;gap:14px}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:640px){.field-row{grid-template-columns:1fr}}
.field{display:flex;flex-direction:column;gap:5px}
.field-label{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#6B6560;font-weight:500}
.required{color:#C0170F}
.field-input{padding:10px 13px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .18s,box-shadow .18s;width:100%}
.field-input:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.1)}
.err-border{border-color:#C0170F!important}
.err-msg{font-size:11px;color:#C0170F;font-family:'DM Mono',monospace}
textarea.field-input{resize:vertical;min-height:72px}
/* rsvp chips */
.rsvp-chips{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
@media(max-width:500px){.rsvp-chips{grid-template-columns:1fr 1fr}}
.rsvp-chip{display:flex;align-items:center;gap:7px;padding:8px 12px;border:1.5px solid #E8E2DA;border-radius:11px;cursor:pointer;font-size:12px;font-weight:600;font-family:'DM Mono',monospace;color:#6B6560;background:#fff;transition:all .15s;user-select:none}
.rsvp-chip:hover{border-color:#9E9890}
.rsvp-chip.selected{font-weight:700}
.rsvp-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
/* toggle grid */
.toggle-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
@media(max-width:640px){.toggle-grid{grid-template-columns:1fr}}
.toggle-item{display:flex;align-items:center;gap:10px;padding:12px 14px;border:1.5px solid #E8E2DA;border-radius:13px;cursor:pointer;transition:all .18s;background:#F7F5F2;user-select:none}
.toggle-item:hover{border-color:#9E9890}
.toggle-item.active-amber{border-color:rgba(249,178,51,.5);background:rgba(249,178,51,.06)}
.toggle-item.active-navy{border-color:rgba(29,92,150,.3);background:rgba(29,92,150,.06)}
.ti-icon{font-size:18px;flex-shrink:0}
.ti-label{font-size:13px;font-weight:600;color:#1A1410}
.ti-sub{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace}
.toggle-item .toggle{margin-left:auto;flex-shrink:0}
.toggle{width:40px;height:22px;border-radius:11px;background:#E8E2DA;position:relative;transition:background .2s}
.toggle.on{background:linear-gradient(135deg,#C0170F,#F05A00)}
.toggle-knob{position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .2s}
.toggle.on .toggle-knob{transform:translateX(18px)}
/* form actions */
.form-actions-bar{display:flex;align-items:center;justify-content:space-between;background:#fff;border:1px solid #E8E2DA;border-radius:16px;padding:14px 20px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
.form-actions-left{display:flex;align-items:center;gap:10px}
/* sidebar cards */
.side-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;padding:16px 18px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
.side-card-title{font-family:'Playfair Display',serif;font-size:14px;font-weight:700;color:#1A1410;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #F0EDE8}
.side-rows{display:flex;flex-direction:column;gap:0}
.side-row{display:flex;justify-content:space-between;align-items:baseline;padding:6px 0;border-bottom:1px solid #F7F5F2;gap:8px}
.side-row:last-child{border-bottom:none}
.sr-lbl{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;flex-shrink:0}
.sr-val{font-size:12px;font-weight:600;color:#1A1410;text-align:right}
.sr-val.green{color:#16a34a}
.sr-val.muted{color:#9E9890;font-weight:400}
.sr-val.mono{font-family:'DM Mono',monospace}
.empty-checkin{text-align:center;padding:16px 0}
/* buttons */
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:11px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-cta:disabled{opacity:.5;cursor:not-allowed;transform:none;animation:none}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;text-decoration:none;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-danger-outline{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid rgba(192,23,15,.3);background:rgba(192,23,15,.05);color:#C0170F;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s}
.btn-danger-outline:hover{background:rgba(192,23,15,.1);border-color:#C0170F}
.spin-icon{animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
/* modal */
.modal-wrap{padding:24px;font-family:'DM Sans',sans-serif}
.modal-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px}
.modal-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#1A1410}
.modal-sub{font-size:12px;color:#9E9890;margin-top:2px}
.modal-close{width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1.5px solid #E8E2DA;background:#fff;color:#9E9890;cursor:pointer;transition:all .15s;flex-shrink:0}
.modal-close:hover{border-color:#C0170F;color:#C0170F}
.delete-body{display:flex;align-items:flex-start;gap:16px;margin-bottom:20px}
.delete-icon-wrap{font-size:36px;flex-shrink:0;opacity:.7}
.modal-footer{display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #F0EDE8}
.capitalize{text-transform:capitalize}
</style>