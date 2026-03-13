<template>
    <AuthenticatedLayout>

        <!-- Confetti -->
        <div class="confetti" aria-hidden="true">
            <div class="cdot" v-for="n in 14" :key="n" :style="{
                width: (3+(n*3)%9)+'px', height: (3+(n*3)%9)+'px',
                left: (n*7.1%100)+'%',
                background: ['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233'][n%6],
                animationDuration: (10+n*1.1)+'s', animationDelay: (n*.6)+'s',
                borderRadius: n%3===0 ? '2px' : '50%',
            }"></div>
        </div>

        <div class="page-wrap">

            <!-- Page header -->
            <div class="page-header">
                <div>
                    <div class="page-eyebrow">
                        <span class="eyebrow-dot"></span>
                        Editing Event
                    </div>
                    <h1 class="page-title">{{ event.title }}</h1>
                </div>
                <Link :href="route('events.show', event.id)" class="back-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                    Back to Event
                </Link>
            </div>

            <form @submit.prevent="submit" class="form-layout">

                <!-- Left column -->
                <div class="form-left">

                    <!-- Event Type -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="card-icon-wrap" style="background:rgba(192,23,15,.1)">🏷️</div>
                            <div>
                                <div class="card-title">Event Type <span class="required">*</span></div>
                                <div class="card-sub">Choose what kind of event this is</div>
                            </div>
                        </div>
                        <div class="type-grid">
                            <div v-for="type in eventTypes" :key="type.id"
                                 @click="form.event_type_id = type.id"
                                 :class="['type-chip', form.event_type_id === type.id ? 'selected' : '']">
                                <span class="type-icon">{{ type.icon }}</span>
                                <span class="type-name">{{ type.name }}</span>
                            </div>
                        </div>
                        <p v-if="form.errors.event_type_id" class="err-msg" style="padding:0 20px 14px">{{ form.errors.event_type_id }}</p>
                    </div>

                    <!-- Event Details -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="card-icon-wrap" style="background:rgba(29,92,150,.1)">🎪</div>
                            <div>
                                <div class="card-title">Event Details</div>
                                <div class="card-sub">Name, description, capacity and status</div>
                            </div>
                        </div>
                        <div class="fields">
                            <div class="field-row">
                                <div class="field">
                                    <label class="field-label">Title <span class="required">*</span></label>
                                    <input v-model="form.title" type="text" class="field-input" placeholder="Event title">
                                    <p v-if="form.errors.title" class="err-msg">{{ form.errors.title }}</p>
                                </div>
                                <div class="field">
                                    <label class="field-label">Expected Guests <span class="required">*</span></label>
                                    <input v-model="form.expected_guests" type="number" min="1" class="field-input" placeholder="250">
                                    <p v-if="form.errors.expected_guests" class="err-msg">{{ form.errors.expected_guests }}</p>
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-label">Description</label>
                                <textarea v-model="form.description" rows="3" class="field-input" placeholder="Tell your guests what to expect…"></textarea>
                            </div>
                            <div class="field">
                                <label class="field-label">Status</label>
                                <div class="status-grid">
                                    <div v-for="s in STATUSES" :key="s.value"
                                         @click="form.status = s.value"
                                         :class="['status-chip', form.status === s.value ? 'selected' : '']"
                                         :style="form.status === s.value ? { borderColor: s.color, background: s.bg, color: s.color } : {}">
                                        <span class="status-dot" :style="{ background: s.color }"></span>
                                        {{ s.label }}
                                    </div>
                                </div>
                                <p v-if="form.errors.status" class="err-msg">{{ form.errors.status }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="card-icon-wrap" style="background:rgba(249,178,51,.15)">📅</div>
                            <div>
                                <div class="card-title">Date &amp; Time</div>
                                <div class="card-sub">When does this event take place?</div>
                            </div>
                        </div>
                        <div class="fields">
                            <div class="field-row">
                                <div class="field">
                                    <label class="field-label">Event Date <span class="required">*</span></label>
                                    <input v-model="form.event_date" type="date" :min="minDate" @change="validateDates" class="field-input">
                                    <p v-if="form.errors.event_date" class="err-msg">{{ form.errors.event_date }}</p>
                                </div>
                                <div class="field">
                                    <label class="field-label">End Date <span class="optional">(optional)</span></label>
                                    <input v-model="form.event_end_date" type="date" :min="form.event_date||minDate" @change="validateDates" :disabled="!form.event_date" class="field-input">
                                    <p v-if="dateErrors.event_end_date" class="warn-msg">{{ dateErrors.event_end_date }}</p>
                                    <p v-if="form.errors.event_end_date" class="err-msg">{{ form.errors.event_end_date }}</p>
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="field">
                                    <label class="field-label">Start Time</label>
                                    <input v-model="form.start_time" type="time" @change="validateTimes" class="field-input">
                                    <p v-if="form.errors.start_time" class="err-msg">{{ form.errors.start_time }}</p>
                                </div>
                                <div class="field">
                                    <label class="field-label">End Time</label>
                                    <input v-model="form.end_time" type="time" @change="validateTimes" :disabled="!form.start_time" class="field-input">
                                    <p v-if="timeErrors.end_time" class="warn-msg">{{ timeErrors.end_time }}</p>
                                    <p v-if="form.errors.end_time" class="err-msg">{{ form.errors.end_time }}</p>
                                </div>
                            </div>
                            <div v-if="timeErrors.end_time || dateErrors.event_end_date" class="warn-box">
                                <svg width="14" height="14" fill="#b45309" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <div style="font-size:12px;color:#92400e">
                                    <strong style="display:block;margin-bottom:4px">Date/Time Issues:</strong>
                                    <div v-if="timeErrors.end_time">• {{ timeErrors.end_time }}</div>
                                    <div v-if="dateErrors.event_end_date">• {{ dateErrors.event_end_date }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="card-icon-wrap" style="background:rgba(22,163,74,.1)">📍</div>
                            <div>
                                <div class="card-title">Location</div>
                                <div class="card-sub">Where will this take place?</div>
                            </div>
                        </div>
                        <div class="fields">
                            <div class="field-row">
                                <div class="field">
                                    <label class="field-label">Venue Name</label>
                                    <input v-model="form.venue_name" type="text" class="field-input" placeholder="Serena Hotel">
                                </div>
                                <div class="field">
                                    <label class="field-label">City <span class="required">*</span></label>
                                    <input v-model="form.city" type="text" class="field-input" placeholder="Dar es Salaam">
                                    <p v-if="form.errors.city" class="err-msg">{{ form.errors.city }}</p>
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-label">Venue Address</label>
                                <textarea v-model="form.venue_address" rows="2" class="field-input" placeholder="Full street address…"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="card-icon-wrap" style="background:rgba(158,152,144,.15)">📝</div>
                            <div>
                                <div class="card-title">Additional Notes</div>
                                <div class="card-sub">Internal notes and requirements</div>
                            </div>
                        </div>
                        <div class="fields">
                            <div class="field">
                                <textarea v-model="form.notes" rows="3" class="field-input" placeholder="Any additional notes, requirements or reminders…"></textarea>
                                <p v-if="form.errors.notes" class="err-msg">{{ form.errors.notes }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right sidebar -->
                <div class="form-right">

                    <!-- Budget & Settings -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="card-icon-wrap" style="background:rgba(192,23,15,.08)">💰</div>
                            <div>
                                <div class="card-title">Budget &amp; Settings</div>
                            </div>
                        </div>
                        <div class="fields">
                            <div class="field">
                                <label class="field-label">Estimated Budget <span class="optional">(TZS)</span></label>
                                <input v-model="form.estimated_budget" type="number" step="0.01" min="0" class="field-input" placeholder="45,000,000">
                                <p v-if="form.errors.estimated_budget" class="err-msg">{{ form.errors.estimated_budget }}</p>
                            </div>
                            <div class="toggle-row">
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#1A1410">Public Event</div>
                                    <div style="font-size:11px;color:#9E9890;font-family:'DM Mono',monospace">Visible on public listings</div>
                                </div>
                                <div class="toggle-wrap" @click="form.is_public = !form.is_public">
                                    <div :class="['toggle', form.is_public ? 'on' : '']">
                                        <div class="toggle-knob"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danger zone -->
                    <div class="danger-card">
                        <div class="danger-head">⚠️ Danger Zone</div>
                        <p class="danger-text">Cancelling this event will notify all confirmed guests and cannot be undone.</p>
                        <button type="button" class="btn-danger-outline" @click="confirmCancel">
                            🚫 Cancel This Event
                        </button>
                    </div>

                    <!-- Actions -->
                    <div class="action-card">
                        <p class="required-note">* Required fields</p>
                        <Link :href="route('events.show', event.id)" class="btn-ghost">Discard Changes</Link>
                        <button type="submit" :disabled="form.processing || hasValidationErrors" class="btn-cta">
                            <svg v-if="form.processing" class="spin-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                            <svg v-else width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            {{ form.processing ? 'Saving…' : 'Save Changes' }}
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { onMounted, watch, ref, computed } from 'vue'

const props = defineProps({ event: Object, eventTypes: Array, flash: Object });

const timeErrors = ref({});
const dateErrors = ref({});

const STATUSES = [
    { value:'draft',     label:'Draft',     color:'#9E9890', bg:'rgba(158,152,144,.12)' },
    { value:'planning',  label:'Planning',  color:'#1D5C96', bg:'rgba(29,92,150,.1)'    },
    { value:'confirmed', label:'Confirmed', color:'#16a34a', bg:'rgba(22,163,74,.1)'    },
    { value:'ongoing',   label:'Live Now',  color:'#F05A00', bg:'rgba(240,90,0,.1)'     },
    { value:'completed', label:'Wrapped',   color:'#C0170F', bg:'rgba(192,23,15,.08)'   },
    { value:'cancelled', label:'Cancelled', color:'#6B6560', bg:'rgba(107,101,96,.1)'   },
];

const minDate = computed(() => new Date().toISOString().split('T')[0]);

const hasValidationErrors = computed(() =>
    Object.keys(timeErrors.value).length > 0 || Object.keys(dateErrors.value).length > 0
);

const formatDateForInput = (d) => {
    if (!d) return '';
    const dt = new Date(d);
    return `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`;
};

const form = useForm({
    event_type_id:    props.event.event_type_id,
    title:            props.event.title || '',
    description:      props.event.description || '',
    status:           props.event.status || 'draft',
    event_date:       formatDateForInput(props.event.event_date),
    event_end_date:   formatDateForInput(props.event.event_end_date),
    start_time:       props.event.start_time || '',
    end_time:         props.event.end_time || '',
    venue_name:       props.event.venue_name || '',
    venue_address:    props.event.venue_address || '',
    city:             props.event.city || '',
    expected_guests:  props.event.expected_guests || '',
    estimated_budget: props.event.estimated_budget || '',
    is_public:        props.event.is_public || false,
    notes:            props.event.notes || '',
});

const validateDates = () => {
    dateErrors.value = {};
    if (form.event_date && form.event_end_date) {
        if (new Date(form.event_end_date) < new Date(form.event_date)) {
            dateErrors.value.event_end_date = 'End date must be on or after start date';
            form.event_end_date = '';
        }
    }
};

const validateTimes = () => {
    timeErrors.value = {};
    if (form.start_time && form.end_time) {
        const isSameDay = !form.event_end_date || form.event_date === form.event_end_date;
        if (isSameDay) {
            const [sh, sm] = form.start_time.split(':').map(Number);
            const [eh, em] = form.end_time.split(':').map(Number);
            if ((eh*60+em) <= (sh*60+sm))
                timeErrors.value.end_time = 'End time must be after start time (same-day event)';
        }
    }
};

watch([() => form.event_date, () => form.event_end_date], () => {
    if (form.event_date && form.event_end_date && form.event_date !== form.event_end_date)
        timeErrors.value = {};
    else if (form.start_time && form.end_time) validateTimes();
});
watch([() => form.start_time, () => form.end_time], () => {
    if (form.start_time && form.end_time) validateTimes();
});

const submit = () => {
    timeErrors.value = {}; dateErrors.value = {};
    validateDates();
    if (form.start_time && form.end_time) validateTimes();
    if (hasValidationErrors.value) {
        Swal.fire({ icon:'warning', title:'Fix Issues', confirmButtonColor:'#C0170F',
            html:`<div class="text-left text-sm"><ul style="list-style:disc;padding-left:1.2em">
                ${timeErrors.value.end_time ? `<li>${timeErrors.value.end_time}</li>` : ''}
                ${dateErrors.value.event_end_date ? `<li>${dateErrors.value.event_end_date}</li>` : ''}
            </ul></div>` });
        return;
    }
    if (!form.event_type_id) {
        Swal.fire({ icon:'warning', title:'Select Event Type', text:'Please pick an event type.', confirmButtonColor:'#C0170F' });
        return;
    }
    if (!form.title || !form.event_date || !form.city || !form.expected_guests) {
        Swal.fire({ icon:'warning', title:'Missing Fields', text:'Please fill in all required fields.', confirmButtonColor:'#C0170F' });
        return;
    }
    Swal.fire({
        title: 'Save changes?', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#C0170F', cancelButtonColor: '#9E9890',
        confirmButtonText: 'Save Changes', cancelButtonText: 'Go Back',
    }).then(r => {
        if (r.isConfirmed) {
            Swal.fire({ title: 'Saving…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            form.put(route('events.update', props.event.id), {
                preserveScroll: true,
                onSuccess: () => Swal.fire({ icon:'success', title:'Saved!', text:'Event updated successfully.', timer:2000, confirmButtonColor:'#C0170F' }),
                onError: (errors) => Swal.fire({ icon:'error', title:'Update Failed', confirmButtonColor:'#C0170F',
                    html: Object.values(errors).map(e => `<div class="text-sm mb-1">${e}</div>`).join('') }),
                onFinish: () => Swal.close(),
            });
        }
    });
};

const confirmCancel = () => {
    Swal.fire({
        title: 'Cancel this event?', icon: 'warning',
        text: 'All confirmed guests will be notified. This cannot be undone.',
        showCancelButton: true, confirmButtonColor: '#C0170F', cancelButtonColor: '#9E9890',
        confirmButtonText: 'Yes, Cancel Event', cancelButtonText: 'Keep Event',
    }).then(r => {
        if (r.isConfirmed) {
            form.status = 'cancelled';
            form.put(route('events.update', props.event.id), { preserveScroll: true });
        }
    });
};

onMounted(() => {
    if (props.flash?.success)
        Swal.fire({ icon:'success', title:'Success!', text:props.flash.success, timer:2000, confirmButtonColor:'#C0170F' });
    if (props.flash?.error)
        Swal.fire({ icon:'error', title:'Error!', text:props.flash.error, confirmButtonColor:'#C0170F' });
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

/* confetti */
.confetti { position:fixed; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
.cdot     { position:absolute; opacity:0; animation:rise linear infinite; }
@keyframes rise { 0%{transform:translateY(110vh) rotate(0deg);opacity:0;} 5%{opacity:.35;} 95%{opacity:.15;} 100%{transform:translateY(-80px) rotate(540deg);opacity:0;} }

/* page */
.page-wrap { position:relative; z-index:1; background:#F7F5F2; min-height:100vh; padding:28px 24px 64px; font-family:'DM Sans',sans-serif; color:#1A1410; }

/* header */
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:28px; gap:16px; }
.page-eyebrow { display:flex; align-items:center; gap:7px; font-family:'DM Mono',monospace; font-size:10px; letter-spacing:.18em; color:#9E9890; text-transform:uppercase; margin-bottom:6px; }
.eyebrow-dot { width:6px; height:6px; border-radius:50%; background:#F05A00; animation:blink .9s ease-in-out infinite; flex-shrink:0; }
@keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.2;} }
.page-title { font-family:'Playfair Display',serif; font-size:clamp(20px,3vw,28px); font-weight:900; color:#1A1410; line-height:1.15; }
.back-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:10px; border:1.5px solid #E8E2DA; background:#fff; color:#6B6560; font-size:12px; font-weight:600; font-family:'DM Mono',monospace; text-decoration:none; transition:all .18s; white-space:nowrap; flex-shrink:0; }
.back-btn:hover { border-color:#9E9890; color:#1A1410; transform:translateX(-2px); }

/* form layout */
.form-layout { display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start; }
@media(max-width:1024px){ .form-layout{ grid-template-columns:1fr; } }
.form-left  { display:flex; flex-direction:column; gap:16px; }
.form-right { display:flex; flex-direction:column; gap:16px; position:sticky; top:80px; }

/* cards */
.form-card { background:#fff; border:1px solid #E8E2DA; border-radius:20px; box-shadow:0 2px 20px rgba(0,0,0,.06); overflow:hidden; }
.form-card-head { display:flex; align-items:center; gap:12px; padding:16px 20px; border-bottom:1px solid #E8E2DA; background:#F0EDE8; }
.card-icon-wrap { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.card-title { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:#1A1410; }
.card-sub   { font-size:11px; color:#9E9890; font-family:'DM Mono',monospace; margin-top:1px; }
.required   { color:#C0170F; }
.optional   { color:#9E9890; font-weight:400; }

/* type grid */
.type-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; padding:16px 20px; }
@media(max-width:600px){ .type-grid{ grid-template-columns:repeat(2,1fr); } }
.type-chip  { display:flex; flex-direction:column; align-items:center; gap:6px; padding:14px 10px; border:1.5px solid #E8E2DA; border-radius:14px; cursor:pointer; transition:all .18s; background:#fff; }
.type-chip:hover { border-color:#F05A00; background:#fff8f5; transform:translateY(-2px); }
.type-chip.selected { border-color:#C0170F; background:rgba(192,23,15,.06); box-shadow:0 0 0 3px rgba(192,23,15,.1); }
.type-icon  { font-size:26px; }
.type-name  { font-size:11px; font-weight:700; font-family:'DM Mono',monospace; color:#1A1410; text-align:center; }
.type-chip.selected .type-name { color:#C0170F; }

/* status chips */
.status-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
@media(max-width:640px){ .status-grid{ grid-template-columns:repeat(2,1fr); } }
.status-chip { display:flex; align-items:center; gap:6px; padding:8px 12px; border:1.5px solid #E8E2DA; border-radius:10px; cursor:pointer; transition:all .18s; font-size:12px; font-weight:600; font-family:'DM Mono',monospace; color:#6B6560; background:#fff; }
.status-chip:hover { border-color:#9E9890; }
.status-chip.selected { font-weight:700; }
.status-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }

/* fields */
.fields    { padding:16px 20px; display:flex; flex-direction:column; gap:14px; }
.field-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:640px){ .field-row{ grid-template-columns:1fr; } }
.field      { display:flex; flex-direction:column; gap:5px; }
.field-label { font-family:'DM Mono',monospace; font-size:10px; letter-spacing:.12em; text-transform:uppercase; color:#6B6560; font-weight:500; }
.field-input { padding:10px 13px; border:1.5px solid #E8E2DA; border-radius:11px; font-size:13px; font-family:'DM Sans',sans-serif; color:#1A1410; background:#fff; outline:none; transition:border-color .18s,box-shadow .18s; width:100%; }
.field-input:focus { border-color:#C0170F; box-shadow:0 0 0 3px rgba(192,23,15,.1); }
.field-input:disabled { background:#F0EDE8; cursor:not-allowed; color:#9E9890; }
textarea.field-input { resize:vertical; min-height:72px; }

/* validation */
.err-msg  { font-size:11px; color:#C0170F; font-family:'DM Mono',monospace; }
.warn-msg { font-size:11px; color:#b45309; font-family:'DM Mono',monospace; }
.warn-box { display:flex; align-items:flex-start; gap:8px; background:rgba(249,178,51,.1); border:1px solid rgba(249,178,51,.4); border-radius:10px; padding:12px 14px; }

/* toggle */
.toggle-row  { display:flex; align-items:center; justify-content:space-between; padding:14px 0 2px; border-top:1px solid #F0EDE8; }
.toggle-wrap { cursor:pointer; }
.toggle      { width:40px; height:22px; border-radius:11px; background:#E8E2DA; position:relative; transition:background .2s; }
.toggle.on   { background:linear-gradient(135deg,#C0170F,#F05A00); }
.toggle-knob { position:absolute; top:3px; left:3px; width:16px; height:16px; border-radius:50%; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.2); transition:transform .2s; }
.toggle.on .toggle-knob { transform:translateX(18px); }

/* danger zone */
.danger-card { background:#fff; border:1.5px solid rgba(192,23,15,.2); border-radius:20px; padding:16px 20px; box-shadow:0 2px 20px rgba(0,0,0,.06); }
.danger-head { font-family:'Playfair Display',serif; font-size:14px; font-weight:700; color:#C0170F; margin-bottom:6px; }
.danger-text { font-size:12px; color:#6B6560; line-height:1.6; margin-bottom:14px; }
.btn-danger-outline { display:flex; align-items:center; justify-content:center; gap:6px; width:100%; padding:10px; border-radius:10px; border:1.5px solid rgba(192,23,15,.35); background:rgba(192,23,15,.05); color:#C0170F; font-size:12px; font-weight:700; font-family:'DM Sans',sans-serif; cursor:pointer; transition:all .18s; }
.btn-danger-outline:hover { background:rgba(192,23,15,.1); border-color:#C0170F; }

/* action card */
.action-card { background:#fff; border:1px solid #E8E2DA; border-radius:20px; padding:18px 20px; box-shadow:0 2px 20px rgba(0,0,0,.06); display:flex; flex-direction:column; gap:10px; }
.required-note { font-size:11px; color:#9E9890; font-family:'DM Mono',monospace; text-align:center; }
.btn-cta  { display:flex; align-items:center; justify-content:center; gap:7px; width:100%; padding:12px; border-radius:11px; border:none; cursor:pointer; background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%); background-size:200% auto; color:#fff; font-size:13px; font-weight:700; font-family:'DM Sans',sans-serif; box-shadow:0 4px 14px rgba(192,23,15,.3); animation:shine 3s linear infinite; transition:transform .2s,box-shadow .2s; }
@keyframes shine { 0%{background-position:0% center;} 100%{background-position:200% center;} }
.btn-cta:hover    { transform:translateY(-1px); box-shadow:0 6px 20px rgba(192,23,15,.4); }
.btn-cta:disabled { opacity:.5; cursor:not-allowed; transform:none; animation:none; }
.btn-ghost { display:flex; align-items:center; justify-content:center; width:100%; padding:10px; border-radius:11px; border:1.5px solid #E8E2DA; background:#F7F5F2; color:#6B6560; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; text-decoration:none; transition:all .18s; }
.btn-ghost:hover { border-color:#9E9890; color:#1A1410; }
.spin-icon { animation:spin .8s linear infinite; }
@keyframes spin { to{ transform:rotate(360deg); } }
</style>