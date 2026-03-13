<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- Confetti -->
            <div class="confetti-layer" aria-hidden="true">
                <div v-for="i in 16" :key="i" class="cdot" :style="cdotStyle(i)"></div>
            </div>

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('bookings.index')" class="bc-link">Bookings</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">Create</span>
                    </div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>New Booking</div>
                    <h1 class="page-title">Create Booking</h1>
                    <p class="page-sub">Fill in the details to register a new event booking</p>
                </div>
                <Link :href="route('bookings.index')" class="btn-ghost">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
                    Back to Bookings
                </Link>
            </div>

            <form @submit.prevent="submit">
                <div class="two-col">

                    <!-- ── LEFT: Main Form ── -->
                    <div class="form-col">

                        <!-- Event & Client -->
                        <div class="form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(192,23,15,.12)">📅</span>
                                <div>
                                    <div class="card-title">Event & Client</div>
                                    <div class="card-sub">Select the event and client for this booking</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Event <span class="req">*</span></label>
                                        <select v-model="form.event_id" class="ep-select" :class="{error: form.errors.event_id}" @change="onEventChange">
                                            <option value="">— Select Event —</option>
                                            <option v-for="e in events" :key="e.id" :value="e.id">{{ e.title }}</option>
                                        </select>
                                        <div v-if="form.errors.event_id" class="field-error">{{ form.errors.event_id }}</div>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Client / Customer <span class="req">*</span></label>
                                        <select v-model="form.client_id" class="ep-select" :class="{error: form.errors.client_id}">
                                            <option value="">— Select Client —</option>
                                            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                                        </select>
                                        <div v-if="form.errors.client_id" class="field-error">{{ form.errors.client_id }}</div>
                                    </div>
                                </div>

                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Venue</label>
                                        <select v-model="form.venue_id" class="ep-select">
                                            <option value="">— Select Venue —</option>
                                            <option v-for="v in venues" :key="v.id" :value="v.id">{{ v.name }}</option>
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Event Type</label>
                                        <select v-model="form.event_type" class="ep-select">
                                            <option value="">— Select Type —</option>
                                            <option value="wedding">Wedding</option>
                                            <option value="corporate">Corporate</option>
                                            <option value="birthday">Birthday</option>
                                            <option value="conference">Conference</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dates & Times -->
                        <div class="form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(29,92,150,.1)">🗓️</span>
                                <div>
                                    <div class="card-title">Dates & Times</div>
                                    <div class="card-sub">Set booking and event schedule</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Booking Date <span class="req">*</span></label>
                                        <input v-model="form.booking_date" type="date" class="ep-input" :class="{error: form.errors.booking_date}">
                                        <div v-if="form.errors.booking_date" class="field-error">{{ form.errors.booking_date }}</div>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Event Date <span class="req">*</span></label>
                                        <input v-model="form.event_date" type="date" class="ep-input" :class="{error: form.errors.event_date}">
                                        <div v-if="form.errors.event_date" class="field-error">{{ form.errors.event_date }}</div>
                                    </div>
                                </div>
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Start Time</label>
                                        <input v-model="form.start_time" type="time" class="ep-input">
                                    </div>
                                    <div class="field">
                                        <label class="field-label">End Time</label>
                                        <input v-model="form.end_time" type="time" class="ep-input">
                                    </div>
                                </div>
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Payment Due Date</label>
                                        <input v-model="form.due_date" type="date" class="ep-input">
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Guest Count</label>
                                        <input v-model="form.guest_count" type="number" min="0" placeholder="0" class="ep-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial -->
                        <div class="form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(22,163,74,.1)">💰</span>
                                <div>
                                    <div class="card-title">Financial Details</div>
                                    <div class="card-sub">Set pricing and payment terms</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Total Amount (TZS) <span class="req">*</span></label>
                                        <div class="prefix-wrap">
                                            <span class="prefix-txt">TZS</span>
                                            <input v-model="form.total_amount" type="number" min="0" step="0.01" placeholder="0" class="ep-input prefix-pad" :class="{error: form.errors.total_amount}">
                                        </div>
                                        <div v-if="form.errors.total_amount" class="field-error">{{ form.errors.total_amount }}</div>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Initial Payment (TZS)</label>
                                        <div class="prefix-wrap">
                                            <span class="prefix-txt">TZS</span>
                                            <input v-model="form.paid_amount" type="number" min="0" step="0.01" placeholder="0" class="ep-input prefix-pad">
                                        </div>
                                    </div>
                                </div>
                                <div class="field-row">
                                    <div class="field">
                                        <label class="field-label">Payment Status</label>
                                        <select v-model="form.payment_status" class="ep-select">
                                            <option value="unpaid">Unpaid</option>
                                            <option value="partially_paid">Partially Paid</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label class="field-label">Discount (TZS)</label>
                                        <div class="prefix-wrap">
                                            <span class="prefix-txt">TZS</span>
                                            <input v-model="form.discount" type="number" min="0" step="0.01" placeholder="0" class="ep-input prefix-pad">
                                        </div>
                                    </div>
                                </div>

                                <!-- Live balance preview -->
                                <div class="balance-strip" v-if="form.total_amount">
                                    <div class="bal-item">
                                        <span class="bal-label">Total</span>
                                        <span class="bal-val">TZS {{ fmt(form.total_amount) }}</span>
                                    </div>
                                    <div class="bal-divider">−</div>
                                    <div class="bal-item">
                                        <span class="bal-label">Paid</span>
                                        <span class="bal-val">TZS {{ fmt(form.paid_amount || 0) }}</span>
                                    </div>
                                    <div class="bal-divider">=</div>
                                    <div class="bal-item">
                                        <span class="bal-label">Balance</span>
                                        <span class="bal-val" :style="{color: balance > 0 ? '#C0170F' : '#16a34a', fontWeight:800}">
                                            TZS {{ fmt(balance) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(249,178,51,.12)">📝</span>
                                <div>
                                    <div class="card-title">Notes & Requirements</div>
                                    <div class="card-sub">Additional info and special requirements</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="field">
                                    <label class="field-label">Internal Notes</label>
                                    <textarea v-model="form.notes" rows="3" placeholder="Add internal notes about this booking…" class="ep-textarea"></textarea>
                                </div>
                                <div class="field" style="margin-top:12px">
                                    <label class="field-label">Special Requirements</label>
                                    <textarea v-model="form.special_requirements" rows="3" placeholder="Dietary, accessibility, AV, décor requirements…" class="ep-textarea"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ── RIGHT: Sidebar ── -->
                    <div class="sidebar-col">

                        <!-- Booking Summary -->
                        <div class="form-card sticky-card">
                            <div class="card-head">
                                <span class="card-icon" style="background:rgba(192,23,15,.12)">📋</span>
                                <div>
                                    <div class="card-title">Booking Summary</div>
                                    <div class="card-sub">Live preview</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="summary-row">
                                    <span class="sum-label">Event</span>
                                    <span class="sum-val">{{ selectedEvent?.title || '—' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="sum-label">Type</span>
                                    <span class="sum-val capitalize">{{ form.event_type || '—' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="sum-label">Venue</span>
                                    <span class="sum-val">{{ selectedVenue?.name || '—' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="sum-label">Event Date</span>
                                    <span class="sum-val">{{ fmtDate(form.event_date) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="sum-label">Guests</span>
                                    <span class="sum-val">{{ form.guest_count || '—' }}</span>
                                </div>
                                <div class="sum-divider"></div>
                                <div class="summary-row">
                                    <span class="sum-label">Total</span>
                                    <span class="sum-val" style="font-weight:800;color:#1A1410">TZS {{ fmt(form.total_amount || 0) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="sum-label">Paid</span>
                                    <span class="sum-val" style="color:#16a34a;font-weight:700">TZS {{ fmt(form.paid_amount || 0) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="sum-label">Balance</span>
                                    <span class="sum-val" :style="{color: balance > 0 ? '#C0170F' : '#16a34a', fontWeight:800}">TZS {{ fmt(balance) }}</span>
                                </div>

                                <!-- Status chip -->
                                <div class="sum-divider"></div>
                                <div class="summary-row">
                                    <span class="sum-label">Status</span>
                                    <span class="status-chip" :style="BOOKING_STATUS['pending']">Pending</span>
                                </div>
                                <div class="summary-row">
                                    <span class="sum-label">Payment</span>
                                    <span class="status-chip" :style="PAYMENT_STATUS[form.payment_status] || PAYMENT_STATUS.unpaid">
                                        {{ form.payment_status?.replace('_',' ') || 'Unpaid' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="action-card">
                            <button type="submit" :disabled="form.processing" class="btn-cta full-width">
                                <svg v-if="!form.processing" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                <span v-if="form.processing" class="spinner"></span>
                                {{ form.processing ? 'Saving…' : 'Create Booking' }}
                            </button>
                            <Link :href="route('bookings.index')" class="btn-ghost full-width" style="justify-content:center;margin-top:8px">
                                Cancel
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
import { computed } from 'vue'

const props = defineProps({
    events:  { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
    venues:  { type: Array, default: () => [] },
})

const form = useForm({
    event_id:             '',
    client_id:            '',
    venue_id:             '',
    event_type:           '',
    booking_date:         '',
    event_date:           '',
    start_time:           '',
    end_time:             '',
    due_date:             '',
    guest_count:          '',
    total_amount:         '',
    paid_amount:          '',
    discount:             '',
    payment_status:       'unpaid',
    notes:                '',
    special_requirements: '',
})

const selectedEvent  = computed(() => props.events.find(e => e.id == form.event_id)  || null)
const selectedVenue  = computed(() => props.venues.find(v => v.id == form.venue_id)  || null)
const balance        = computed(() => Math.max(0, parseFloat(form.total_amount || 0) - parseFloat(form.paid_amount || 0)))

const onEventChange = () => {
    const ev = selectedEvent.value
    if (ev) {
        form.event_date  = ev.start_date || ''
        form.start_time  = ev.start_time || ''
        form.event_type  = ev.event_type || ''
        if (ev.venue_id) form.venue_id = ev.venue_id
    }
}

const submit = () => {
    form.post(route('bookings.store'), {
        onSuccess: () => form.reset(),
    })
}

const BOOKING_STATUS = {
    pending:     { background:'rgba(249,178,51,.15)', color:'#b45309',  border:'1px solid rgba(249,178,51,.4)'  },
    confirmed:   { background:'rgba(22,163,74,.1)',   color:'#16a34a',  border:'1px solid rgba(22,163,74,.25)'  },
    in_progress: { background:'rgba(29,92,150,.1)',   color:'#1D5C96',  border:'1px solid rgba(29,92,150,.25)'  },
    completed:   { background:'rgba(192,23,15,.08)',  color:'#C0170F',  border:'1px solid rgba(192,23,15,.2)'   },
    cancelled:   { background:'rgba(107,101,96,.12)', color:'#6B6560',  border:'1px solid rgba(107,101,96,.3)'  },
}
const PAYMENT_STATUS = {
    unpaid:         { background:'rgba(192,23,15,.08)',  color:'#C0170F',  border:'1px solid rgba(192,23,15,.2)'  },
    partially_paid: { background:'rgba(249,178,51,.12)', color:'#b45309',  border:'1px solid rgba(249,178,51,.3)' },
    paid:           { background:'rgba(22,163,74,.1)',   color:'#16a34a',  border:'1px solid rgba(22,163,74,.25)' },
}

const fmt = v => parseFloat(v||0).toLocaleString('en-US', { minimumFractionDigits:0, maximumFractionDigits:0 })
const fmtDate = d => { if (!d) return '—'; return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }) }

const cdotStyle = (i) => {
    const colors = ['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233']
    return {
        left: `${(i * 6.25) % 100}%`,
        background: colors[i % colors.length],
        width: `${5 + (i % 4) * 2}px`,
        height: `${5 + (i % 4) * 2}px`,
        animationDelay: `${(i * 0.37) % 3}s`,
        animationDuration: `${3 + (i % 3)}s`,
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

/* ── Base ── */
*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410;position:relative;overflow-x:hidden}

/* ── Confetti ── */
.confetti-layer{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.cdot{position:absolute;bottom:-10px;border-radius:50%;opacity:0;animation:rise linear infinite}
@keyframes rise{0%{transform:translateY(0) rotate(0deg);opacity:.7}100%{transform:translateY(-110vh) rotate(540deg);opacity:0}}

/* ── Header ── */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:26px;flex-wrap:wrap;position:relative;z-index:1}
.breadcrumb{display:flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;margin-bottom:5px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C0B8}.bc-cur{color:#6B6560}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(22px,3vw,30px);font-weight:900;color:#1A1410;line-height:1.15;margin-bottom:4px}
.page-sub{font-size:13px;color:#9E9890;font-family:'DM Mono',monospace}

/* ── Layout ── */
.two-col{display:grid;grid-template-columns:1fr 300px;gap:18px;position:relative;z-index:1;align-items:start}
@media(max-width:900px){.two-col{grid-template-columns:1fr}}
.form-col{display:flex;flex-direction:column;gap:16px}
.sidebar-col{display:flex;flex-direction:column;gap:14px}
.sticky-card{position:sticky;top:80px}

/* ── Cards ── */
.form-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04)}
.card-head{display:flex;align-items:center;gap:12px;padding:14px 18px;background:#F0EDE8;border-bottom:1px solid #E8E2DA}
.card-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.card-title{font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:#1A1410;line-height:1.2}
.card-sub{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:1px}
.card-body{padding:18px}

/* ── Fields ── */
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.field-row:last-child{margin-bottom:0}
.field{display:flex;flex-direction:column;gap:5px}
.field-label{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#6B6560;font-weight:600}
.req{color:#C0170F}
.field-error{font-size:11px;color:#C0170F;font-family:'DM Mono',monospace;margin-top:2px}
.ep-input,.ep-select,.ep-textarea{width:100%;padding:9px 12px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
.ep-input:focus,.ep-select:focus,.ep-textarea:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.ep-input.error,.ep-select.error{border-color:#C0170F}
.ep-textarea{resize:vertical;min-height:80px;line-height:1.6}
.ep-select{cursor:pointer}

/* ── Prefix inputs ── */
.prefix-wrap{position:relative}
.prefix-txt{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;pointer-events:none;font-weight:600}
.prefix-pad{padding-left:38px !important}

/* ── Balance strip ── */
.balance-strip{display:flex;align-items:center;gap:10px;background:#F7F5F2;border:1px solid #E8E2DA;border-radius:12px;padding:12px 16px;margin-top:14px}
.bal-item{display:flex;flex-direction:column;align-items:center;flex:1}
.bal-label{font-family:'DM Mono',monospace;font-size:9px;letter-spacing:.12em;text-transform:uppercase;color:#9E9890;margin-bottom:3px}
.bal-val{font-family:'DM Mono',monospace;font-size:12px;font-weight:700;color:#1A1410}
.bal-divider{font-size:16px;color:#C8C0B8;font-weight:300;flex-shrink:0}

/* ── Summary sidebar ── */
.summary-row{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:5px 0;border-bottom:1px solid #F7F5F2}
.summary-row:last-child{border-bottom:none}
.sum-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890;flex-shrink:0}
.sum-val{font-size:12px;font-weight:600;color:#1A1410;text-align:right;font-family:'DM Mono',monospace}
.sum-val.capitalize{text-transform:capitalize}
.sum-divider{height:1px;background:#F0EDE8;margin:8px 0}
.status-chip{display:inline-flex;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;font-family:'DM Mono',monospace;text-transform:capitalize}

/* ── Action card ── */
.action-card{display:flex;flex-direction:column;gap:0}

/* ── Buttons ── */
.btn-cta{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:11px 22px;border-radius:11px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-cta:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-cta:disabled{opacity:.6;cursor:not-allowed;animation:none}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;text-decoration:none;white-space:nowrap}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.full-width{width:100%}
.spinner{width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;display:inline-block;flex-shrink:0}
@keyframes spin{to{transform:rotate(360deg)}}
</style>