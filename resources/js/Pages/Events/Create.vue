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
                        New Event
                    </div>
                    <h1 class="page-title">Create an Event</h1>
                </div>
                <Link :href="route('events.index')" class="back-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                    Back to Events
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
                        <p v-if="form.errors.event_type_id" class="err-msg">{{ form.errors.event_type_id }}</p>
                    </div>

                    <!-- Event Details -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <div class="card-icon-wrap" style="background:rgba(29,92,150,.1)">🎪</div>
                            <div>
                                <div class="card-title">Event Details</div>
                                <div class="card-sub">Name, description and capacity</div>
                            </div>
                        </div>
                        <div class="fields">
                            <div class="field-row">
                                <div class="field">
                                    <label class="field-label">Title <span class="required">*</span></label>
                                    <input v-model="form.title" type="text" class="field-input" placeholder="Annual Awards Night 2025">
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
                                    <input v-model="form.event_date" type="date" :min="minDate" @change="validateDates" class="field-input" required>
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

                    <!-- Summary card -->
                    <div class="summary-card">
                        <div class="summary-head">📋 Event Summary</div>
                        <div class="summary-rows">
                            <div class="summary-row">
                                <span class="summary-lbl">Type</span>
                                <span class="summary-val">{{ selectedTypeName || '—' }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">Title</span>
                                <span class="summary-val">{{ form.title || '—' }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">Date</span>
                                <span class="summary-val">{{ form.event_date ? formatDisplay(form.event_date) : '—' }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">City</span>
                                <span class="summary-val">{{ form.city || '—' }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">Guests</span>
                                <span class="summary-val">{{ form.expected_guests || '—' }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-lbl">Visibility</span>
                                <span class="summary-val">{{ form.is_public ? '🌐 Public' : '🔒 Private' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="action-card">
                        <p class="required-note">* Required fields must be filled before creating</p>
                        <Link :href="route('events.index')" class="btn-ghost">Cancel</Link>
                        <button type="submit" :disabled="form.processing || hasValidationErrors" class="btn-cta">
                            <svg v-if="form.processing" class="spin-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                            <svg v-else width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                            {{ form.processing ? 'Creating…' : 'Create Event' }}
                        </button>
                    </div>

                </div>

            </form>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { onMounted, watch, ref, computed } from 'vue';

const props = defineProps({ eventTypes: Array });
const page  = usePage();

const timeErrors = ref({});
const dateErrors = ref({});

const minDate = computed(() => new Date().toISOString().split('T')[0]);

const hasValidationErrors = computed(() =>
    Object.keys(timeErrors.value).length > 0 || Object.keys(dateErrors.value).length > 0
);

const form = useForm({
    event_type_id: null, title: '', description: '',
    event_date: '', event_end_date: '', start_time: '', end_time: '',
    venue_name: '', venue_address: '', city: '',
    expected_guests: '', estimated_budget: '', is_public: false
});

const selectedTypeName = computed(() => {
    if (!form.event_type_id) return '';
    const t = props.eventTypes?.find(t => t.id === form.event_type_id);
    return t ? `${t.icon} ${t.name}` : '';
});

const formatDisplay = (d) => d ? new Date(d).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '';

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
watch(() => form.errors, (errors) => {
    if (Object.keys(errors).length > 0) {
        Swal.fire({
            icon: 'error', title: 'Validation Error',
            html: Object.values(errors).map(e => `<div class="text-sm mb-1">${e}</div>`).join(''),
            confirmButtonText: 'OK',
            confirmButtonColor: '#C0170F',
        });
    }
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
        Swal.fire({ icon:'warning', title:'Select Event Type', text:'Please pick an event type to continue.', confirmButtonColor:'#C0170F' });
        return;
    }
    if (!form.title || !form.event_date || !form.city || !form.expected_guests) {
        Swal.fire({ icon:'warning', title:'Missing Fields', text:'Please fill in all required fields.', confirmButtonColor:'#C0170F' });
        return;
    }
    Swal.fire({
        title: 'Create this event?', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#C0170F', cancelButtonColor: '#9E9890',
        confirmButtonText: 'Create Event', cancelButtonText: 'Go Back',
    }).then(r => { if (r.isConfirmed) form.post(route('events.store')); });
};

onMounted(() => {
    if (page.props.flash?.success)
        Swal.fire({ icon:'success', title:'Success!', text:page.props.flash.success, timer:3000, confirmButtonColor:'#C0170F' });
    if (page.props.flash?.error)
        Swal.fire({ icon:'error', title:'Error!', text:page.props.flash.error, confirmButtonColor:'#C0170F' });
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
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:28px; }
.page-eyebrow { display:flex; align-items:center; gap:7px; font-family:'DM Mono',monospace; font-size:10px; letter-spacing:.18em; color:#9E9890; text-transform:uppercase; margin-bottom:6px; }
.eyebrow-dot { width:6px; height:6px; border-radius:50%; background:#C0170F; animation:blink .9s ease-in-out infinite; flex-shrink:0; }
@keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.2;} }
.page-title { font-family:'Playfair Display',serif; font-size:28px; font-weight:900; color:#1A1410; line-height:1.1; }
.back-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:10px; border:1.5px solid #E8E2DA; background:#fff; color:#6B6560; font-size:12px; font-weight:600; font-family:'DM Mono',monospace; text-decoration:none; transition:all .18s; white-space:nowrap; }
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
.type-grid  { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; padding:16px 20px; }
@media(max-width:600px){ .type-grid{ grid-template-columns:repeat(2,1fr); } }
.type-chip  { display:flex; flex-direction:column; align-items:center; gap:6px; padding:14px 10px; border:1.5px solid #E8E2DA; border-radius:14px; cursor:pointer; transition:all .18s; background:#fff; }
.type-chip:hover { border-color:#F05A00; background:#fff8f5; transform:translateY(-2px); }
.type-chip.selected { border-color:#C0170F; background:rgba(192,23,15,.06); box-shadow:0 0 0 3px rgba(192,23,15,.1); }
.type-icon  { font-size:26px; }
.type-name  { font-size:11px; font-weight:700; font-family:'DM Mono',monospace; color:#1A1410; text-align:center; }
.type-chip.selected .type-name { color:#C0170F; }

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

/* summary card */
.summary-card { background:#fff; border:1px solid #E8E2DA; border-radius:20px; overflow:hidden; box-shadow:0 2px 20px rgba(0,0,0,.06); }
.summary-head { padding:14px 20px; font-family:'Playfair Display',serif; font-size:14px; font-weight:700; color:#1A1410; background:#F0EDE8; border-bottom:1px solid #E8E2DA; }
.summary-rows { padding:12px 20px; display:flex; flex-direction:column; gap:0; }
.summary-row  { display:flex; justify-content:space-between; align-items:baseline; padding:8px 0; border-bottom:1px solid #F0EDE8; gap:10px; }
.summary-row:last-child { border-bottom:none; }
.summary-lbl  { font-family:'DM Mono',monospace; font-size:10px; color:#9E9890; text-transform:uppercase; letter-spacing:.1em; flex-shrink:0; }
.summary-val  { font-size:12px; font-weight:600; color:#1A1410; text-align:right; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:160px; }

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