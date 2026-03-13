<template>
    <AuthenticatedLayout>

        <div class="page-wrap">

            <!-- ── Page Header ── -->
            <div class="page-header">
                <div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Finance</div>
                    <h1 class="page-title">My Bookings</h1>
                    <p class="page-sub">Manage all your event bookings in one place</p>
                </div>
                <Link :href="route('bookings.create')" class="btn-cta">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    Create Booking
                </Link>
            </div>

            <!-- ── Stat Cards ── -->
            <div class="stats-grid">
                <div class="stat-card" style="--accent:#C0170F">
                    <div class="stat-icon-wrap" style="background:rgba(192,23,15,.1)">
                        <svg width="18" height="18" fill="none" stroke="#C0170F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Total Bookings</div>
                        <div class="stat-value">{{ stats?.total ?? 0 }}</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#16a34a">
                    <div class="stat-icon-wrap" style="background:rgba(22,163,74,.1)">
                        <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value" style="font-size:16px">TZS {{ fmt(stats?.total_revenue ?? 0) }}</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#F9B233">
                    <div class="stat-icon-wrap" style="background:rgba(249,178,51,.12)">
                        <svg width="18" height="18" fill="none" stroke="#b45309" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Pending</div>
                        <div class="stat-value">{{ stats?.pending ?? 0 }}</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#1D5C96">
                    <div class="stat-icon-wrap" style="background:rgba(29,92,150,.1)">
                        <svg width="18" height="18" fill="none" stroke="#1D5C96" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Confirmed</div>
                        <div class="stat-value">{{ stats?.confirmed ?? 0 }}</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#F05A00">
                    <div class="stat-icon-wrap" style="background:rgba(240,90,0,.1)">
                        <svg width="18" height="18" fill="none" stroke="#F05A00" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Overdue</div>
                        <div class="stat-value">{{ stats?.overdue ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- ── Filters ── -->
            <div class="filter-card">
                <div class="filter-card-head">
                    <div>
                        <div class="fch-title">Booking List</div>
                        <div class="fch-sub">Filter and manage your event bookings</div>
                    </div>
                    <div class="fch-actions">
                        <button @click="resetFilters" class="btn-ghost">Reset</button>
                        <button @click="showAdvanced = !showAdvanced" class="btn-ghost">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            {{ showAdvanced ? 'Hide' : 'Advanced' }}
                        </button>
                    </div>
                </div>

                <!-- Quick filters -->
                <div class="filter-row">
                    <div class="filter-field">
                        <label class="field-label">Search</label>
                        <div class="search-wrap">
                            <svg class="search-ico" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input v-model="appliedFilters.search" type="search" placeholder="Search bookings…" class="ep-input search-pad">
                        </div>
                    </div>
                    <div class="filter-field">
                        <label class="field-label">Status</label>
                        <select v-model="appliedFilters.status" class="ep-select">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="field-label">Payment</label>
                        <select v-model="appliedFilters.payment_status" class="ep-select">
                            <option value="">All Payment</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="paid">Paid</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="field-label">From Date</label>
                        <input v-model="appliedFilters.start_date" type="date" class="ep-input">
                    </div>
                    <div class="filter-field">
                        <label class="field-label">To Date</label>
                        <input v-model="appliedFilters.end_date" type="date" class="ep-input">
                    </div>
                </div>

                <!-- Advanced filters -->
                <div v-if="showAdvanced" class="adv-filters">
                    <div class="filter-field">
                        <label class="field-label">Event Type</label>
                        <select v-model="appliedFilters.event_type" class="ep-select">
                            <option value="">All Types</option>
                            <option value="wedding">Wedding</option>
                            <option value="corporate">Corporate</option>
                            <option value="birthday">Birthday</option>
                            <option value="conference">Conference</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="field-label">Min Amount (TZS)</label>
                        <div class="amt-wrap">
                            <span class="amt-prefix">TZS</span>
                            <input v-model="appliedFilters.min_amount" type="number" min="0" placeholder="0" class="ep-input amt-pad">
                        </div>
                    </div>
                    <div class="filter-field">
                        <label class="field-label">Max Amount (TZS)</label>
                        <div class="amt-wrap">
                            <span class="amt-prefix">TZS</span>
                            <input v-model="appliedFilters.max_amount" type="number" min="0" placeholder="Any" class="ep-input amt-pad">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Table ── -->
            <div class="table-card">
                <div class="table-scroll">
                    <table class="ep-table">
                        <thead>
                            <tr>
                                <th>Booking Details</th>
                                <th>Event Information</th>
                                <th>Financial Details</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Empty state -->
                            <tr v-if="!bookings?.data?.length">
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="empty-icon">📋</div>
                                        <div class="empty-title">No bookings found</div>
                                        <div class="empty-sub">Get started by creating your first booking</div>
                                        <Link :href="route('bookings.create')" class="btn-cta" style="margin-top:4px">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                                            Create First Booking
                                        </Link>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="booking in bookings?.data ?? []" :key="booking.id" class="ep-row">

                                <!-- Booking Details -->
                                <td class="td-booking">
                                    <div class="booking-avatar" :style="{background: STATUS_COLORS[booking.status] || '#9E9890'}">
                                        {{ booking.booking_number?.substring(0,2) }}
                                    </div>
                                    <div>
                                        <div class="bk-number">{{ booking.booking_number }}</div>
                                        <div class="bk-date">{{ fmtDate(booking.booking_date) }}</div>
                                        <div v-if="booking.guest_count" class="bk-guests">{{ booking.guest_count }} guests</div>
                                    </div>
                                </td>

                                <!-- Event Information -->
                                <td class="td-event">
                                    <div class="ev-title">{{ booking.event?.title || 'Event' }}</div>
                                    <div class="ev-venue">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ booking.venue?.name || 'Venue TBD' }}
                                    </div>
                                    <div class="ev-date">
                                        {{ fmtDate(booking.event_date) }}
                                        <span v-if="booking.start_time"> · {{ fmtTime(booking.start_time) }}</span>
                                    </div>
                                </td>

                                <!-- Financial -->
                                <td class="td-finance">
                                    <div class="fin-total">TZS {{ fmt(booking.total_amount) }}</div>
                                    <div class="fin-paid">Paid: TZS {{ fmt(booking.paid_amount) }}</div>
                                    <div v-if="parseFloat(booking.due_amount) > 0" class="fin-due">
                                        Balance: TZS {{ fmt(booking.due_amount) }}
                                    </div>
                                    <div v-else class="fin-clear">Fully Paid</div>
                                </td>

                                <!-- Status -->
                                <td class="td-status">
                                    <span class="status-chip" :style="BOOKING_STATUS[booking.status] || BOOKING_STATUS.default">
                                        {{ booking.status?.replace('_',' ') }}
                                    </span>
                                    <span class="status-chip" :style="PAYMENT_STATUS[booking.payment_status] || PAYMENT_STATUS.default">
                                        {{ booking.payment_status?.replace('_',' ') }}
                                    </span>
                                    <div v-if="booking.due_date" class="due-date">Due: {{ fmtDate(booking.due_date) }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="td-actions">
                                    <Link :href="route('bookings.show', booking.id)" class="action-btn" title="View">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </Link>
                                    <Link :href="route('bookings.edit', booking.id)" class="action-btn" title="Edit">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </Link>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Pagination ── -->
            <div v-if="bookings?.data?.length" class="pagination-bar">
                <div class="pg-info">
                    Showing <strong>{{ bookings?.from ?? 0 }}</strong>–<strong>{{ bookings?.to ?? 0 }}</strong>
                    of <strong>{{ bookings?.total ?? 0 }}</strong> bookings
                </div>
                <nav class="pg-nav">
                    <template v-for="link in bookings?.links ?? []" :key="link.label">
                        <span v-if="!link.url" class="pg-link pg-disabled" v-html="link.label"></span>
                        <Link v-else :href="link.url" preserve-scroll
                              :class="['pg-link', link.active ? 'pg-active' : '']"
                              v-html="link.label">
                        </Link>
                    </template>
                </nav>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, reactive, watch } from 'vue'
import { debounce } from 'lodash'

const props = defineProps({
    bookings: Object,
    filters: Object,
    stats: Object,
})

const showAdvanced = ref(false)

const appliedFilters = reactive({
    search:         props.filters?.search         || '',
    status:         props.filters?.status         || '',
    payment_status: props.filters?.payment_status || '',
    start_date:     props.filters?.start_date     || '',
    end_date:       props.filters?.end_date       || '',
    event_type:     props.filters?.event_type     || '',
    min_amount:     props.filters?.min_amount     || '',
    max_amount:     props.filters?.max_amount     || '',
})

const debouncedFetch = debounce(() => {
    router.get(route('bookings.index'), appliedFilters, {
        preserveState: true, preserveScroll: true, replace: true,
    })
}, 300)

watch(appliedFilters, debouncedFetch, { deep: true })

const resetFilters = () => Object.assign(appliedFilters, {
    search:'', status:'', payment_status:'', start_date:'', end_date:'',
    event_type:'', min_amount:'', max_amount:'',
})

// ── Status maps ──────────────────────────────────────────────────────────────
const STATUS_COLORS = {
    pending:     '#b45309',
    confirmed:   '#16a34a',
    in_progress: '#1D5C96',
    completed:   '#C0170F',
    cancelled:   '#6B6560',
}

const BOOKING_STATUS = {
    pending:     { background:'rgba(249,178,51,.15)', color:'#b45309',  border:'1px solid rgba(249,178,51,.4)'  },
    confirmed:   { background:'rgba(22,163,74,.1)',   color:'#16a34a',  border:'1px solid rgba(22,163,74,.25)'  },
    in_progress: { background:'rgba(29,92,150,.1)',   color:'#1D5C96',  border:'1px solid rgba(29,92,150,.25)'  },
    completed:   { background:'rgba(192,23,15,.08)',  color:'#C0170F',  border:'1px solid rgba(192,23,15,.2)'   },
    cancelled:   { background:'rgba(107,101,96,.12)', color:'#6B6560',  border:'1px solid rgba(107,101,96,.3)'  },
    default:     { background:'rgba(158,152,144,.12)',color:'#6B6560',  border:'1px solid rgba(158,152,144,.3)' },
}

const PAYMENT_STATUS = {
    unpaid:         { background:'rgba(192,23,15,.08)',  color:'#C0170F',  border:'1px solid rgba(192,23,15,.2)'  },
    partially_paid: { background:'rgba(249,178,51,.12)', color:'#b45309',  border:'1px solid rgba(249,178,51,.3)' },
    paid:           { background:'rgba(22,163,74,.1)',   color:'#16a34a',  border:'1px solid rgba(22,163,74,.25)' },
    refunded:       { background:'rgba(29,92,150,.1)',   color:'#1D5C96',  border:'1px solid rgba(29,92,150,.2)'  },
    default:        { background:'rgba(158,152,144,.12)',color:'#6B6560',  border:'1px solid rgba(158,152,144,.3)'},
}

// ── Formatters ───────────────────────────────────────────────────────────────
const fmt = amount => parseFloat(amount||0).toLocaleString('en-US', { minimumFractionDigits:0, maximumFractionDigits:0 })

const fmtDate = d => {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' })
}

const fmtTime = t => {
    if (!t) return ''
    try {
        if (t.includes('T') || t.includes(' ')) return new Date(t).toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' })
        const [h, m] = t.split(':')
        const d = new Date(); d.setHours(parseInt(h), parseInt(m||0))
        return d.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' })
    } catch { return t }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

/* ── Page ── */
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}

/* ── Header ── */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(22px,3vw,30px);font-weight:900;color:#1A1410;line-height:1.15;margin-bottom:4px}
.page-sub{font-size:13px;color:#9E9890;font-family:'DM Mono',monospace}

/* ── Stat Cards ── */
.stats-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:22px}
@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
.stat-card{background:#fff;border:1px solid #E8E2DA;border-top:3px solid var(--accent);border-radius:16px;padding:16px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 8px rgba(0,0,0,.04);transition:transform .18s,box-shadow .18s}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.08)}
.stat-icon-wrap{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:#9E9890;margin-bottom:4px}
.stat-value{font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:#1A1410;line-height:1}

/* ── Filter Card ── */
.filter-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;box-shadow:0 1px 8px rgba(0,0,0,.04);margin-bottom:16px;overflow:hidden}
.filter-card-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 20px;border-bottom:1px solid #F0EDE8;background:#FAFAF8;flex-wrap:wrap}
.fch-title{font-family:'Playfair Display',serif;font-size:16px;font-weight:900;color:#1A1410}
.fch-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px}
.fch-actions{display:flex;gap:8px}

.filter-row{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:12px;padding:16px 20px}
@media(max-width:1024px){.filter-row{grid-template-columns:repeat(2,1fr)}}
@media(max-width:540px){.filter-row{grid-template-columns:1fr}}
.adv-filters{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:12px 20px 16px;background:#F7F5F2;border-top:1px solid #F0EDE8}
@media(max-width:768px){.adv-filters{grid-template-columns:1fr}}

.filter-field{display:flex;flex-direction:column;gap:5px}
.field-label{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#6B6560;font-weight:500}

.ep-input{width:100%;padding:8px 12px;border:1.5px solid #E8E2DA;border-radius:10px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;box-sizing:border-box}
.ep-input:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.ep-select{width:100%;padding:8px 12px;border:1.5px solid #E8E2DA;border-radius:10px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;cursor:pointer;transition:border-color .15s}
.ep-select:focus{border-color:#C0170F}

.search-wrap{position:relative}
.search-ico{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9E9890;pointer-events:none}
.search-pad{padding-left:30px !important}

.amt-wrap{position:relative}
.amt-prefix{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;pointer-events:none;font-weight:600}
.amt-pad{padding-left:36px !important}

/* ── Table ── */
.table-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04);margin-bottom:16px}
.table-scroll{overflow-x:auto}
.ep-table{width:100%;border-collapse:collapse;min-width:760px}
.ep-table thead tr{background:#F0EDE8;border-bottom:2px solid #E8E2DA}
.ep-table th{padding:11px 18px;text-align:left;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#6B6560;font-weight:700;white-space:nowrap}
.ep-table tbody tr{border-bottom:1px solid #F0EDE8}
.ep-row{transition:background .15s}
.ep-row:hover{background:#FAFAF8}
.ep-row:last-child{border-bottom:none}
.ep-table td{padding:14px 18px;vertical-align:middle}

/* ── Table cells ── */
.td-booking{display:flex;align-items:center;gap:12px}
.booking-avatar{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-family:'DM Mono',monospace;font-size:12px;font-weight:700;flex-shrink:0}
.bk-number{font-size:13px;font-weight:700;color:#1A1410;font-family:'DM Mono',monospace}
.bk-date{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px}
.bk-guests{font-size:11px;color:#9E9890;margin-top:2px}

.td-event{}
.ev-title{font-size:13px;font-weight:700;color:#1A1410;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ev-venue{display:flex;align-items:center;gap:4px;font-size:11px;color:#6B6560;font-family:'DM Mono',monospace;margin-top:3px}
.ev-date{font-size:11px;color:#9E9890;margin-top:2px;font-family:'DM Mono',monospace}

.td-finance{}
.fin-total{font-size:13px;font-weight:700;color:#1A1410}
.fin-paid{font-size:11px;color:#6B6560;margin-top:2px;font-family:'DM Mono',monospace}
.fin-due{font-size:11px;font-weight:700;color:#C0170F;margin-top:2px;font-family:'DM Mono',monospace}
.fin-clear{font-size:11px;font-weight:700;color:#16a34a;margin-top:2px;font-family:'DM Mono',monospace}

.td-status{display:flex;flex-direction:column;gap:5px;align-items:flex-start}
.status-chip{display:inline-flex;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;font-family:'DM Mono',monospace;text-transform:capitalize;letter-spacing:.03em}
.due-date{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px}

.td-actions{white-space:nowrap}
.action-btn{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;color:#9E9890;transition:all .15s;margin-left:4px;text-decoration:none}
.action-btn:hover{background:rgba(192,23,15,.08);color:#C0170F}
.action-btn:first-child{margin-left:0}

/* ── Empty state ── */
.empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 24px;text-align:center}
.empty-icon{font-size:44px;margin-bottom:12px;opacity:.4}
.empty-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#1A1410;margin-bottom:6px}
.empty-sub{font-size:13px;color:#9E9890;margin-bottom:16px}

/* ── Pagination ── */
.pagination-bar{background:#fff;border:1px solid #E8E2DA;border-radius:14px;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;box-shadow:0 1px 8px rgba(0,0,0,.04)}
.pg-info{font-family:'DM Mono',monospace;font-size:11px;color:#6B6560}
.pg-nav{display:flex;gap:3px;flex-wrap:wrap}
.pg-link{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 10px;border-radius:8px;font-family:'DM Mono',monospace;font-size:12px;font-weight:600;color:#6B6560;background:#F7F5F2;border:1px solid #E8E2DA;text-decoration:none;transition:all .15s;cursor:pointer}
.pg-link:hover{border-color:#C0170F;color:#C0170F;background:rgba(192,23,15,.05)}
.pg-active{background:#C0170F;color:#fff;border-color:#C0170F}
.pg-active:hover{background:#a31209;color:#fff}
.pg-disabled{opacity:.4;cursor:not-allowed}

/* ── Buttons ── */
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:11px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s;flex-shrink:0}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:9px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;white-space:nowrap}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
</style>