<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('vendors.index')" class="bc-link">Vendors</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">{{ vendor.business_name }}</span>
                    </div>
                    <div class="name-row">
                        <h1 class="page-title">{{ vendor.business_name }}</h1>
                        <span class="verif-chip" :style="VERIF_STYLE[vendor.verification_status] || VERIF_STYLE.default">
                            <span class="status-dot" :style="{background: VERIF_DOT[vendor.verification_status] || '#9E9890'}"></span>
                            {{ formatVerification(vendor.verification_status) }}
                        </span>
                        <span v-if="vendor.is_featured" class="feat-badge">
                            <svg width="10" height="10" fill="#b45309" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            Featured
                        </span>
                    </div>
                    <p class="page-sub">{{ vendor.category?.name }}<span v-if="vendor.city"> · {{ vendor.city }}, {{ vendor.country }}</span></p>
                </div>
                <Link :href="route('vendors.edit', vendor.id)" class="btn-cta">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Vendor
                </Link>
            </div>

            <!-- ── Two-col layout ── -->
            <div class="layout">

                <!-- ═══ MAIN ═══ -->
                <div class="main-col">

                    <!-- Cover + About -->
                    <div class="ep-card">
                        <div class="cover-wrap">
                            <img v-if="vendor.cover_image" :src="vendor.cover_image" :alt="vendor.business_name" class="cover-img">
                            <div v-else class="cover-placeholder">
                                <svg width="40" height="40" fill="none" stroke="rgba(192,23,15,.3)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                            <div v-if="vendor.logo" class="logo-badge">
                                <img :src="vendor.logo" :alt="vendor.business_name" class="logo-badge-img">
                            </div>
                        </div>
                        <div class="about-body" :class="vendor.logo ? 'about-with-logo' : ''">
                            <div class="card-head-inline">
                                <span class="card-icon" style="background:rgba(29,92,150,.1)">🏢</span>
                                <span class="card-title">About</span>
                            </div>
                            <p v-if="vendor.description" class="about-text">{{ vendor.description }}</p>
                            <p v-else class="about-empty">No description provided.</p>
                        </div>
                    </div>

                    <!-- Service Areas & Specializations -->
                    <div v-if="vendor.service_areas?.length || vendor.specializations?.length" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(240,90,0,.1)">🗺️</span>
                            <div>
                                <div class="card-title">Service Areas &amp; Specializations</div>
                                <div class="card-sub">Where we operate and what we do best</div>
                            </div>
                        </div>
                        <div class="card-body grid-2">
                            <div v-if="vendor.service_areas?.length">
                                <div class="section-lbl">Service Areas</div>
                                <div class="tag-cloud">
                                    <span v-for="area in vendor.service_areas" :key="area" class="tag-navy">{{ area }}</span>
                                </div>
                            </div>
                            <div v-if="vendor.specializations?.length">
                                <div class="section-lbl">Specializations</div>
                                <div class="tag-cloud">
                                    <span v-for="spec in vendor.specializations" :key="spec" class="tag-amber">{{ spec }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div v-if="vendor.services?.length" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(22,163,74,.1)">⚙️</span>
                            <div>
                                <div class="card-title">Services</div>
                                <div class="card-sub">{{ vendor.services.length }} service{{ vendor.services.length !== 1 ? 's' : '' }} offered</div>
                            </div>
                        </div>
                        <div class="services-list">
                            <div v-for="service in vendor.services" :key="service.id" class="service-row">
                                <div>
                                    <div class="service-name">{{ service.name }}</div>
                                    <div v-if="service.description" class="service-desc">{{ service.description }}</div>
                                </div>
                                <span v-if="service.price" class="service-price">TZS {{ formatPrice(service.price) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Portfolio -->
                    <div v-if="vendor.portfolios?.length" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(249,178,51,.12)">🖼️</span>
                            <div>
                                <div class="card-title">Portfolio</div>
                                <div class="card-sub">{{ vendor.portfolios.length }} item{{ vendor.portfolios.length !== 1 ? 's' : '' }}</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="portfolio-grid">
                                <div v-for="item in vendor.portfolios" :key="item.id" class="portfolio-item">
                                    <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="portfolio-img">
                                    <div v-else class="portfolio-placeholder">
                                        <svg width="20" height="20" fill="none" stroke="#C8C0B8" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div v-if="vendor.reviews?.length" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(192,23,15,.08)">⭐</span>
                            <div>
                                <div class="card-title">Reviews</div>
                                <div class="card-sub">{{ vendor.reviews_count }} total</div>
                            </div>
                        </div>
                        <div class="reviews-list">
                            <div v-for="review in vendor.reviews.slice(0,5)" :key="review.id" class="review-row">
                                <div class="review-top">
                                    <div class="review-avatar">{{ (review.user?.name || 'A')[0].toUpperCase() }}</div>
                                    <span class="review-author">{{ review.user?.name ?? 'Anonymous' }}</span>
                                    <div class="stars">
                                        <svg v-for="s in 5" :key="s" width="12" height="12" :fill="s <= review.rating ? '#F9B233' : '#E8E2DA'" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    </div>
                                </div>
                                <p v-if="review.comment" class="review-text">{{ review.comment }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ═══ SIDEBAR ═══ -->
                <div class="sidebar-col">

                    <!-- Overview -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(29,92,150,.1)">📊</span>
                            <div><div class="card-title">Overview</div></div>
                        </div>
                        <div class="dl-list">
                            <div class="dl-row">
                                <span class="dl-label">Status</span>
                                <span class="status-chip" :style="vendor.is_active ? ACTIVE_ON : ACTIVE_OFF">
                                    <span class="status-dot" :style="{background: vendor.is_active ? '#16a34a' : '#9E9890'}"></span>
                                    {{ vendor.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="dl-row">
                                <span class="dl-label">Category</span>
                                <span class="dl-value">{{ vendor.category?.name ?? '—' }}</span>
                            </div>
                            <div class="dl-row">
                                <span class="dl-label">Rating</span>
                                <div v-if="vendor.rating" class="rating-row">
                                    <svg width="13" height="13" fill="#F9B233" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <span class="dl-value">{{ vendor.rating }}</span>
                                    <span class="dl-muted">({{ vendor.total_reviews }})</span>
                                </div>
                                <span v-else class="dl-muted">No reviews</span>
                            </div>
                            <div class="dl-row">
                                <span class="dl-label">Experience</span>
                                <span class="dl-value">{{ vendor.years_of_experience ? vendor.years_of_experience + ' yrs' : '—' }}</span>
                            </div>
                            <div class="dl-row">
                                <span class="dl-label">Team Size</span>
                                <span class="dl-value">{{ vendor.team_size ?? '—' }}</span>
                            </div>
                            <div v-if="vendor.minimum_order_value" class="dl-row">
                                <span class="dl-label">Min. Order</span>
                                <span class="dl-value" style="color:#C0170F;font-weight:800">TZS {{ formatPrice(vendor.minimum_order_value) }}</span>
                            </div>
                            <div class="dl-row">
                                <span class="dl-label">Bookings</span>
                                <span class="dl-value">{{ vendor.total_bookings ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(240,90,0,.1)">📞</span>
                            <div><div class="card-title">Contact</div></div>
                        </div>
                        <div class="contact-list">
                            <div v-if="vendor.contact_person" class="contact-row">
                                <div class="contact-icon" style="background:rgba(29,92,150,.12);color:#1D5C96">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <div>
                                    <div class="contact-lbl">Contact Person</div>
                                    <div class="contact-val">{{ vendor.contact_person }}</div>
                                </div>
                            </div>
                            <div v-if="vendor.email" class="contact-row">
                                <div class="contact-icon" style="background:rgba(22,163,74,.1);color:#16a34a">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </div>
                                <div>
                                    <div class="contact-lbl">Email</div>
                                    <a :href="`mailto:${vendor.email}`" class="contact-link">{{ vendor.email }}</a>
                                </div>
                            </div>
                            <div v-if="vendor.phone" class="contact-row">
                                <div class="contact-icon" style="background:rgba(249,178,51,.12);color:#b45309">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.65 3.32 2 2 0 0 1 3.62 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6 6l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </div>
                                <div>
                                    <div class="contact-lbl">Phone</div>
                                    <a :href="`tel:${vendor.phone}`" class="contact-link">{{ vendor.phone }}</a>
                                </div>
                            </div>
                            <div v-if="vendor.website" class="contact-row">
                                <div class="contact-icon" style="background:rgba(192,23,15,.08);color:#C0170F">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                </div>
                                <div>
                                    <div class="contact-lbl">Website</div>
                                    <a :href="vendor.website" target="_blank" rel="noopener" class="contact-link truncate-link">{{ vendor.website }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(249,178,51,.12)">📍</span>
                            <div><div class="card-title">Location</div></div>
                        </div>
                        <div style="padding:14px 16px">
                            <address class="address-block">
                                <div v-if="vendor.address">{{ vendor.address }}</div>
                                <div>{{ vendor.city }}<span v-if="vendor.state">, {{ vendor.state }}</span><span v-if="vendor.postal_code"> {{ vendor.postal_code }}</span></div>
                                <div>{{ vendor.country }}</div>
                            </address>
                            <a v-if="vendor.city"
                               :href="`https://www.google.com/maps/search/${encodeURIComponent([vendor.address, vendor.city, vendor.country].filter(Boolean).join(', '))}`"
                               target="_blank" rel="noopener" class="map-link">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                View on Google Maps
                            </a>
                        </div>
                    </div>

                    <!-- Business Info -->
                    <div v-if="vendor.business_registration_number || vendor.tax_id || vendor.verified_at" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(22,163,74,.1)">📄</span>
                            <div><div class="card-title">Business Info</div></div>
                        </div>
                        <div class="dl-list">
                            <div v-if="vendor.business_registration_number" class="dl-row">
                                <span class="dl-label">Reg. Number</span>
                                <span class="dl-mono">{{ vendor.business_registration_number }}</span>
                            </div>
                            <div v-if="vendor.tax_id" class="dl-row">
                                <span class="dl-label">Tax ID</span>
                                <span class="dl-mono">{{ vendor.tax_id }}</span>
                            </div>
                            <div v-if="vendor.verified_at" class="dl-row">
                                <span class="dl-label">Verified At</span>
                                <span class="dl-value">{{ formatDate(vendor.verified_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="ep-card danger-card">
                        <div class="card-head" style="background:rgba(192,23,15,.05);border-bottom-color:rgba(192,23,15,.15)">
                            <span class="card-icon" style="background:rgba(192,23,15,.1)">⚠️</span>
                            <div><div class="card-title" style="color:#C0170F">Danger Zone</div></div>
                        </div>
                        <div style="padding:14px 16px">
                            <button @click="showDeleteModal = true" class="btn-danger-full">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                Delete Vendor
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Delete Modal ── -->
            <Teleport to="body">
                <div v-if="showDeleteModal" class="modal-backdrop" @click.self="showDeleteModal = false">
                    <div class="modal-box">
                        <div class="modal-head">
                            <div class="modal-icon-wrap" style="background:rgba(192,23,15,.1)">
                                <svg width="20" height="20" fill="none" stroke="#C0170F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </div>
                            <div>
                                <div class="modal-title">Delete Vendor</div>
                                <div class="modal-sub">This action cannot be undone.</div>
                            </div>
                        </div>
                        <p class="modal-body">Are you sure you want to delete <strong style="color:#1A1410">{{ vendor.business_name }}</strong>? All associated data will be permanently removed.</p>
                        <div class="modal-actions">
                            <button @click="showDeleteModal = false" class="btn-ghost">Cancel</button>
                            <button @click="deleteVendor" class="btn-danger">Delete Vendor</button>
                        </div>
                    </div>
                </div>
            </Teleport>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ vendor: Object })
const showDeleteModal = ref(false)

const VERIF_STYLE = {
    pending:  { background:'rgba(249,178,51,.12)', color:'#b45309', border:'1px solid rgba(249,178,51,.4)'  },
    verified: { background:'rgba(22,163,74,.1)',   color:'#16a34a', border:'1px solid rgba(22,163,74,.25)'  },
    rejected: { background:'rgba(192,23,15,.08)',  color:'#C0170F', border:'1px solid rgba(192,23,15,.2)'   },
    default:  { background:'rgba(158,152,144,.12)',color:'#6B6560', border:'1px solid rgba(158,152,144,.3)' },
}
const VERIF_DOT  = { pending:'#b45309', verified:'#16a34a', rejected:'#C0170F' }
const ACTIVE_ON  = { background:'rgba(22,163,74,.1)',   color:'#16a34a', border:'1px solid rgba(22,163,74,.25)'  }
const ACTIVE_OFF = { background:'rgba(158,152,144,.12)',color:'#6B6560', border:'1px solid rgba(158,152,144,.3)' }

const formatVerification = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : ''
const formatPrice = n => n ? Number(n).toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:0}) : '0'
const formatDate  = d => d ? new Date(d).toLocaleDateString('en-GB',{year:'numeric',month:'short',day:'numeric'}) : ''

const deleteVendor = () => {
    router.delete(route('vendors.destroy', props.vendor.id), {
        onSuccess: () => { showDeleteModal.value = false }
    })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap}
.breadcrumb{display:flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;margin-bottom:6px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C0B8}.bc-cur{color:#6B6560}
.name-row{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:5px}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(20px,3vw,28px);font-weight:900;color:#1A1410;line-height:1.15}
.page-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}
.verif-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-family:'DM Mono',monospace;font-size:9px;font-weight:700}
.feat-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;background:rgba(249,178,51,.12);color:#b45309;border:1px solid rgba(249,178,51,.4);font-family:'DM Mono',monospace;font-size:9px;font-weight:700}
.status-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0}
.layout{display:grid;grid-template-columns:1fr 280px;gap:18px;align-items:start}
@media(max-width:1024px){.layout{grid-template-columns:1fr}}
.main-col{display:flex;flex-direction:column;gap:16px}
.sidebar-col{display:flex;flex-direction:column;gap:14px}
.ep-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04)}
.card-head{display:flex;align-items:center;gap:12px;padding:13px 18px;background:#F0EDE8;border-bottom:1px solid #E8E2DA}
.card-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.card-title{font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:#1A1410;line-height:1.2}
.card-sub{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:1px}
.card-body{padding:18px}
.card-head-inline{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:600px){.grid-2{grid-template-columns:1fr}}
.cover-wrap{position:relative;height:200px;background:linear-gradient(135deg,rgba(192,23,15,.08),rgba(29,92,150,.08))}
.cover-img{width:100%;height:100%;object-fit:cover}
.cover-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.logo-badge{position:absolute;bottom:-18px;left:20px;width:52px;height:52px;border-radius:12px;border:3px solid #fff;overflow:hidden;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.12)}
.logo-badge-img{width:100%;height:100%;object-fit:cover}
.about-body{padding:18px}
.about-with-logo{padding-top:30px}
.about-text{font-size:13px;color:#6B6560;line-height:1.7}
.about-empty{font-size:12px;color:#C8C0B8;font-style:italic}
.section-lbl{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.14em;color:#9E9890;font-weight:700;margin-bottom:8px}
.tag-cloud{display:flex;flex-wrap:wrap;gap:5px}
.tag-navy{padding:4px 10px;border-radius:20px;background:rgba(29,92,150,.1);color:#1D5C96;border:1px solid rgba(29,92,150,.25);font-family:'DM Mono',monospace;font-size:9px;font-weight:700}
.tag-amber{padding:4px 10px;border-radius:20px;background:rgba(249,178,51,.12);color:#b45309;border:1px solid rgba(249,178,51,.3);font-family:'DM Mono',monospace;font-size:9px;font-weight:700}
.services-list{padding:0 18px;display:flex;flex-direction:column}
.service-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid #F7F5F2}
.service-row:last-child{border-bottom:none}
.service-name{font-size:13px;font-weight:700;color:#1A1410}
.service-desc{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:2px}
.service-price{font-family:'DM Mono',monospace;font-size:11px;font-weight:800;color:#C0170F;white-space:nowrap;flex-shrink:0}
.portfolio-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.portfolio-item{aspect-ratio:1;border-radius:10px;overflow:hidden;background:#F7F5F2;border:1px solid #E8E2DA}
.portfolio-img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.portfolio-item:hover .portfolio-img{transform:scale(1.05)}
.portfolio-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.reviews-list{padding:0 18px;display:flex;flex-direction:column}
.review-row{padding:12px 0;border-bottom:1px solid #F7F5F2}
.review-row:last-child{border-bottom:none}
.review-top{display:flex;align-items:center;gap:8px;margin-bottom:5px}
.review-avatar{width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#C0170F,#F05A00);color:#fff;font-family:'DM Mono',monospace;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.review-author{font-size:12px;font-weight:700;color:#1A1410;flex:1}
.stars{display:flex;gap:2px}
.review-text{font-size:12px;color:#6B6560;line-height:1.5;padding-left:34px}
.dl-list{padding:0 16px;display:flex;flex-direction:column}
.dl-row{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:9px 0;border-bottom:1px solid #F7F5F2}
.dl-row:last-child{border-bottom:none}
.dl-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890;font-weight:700;flex-shrink:0}
.dl-value{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#1A1410}
.dl-mono{font-family:'DM Mono',monospace;font-size:11px;color:#1A1410}
.dl-muted{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890}
.rating-row{display:flex;align-items:center;gap:4px}
.status-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-family:'DM Mono',monospace;font-size:9px;font-weight:700}
.contact-list{padding:0 16px 6px;display:flex;flex-direction:column;gap:10px}
.contact-row{display:flex;align-items:center;gap:10px;padding:6px 0}
.contact-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.contact-lbl{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890;font-weight:700}
.contact-val{font-size:12px;font-weight:700;color:#1A1410;margin-top:1px}
.contact-link{font-size:12px;font-weight:700;color:#C0170F;text-decoration:none;transition:opacity .15s;display:block;margin-top:1px}
.contact-link:hover{opacity:.75}
.truncate-link{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px}
.address-block{font-size:12px;color:#6B6560;line-height:1.8;font-style:normal;font-family:'DM Mono',monospace;margin-bottom:10px}
.map-link{display:inline-flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;font-weight:700;color:#1D5C96;text-decoration:none;transition:opacity .15s}
.map-link:hover{opacity:.75}
.danger-card{border-color:rgba(192,23,15,.2)}
.btn-danger-full{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:10px 16px;border-radius:11px;border:1.5px solid rgba(192,23,15,.3);background:rgba(192,23,15,.06);color:#C0170F;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .15s}
.btn-danger-full:hover{background:rgba(192,23,15,.12);border-color:rgba(192,23,15,.5)}
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:12px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-ghost{padding:9px 18px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-danger{padding:9px 18px;border-radius:11px;border:none;background:#C0170F;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;transition:background .15s}
.btn-danger:hover{background:#a01209}
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;display:flex;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px)}
.modal-box{background:#fff;border-radius:20px;padding:24px;max-width:420px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.modal-head{display:flex;align-items:center;gap:14px;margin-bottom:14px}
.modal-icon-wrap{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.modal-title{font-family:'Playfair Display',serif;font-size:16px;font-weight:900;color:#1A1410}
.modal-sub{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:2px}
.modal-body{font-size:13px;color:#6B6560;line-height:1.6;margin-bottom:20px}
.modal-actions{display:flex;justify-content:flex-end;gap:10px}
</style>