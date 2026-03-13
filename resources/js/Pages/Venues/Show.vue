<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('venues.index')" class="bc-link">Venues</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">{{ venue.name }}</span>
                    </div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Venue Detail</div>
                    <div class="title-row">
                        <h1 class="page-title">{{ venue.name }}</h1>
                        <span v-if="venue.is_featured" class="badge-featured">★ Featured</span>
                        <span v-if="venue.is_verified" class="badge-verified">✓ Verified</span>
                        <span class="badge-status" :style="venue.is_active ? {background:'rgba(22,163,74,.1)',color:'#16a34a',border:'1px solid rgba(22,163,74,.25)'} : {background:'rgba(192,23,15,.08)',color:'#C0170F',border:'1px solid rgba(192,23,15,.2)'}">
                            {{ venue.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="page-sub">{{ [venue.address, venue.city, venue.country].filter(Boolean).join(', ') }}</p>
                </div>
                <div class="header-actions">
                    <Link :href="route('venues.edit', venue.id)" class="btn-cta">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Venue
                    </Link>
                    <button @click="confirmDelete" :disabled="deleting" class="btn-ghost btn-danger-ghost">
                        <svg v-if="deleting" class="spin" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <svg v-else width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        {{ deleting ? 'Deleting…' : 'Delete' }}
                    </button>
                </div>
            </div>

            <!-- ── Body ── -->
            <div class="body-grid">

                <!-- ═══════════════════════════════════════════════ -->
                <!-- MAIN                                           -->
                <!-- ═══════════════════════════════════════════════ -->
                <div class="main-col">

                    <!-- Image Gallery -->
                    <div class="ep-card">
                        <div v-if="venue.images?.length">
                            <div class="gallery-main">
                                <img :src="venue.images[activeImage]" :alt="venue.name" class="gallery-img">
                                <div class="gallery-count">{{ activeImage + 1 }} / {{ venue.images.length }}</div>
                            </div>
                            <div v-if="venue.images.length > 1" class="gallery-thumbs">
                                <button v-for="(img, i) in venue.images" :key="i"
                                    @click="activeImage = i"
                                    class="thumb-btn" :class="{active: activeImage === i}">
                                    <img :src="img" class="thumb-img">
                                </button>
                            </div>
                        </div>
                        <div v-else class="gallery-empty">
                            <div class="gallery-empty-icon">🏛️</div>
                            <div class="gallery-empty-txt">No images uploaded</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="venue.description" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(29,92,150,.1)">📄</span>
                            <div>
                                <div class="card-title">About this Venue</div>
                                <div class="card-sub">Overview and description</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="desc-txt">{{ venue.description }}</p>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div v-if="venue.amenities?.length" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(22,163,74,.1)">✅</span>
                            <div>
                                <div class="card-title">Amenities</div>
                                <div class="card-sub">{{ venue.amenities.length }} features available</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="amenities-grid">
                                <div v-for="amenity in venue.amenities" :key="amenity" class="amenity-item">
                                    <div class="amenity-dot">
                                        <svg width="9" height="9" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                    </div>
                                    <span class="amenity-label">{{ formatAmenity(amenity) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div v-if="venue.availability?.length" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(249,178,51,.12)">📅</span>
                            <div>
                                <div class="card-title">Availability</div>
                                <div class="card-sub">Upcoming slots & schedule</div>
                            </div>
                        </div>
                        <div class="avail-list">
                            <div v-for="slot in venue.availability.slice(0, 6)" :key="slot.id" class="avail-row">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="avail-dot" :style="AVAIL_DOT[slot.status] || AVAIL_DOT.default"></span>
                                    <div>
                                        <div class="avail-date">{{ fmtDate(slot.date) }}</div>
                                        <div v-if="slot.start_time && slot.end_time" class="avail-time">{{ slot.start_time }} – {{ slot.end_time }}</div>
                                    </div>
                                </div>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span v-if="slot.price_override" class="avail-price">TZS {{ fmtPrice(slot.price_override) }}</span>
                                    <span class="avail-chip" :style="AVAIL_CHIP[slot.status] || AVAIL_CHIP.default">
                                        {{ slot.status?.replace('_',' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Policies -->
                    <div v-if="venue.terms_and_conditions || venue.cancellation_policy" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(107,101,96,.1)">📋</span>
                            <div>
                                <div class="card-title">Policies</div>
                                <div class="card-sub">Terms and cancellation policy</div>
                            </div>
                        </div>
                        <div class="card-body" style="display:flex;flex-direction:column;gap:16px">
                            <div v-if="venue.terms_and_conditions">
                                <div class="policy-label">Terms &amp; Conditions</div>
                                <p class="desc-txt">{{ venue.terms_and_conditions }}</p>
                            </div>
                            <div v-if="venue.cancellation_policy">
                                <div class="policy-label">Cancellation Policy</div>
                                <p class="desc-txt">{{ venue.cancellation_policy }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div v-if="venue.bookings?.length" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(192,23,15,.08)">🗒️</span>
                            <div>
                                <div class="card-title">Recent Bookings</div>
                                <div class="card-sub">{{ venue.total_bookings ?? venue.bookings.length }} total</div>
                            </div>
                        </div>
                        <div class="booking-list">
                            <div v-for="bk in venue.bookings.slice(0,5)" :key="bk.id" class="booking-row">
                                <div>
                                    <div class="booking-num">{{ bk.booking_number ?? bk.reference_number ?? '#' + bk.id }}</div>
                                    <div class="booking-date">{{ fmtDate(bk.event_date ?? bk.start_date) }}</div>
                                </div>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="booking-amount">TZS {{ fmtPrice(bk.total_amount) }}</span>
                                    <span class="booking-chip" :style="BOOKING_STATUS[bk.status] || BOOKING_STATUS.default">
                                        {{ bk.status?.replace('_',' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="booking-footer">
                            <Link :href="route('bookings.index', { venue_id: venue.id })" class="view-all-link">
                                View all bookings →
                            </Link>
                        </div>
                    </div>

                </div>

                <!-- ═══════════════════════════════════════════════ -->
                <!-- SIDEBAR                                        -->
                <!-- ═══════════════════════════════════════════════ -->
                <div class="sidebar-col">

                    <!-- Overview -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(29,92,150,.1)">📊</span>
                            <div><div class="card-title">Overview</div></div>
                        </div>
                        <div class="card-body">
                            <dl class="detail-list">
                                <div class="dl-row">
                                    <dt>Type</dt>
                                    <dd>{{ formatType(venue.type) }}</dd>
                                </div>
                                <div class="dl-row">
                                    <dt>Capacity</dt>
                                    <dd>{{ venue.capacity_min ?? '—' }}–{{ venue.capacity_max ?? '—' }} guests</dd>
                                </div>
                                <div v-if="venue.area_sqft" class="dl-row">
                                    <dt>Area</dt>
                                    <dd>{{ Number(venue.area_sqft).toLocaleString() }} sq ft</dd>
                                </div>
                                <div class="dl-row">
                                    <dt>Rating</dt>
                                    <dd>
                                        <span v-if="venue.rating" style="display:flex;align-items:center;gap:4px">
                                            <svg width="11" height="11" fill="#F9B233" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            {{ venue.rating }}
                                            <span style="color:#9E9890;font-size:10px">({{ venue.total_reviews ?? 0 }})</span>
                                        </span>
                                        <span v-else style="color:#9E9890">No reviews</span>
                                    </dd>
                                </div>
                                <div class="dl-row">
                                    <dt>Bookings</dt>
                                    <dd>{{ venue.total_bookings ?? 0 }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(22,163,74,.1)">💰</span>
                            <div><div class="card-title">Pricing</div></div>
                        </div>
                        <div class="card-body">
                            <dl class="detail-list">
                                <div class="dl-row">
                                    <dt>Per Day</dt>
                                    <dd style="color:#C0170F;font-weight:800">TZS {{ fmtPrice(venue.base_price_per_day) }}</dd>
                                </div>
                                <div v-if="venue.base_price_per_hour" class="dl-row">
                                    <dt>Per Hour</dt>
                                    <dd style="color:#C0170F;font-weight:800">TZS {{ fmtPrice(venue.base_price_per_hour) }}</dd>
                                </div>
                                <div v-if="venue.security_deposit" class="dl-row">
                                    <dt>Security Deposit</dt>
                                    <dd>TZS {{ fmtPrice(venue.security_deposit) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(192,23,15,.08)">👤</span>
                            <div><div class="card-title">Contact</div></div>
                        </div>
                        <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
                            <div v-if="venue.contact_person" class="contact-item">
                                <div class="contact-avatar" style="background:rgba(29,92,150,.12);color:#1D5C96">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <div>
                                    <div class="contact-lbl">Contact Person</div>
                                    <div class="contact-val">{{ venue.contact_person }}</div>
                                </div>
                            </div>
                            <div v-if="venue.email" class="contact-item">
                                <div class="contact-avatar" style="background:rgba(192,23,15,.08);color:#C0170F">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </div>
                                <div>
                                    <div class="contact-lbl">Email</div>
                                    <a :href="`mailto:${venue.email}`" class="contact-link">{{ venue.email }}</a>
                                </div>
                            </div>
                            <div v-if="venue.phone" class="contact-item">
                                <div class="contact-avatar" style="background:rgba(22,163,74,.1);color:#16a34a">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.59 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6.29 6.29l1.6-1.6a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </div>
                                <div>
                                    <div class="contact-lbl">Phone</div>
                                    <a :href="`tel:${venue.phone}`" class="contact-link">{{ venue.phone }}</a>
                                </div>
                            </div>
                            <p v-if="!venue.contact_person && !venue.email && !venue.phone" class="no-contact">No contact info provided</p>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(249,178,51,.12)">📍</span>
                            <div><div class="card-title">Location</div></div>
                        </div>
                        <div class="card-body">
                            <address class="address-txt not-italic">
                                <span v-if="venue.address">{{ venue.address }}<br></span>
                                {{ venue.city }}<span v-if="venue.state">, {{ venue.state }}</span>
                                <span v-if="venue.postal_code"> {{ venue.postal_code }}</span><br>
                                {{ venue.country }}
                            </address>
                            <a v-if="venue.latitude && venue.longitude"
                                :href="`https://maps.google.com/?q=${venue.latitude},${venue.longitude}`"
                                target="_blank" rel="noopener" class="maps-link">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                View on Google Maps
                            </a>
                        </div>
                    </div>

                    <!-- Virtual Tour -->
                    <div v-if="venue.virtual_tour_url" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(29,92,150,.1)">🎥</span>
                            <div><div class="card-title">Virtual Tour</div></div>
                        </div>
                        <div class="card-body">
                            <a :href="venue.virtual_tour_url" target="_blank" rel="noopener" class="tour-btn">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                Take Virtual Tour
                            </a>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="danger-card">
                        <div class="danger-head">⚠️ Danger Zone</div>
                        <p class="danger-txt">Permanently delete this venue and all its associated data. This cannot be undone.</p>
                        <button @click="confirmDelete" :disabled="deleting" class="btn-delete">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Venue
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({ venue: Object })

const activeImage = ref(0)
const deleting    = ref(false)

const confirmDelete = async () => {
    const result = await Swal.fire({
        title: 'Delete Venue?',
        html: `<p style="font-size:13px;color:#6B6560">You are about to permanently delete <strong style="color:#1A1410">${props.venue.name}</strong>.<br><span style="color:#C0170F;font-weight:600">All associated bookings and records will be removed. This cannot be undone.</span></p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#C0170F',
        cancelButtonColor: '#6B7280',
        focusCancel: true,
        reverseButtons: true,
    })
    if (!result.isConfirmed) return
    deleting.value = true
    router.delete(route('venues.destroy', props.venue.id), {
        onSuccess: () => {
            deleting.value = false
            Swal.fire({ title:'Deleted!', text:`${props.venue.name} has been deleted.`, icon:'success', timer:2000, showConfirmButton:false })
        },
        onError: (errors) => {
            deleting.value = false
            const msg = Object.values(errors)[0] ?? 'Failed to delete venue. It may have active bookings.'
            Swal.fire({ title:'Cannot Delete', text:msg, icon:'error', confirmButtonColor:'#C0170F' })
        },
    })
}

// Status maps
const BOOKING_STATUS = {
    pending:     { background:'rgba(249,178,51,.15)', color:'#b45309',  border:'1px solid rgba(249,178,51,.4)'  },
    confirmed:   { background:'rgba(22,163,74,.1)',   color:'#16a34a',  border:'1px solid rgba(22,163,74,.25)'  },
    in_progress: { background:'rgba(29,92,150,.1)',   color:'#1D5C96',  border:'1px solid rgba(29,92,150,.25)'  },
    completed:   { background:'rgba(192,23,15,.08)',  color:'#C0170F',  border:'1px solid rgba(192,23,15,.2)'   },
    cancelled:   { background:'rgba(107,101,96,.12)', color:'#6B6560',  border:'1px solid rgba(107,101,96,.3)'  },
    default:     { background:'rgba(158,152,144,.12)', color:'#6B6560', border:'1px solid rgba(158,152,144,.3)' },
}
const AVAIL_CHIP = {
    available:   { background:'rgba(22,163,74,.1)',   color:'#16a34a', border:'1px solid rgba(22,163,74,.25)'  },
    booked:      { background:'rgba(192,23,15,.08)',  color:'#C0170F', border:'1px solid rgba(192,23,15,.2)'   },
    blocked:     { background:'rgba(107,101,96,.12)', color:'#6B6560', border:'1px solid rgba(107,101,96,.3)'  },
    maintenance: { background:'rgba(249,178,51,.12)', color:'#b45309', border:'1px solid rgba(249,178,51,.3)'  },
    default:     { background:'rgba(158,152,144,.12)', color:'#6B6560', border:'1px solid rgba(158,152,144,.3)' },
}
const AVAIL_DOT = {
    available:   { background:'#16a34a' },
    booked:      { background:'#C0170F' },
    blocked:     { background:'#9E9890' },
    maintenance: { background:'#b45309' },
    default:     { background:'#9E9890' },
}

const formatType    = t => t ? t.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : '—'
const formatAmenity = a => a ? a.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : ''
const fmtPrice      = p => p ? Number(p).toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:0}) : '0'
const fmtDate       = d => { if (!d) return '—'; return new Date(d).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}) }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}

/* ── Header ── */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:26px;flex-wrap:wrap}
.header-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;flex-shrink:0}
.breadcrumb{display:flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;margin-bottom:5px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C0B8}.bc-cur{color:#6B6560}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.title-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:5px}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(20px,3vw,28px);font-weight:900;color:#1A1410;line-height:1.15;margin:0}
.page-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}
.badge-featured{padding:3px 10px;border-radius:20px;background:rgba(249,178,51,.18);border:1px solid rgba(249,178,51,.4);color:#b45309;font-family:'DM Mono',monospace;font-size:10px;font-weight:700}
.badge-verified{padding:3px 10px;border-radius:20px;background:rgba(22,163,74,.1);border:1px solid rgba(22,163,74,.25);color:#16a34a;font-family:'DM Mono',monospace;font-size:10px;font-weight:700}
.badge-status{padding:3px 10px;border-radius:20px;font-family:'DM Mono',monospace;font-size:10px;font-weight:700}

/* ── Layout ── */
.body-grid{display:grid;grid-template-columns:1fr 280px;gap:18px;align-items:start}
@media(max-width:1024px){.body-grid{grid-template-columns:1fr}}
.main-col{display:flex;flex-direction:column;gap:16px}
.sidebar-col{display:flex;flex-direction:column;gap:14px}

/* ── Cards ── */
.ep-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04)}
.card-head{display:flex;align-items:center;gap:12px;padding:13px 18px;background:#F0EDE8;border-bottom:1px solid #E8E2DA}
.card-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.card-title{font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:#1A1410;line-height:1.2}
.card-sub{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:1px}
.card-body{padding:16px 18px}
.desc-txt{font-size:13px;color:#4A4440;line-height:1.75;white-space:pre-line}
.policy-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.13em;color:#9E9890;font-weight:700;margin-bottom:6px}

/* ── Gallery ── */
.gallery-main{position:relative;height:280px;overflow:hidden}
.gallery-img{width:100%;height:100%;object-fit:cover}
.gallery-count{position:absolute;bottom:10px;right:12px;background:rgba(15,13,12,.55);color:#fff;font-family:'DM Mono',monospace;font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;backdrop-filter:blur(4px)}
.gallery-thumbs{display:flex;gap:8px;padding:12px;background:#F7F5F2;border-top:1px solid #E8E2DA;overflow-x:auto}
.thumb-btn{width:58px;height:58px;flex-shrink:0;border-radius:10px;overflow:hidden;border:2px solid transparent;cursor:pointer;transition:border-color .15s,box-shadow .15s;padding:0}
.thumb-btn.active{border-color:#C0170F;box-shadow:0 0 0 2px rgba(192,23,15,.2)}
.thumb-btn:hover:not(.active){border-color:#9E9890}
.thumb-img{width:100%;height:100%;object-fit:cover}
.gallery-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;height:180px;gap:8px;background:#F7F5F2}
.gallery-empty-icon{font-size:36px;opacity:.3}
.gallery-empty-txt{font-family:'DM Mono',monospace;font-size:11px;color:#9E9890}

/* ── Amenities ── */
.amenities-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
@media(max-width:640px){.amenities-grid{grid-template-columns:repeat(2,1fr)}}
.amenity-item{display:flex;align-items:center;gap:7px}
.amenity-dot{width:18px;height:18px;border-radius:50%;background:rgba(22,163,74,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.amenity-label{font-size:12px;color:#1A1410;font-weight:500}

/* ── Availability ── */
.avail-list{display:flex;flex-direction:column}
.avail-row{display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-bottom:1px solid #F0EDE8;transition:background .15s}
.avail-row:hover{background:#FAFAF8}
.avail-row:last-child{border-bottom:none}
.avail-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.avail-date{font-size:13px;font-weight:600;color:#1A1410}
.avail-time{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:2px}
.avail-price{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#1D5C96}
.avail-chip{display:inline-flex;padding:2px 9px;border-radius:20px;font-family:'DM Mono',monospace;font-size:10px;font-weight:700;text-transform:capitalize}

/* ── Bookings ── */
.booking-list{display:flex;flex-direction:column}
.booking-row{display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-bottom:1px solid #F0EDE8;transition:background .15s}
.booking-row:hover{background:#FAFAF8}
.booking-row:last-child{border-bottom:none}
.booking-num{font-family:'DM Mono',monospace;font-size:12px;font-weight:700;color:#1A1410}
.booking-date{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:2px}
.booking-amount{font-family:'DM Mono',monospace;font-size:12px;font-weight:700;color:#1A1410}
.booking-chip{display:inline-flex;padding:2px 9px;border-radius:20px;font-family:'DM Mono',monospace;font-size:10px;font-weight:700;text-transform:capitalize}
.booking-footer{padding:10px 18px;background:#F0EDE8;border-top:1px solid #E8E2DA}
.view-all-link{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#C0170F;text-decoration:none;transition:opacity .15s}
.view-all-link:hover{opacity:.7}

/* ── Sidebar cards ── */
.detail-list{display:flex;flex-direction:column;gap:0}
.dl-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #F7F5F2}
.dl-row:last-child{border-bottom:none}
.dl-row dt{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890}
.dl-row dd{font-size:12px;font-weight:600;color:#1A1410;font-family:'DM Mono',monospace;text-align:right}

/* ── Contact ── */
.contact-item{display:flex;align-items:center;gap:10px}
.contact-avatar{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.contact-lbl{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890;margin-bottom:2px}
.contact-val{font-size:13px;font-weight:600;color:#1A1410}
.contact-link{font-size:12px;font-weight:600;color:#C0170F;text-decoration:none;transition:opacity .15s}
.contact-link:hover{opacity:.7}
.no-contact{font-size:12px;color:#9E9890;font-style:italic}

/* ── Address ── */
.address-txt{font-size:13px;color:#4A4440;line-height:1.7;margin-bottom:10px}
.maps-link{display:inline-flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#C0170F;text-decoration:none;transition:opacity .15s}
.maps-link:hover{opacity:.7}

/* ── Virtual tour btn ── */
.tour-btn{display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:11px;border:1.5px solid rgba(29,92,150,.2);background:rgba(29,92,150,.07);color:#1D5C96;font-size:13px;font-weight:700;text-decoration:none;transition:all .18s;font-family:'DM Sans',sans-serif}
.tour-btn:hover{background:rgba(29,92,150,.14);border-color:#1D5C96}

/* ── Danger Zone ── */
.danger-card{background:#fff;border:1.5px solid rgba(192,23,15,.2);border-radius:16px;padding:16px;box-shadow:0 1px 8px rgba(192,23,15,.05)}
.danger-head{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#C0170F;margin-bottom:8px}
.danger-txt{font-size:12px;color:#9E9890;margin-bottom:12px;line-height:1.5}
.btn-delete{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:9px;border-radius:10px;border:1.5px solid rgba(192,23,15,.3);background:rgba(192,23,15,.06);color:#C0170F;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s}
.btn-delete:hover:not(:disabled){background:rgba(192,23,15,.12);border-color:#C0170F}
.btn-delete:disabled{opacity:.5;cursor:not-allowed}

/* ── Buttons ── */
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:12px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;text-decoration:none;white-space:nowrap}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-ghost:disabled{opacity:.5;cursor:not-allowed}
.btn-danger-ghost{border-color:rgba(192,23,15,.25);color:#C0170F}
.btn-danger-ghost:hover{border-color:#C0170F;background:rgba(192,23,15,.05)}
.spin{animation:spinAnim .7s linear infinite}
@keyframes spinAnim{to{transform:rotate(360deg)}}
</style>