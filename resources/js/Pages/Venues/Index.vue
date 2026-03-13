<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Venue Management</div>
                    <h1 class="page-title">Venues</h1>
                    <p class="page-sub">Manage and organize all your event venues</p>
                </div>
                <Link :href="route('venues.create')" class="btn-cta">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Add Venue
                </Link>
            </div>

            <!-- ── Stat Cards ── -->
            <div v-if="venues.data.length > 0" class="stats-grid">
                <div class="stat-card" style="--accent:#1D5C96">
                    <div class="stat-icon" style="background:rgba(29,92,150,.1)">
                        <svg width="18" height="18" fill="none" stroke="#1D5C96" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Total Venues</div>
                        <div class="stat-value">{{ venues.total }}</div>
                        <div class="stat-note">All venues combined</div>
                    </div>
                </div>

                <div class="stat-card" style="--accent:#16a34a">
                    <div class="stat-icon" style="background:rgba(22,163,74,.1)">
                        <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Active</div>
                        <div class="stat-value">{{ venues.data.filter(v => v.is_active).length }}</div>
                        <div class="stat-note" style="color:#16a34a">Currently available</div>
                    </div>
                </div>

                <div class="stat-card" style="--accent:#b45309">
                    <div class="stat-icon" style="background:rgba(249,178,51,.12)">
                        <svg width="18" height="18" fill="none" stroke="#b45309" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Verified</div>
                        <div class="stat-value">{{ venues.data.filter(v => v.is_verified).length }}</div>
                        <div class="stat-note" style="color:#b45309">Quality assured</div>
                    </div>
                </div>

                <div class="stat-card" style="--accent:#C0170F">
                    <div class="stat-icon" style="background:rgba(192,23,15,.08)">
                        <svg width="18" height="18" fill="none" stroke="#C0170F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Featured</div>
                        <div class="stat-value">{{ venues.data.filter(v => v.is_featured).length }}</div>
                        <div class="stat-note" style="color:#C0170F">Highlighted venues</div>
                    </div>
                </div>
            </div>

            <!-- ── Filter Card ── -->
            <div class="filter-card">
                <div class="filter-head">
                    <div>
                        <div class="filter-title">Filter Venues</div>
                        <div class="filter-sub">Refine your search using the options below</div>
                    </div>
                    <button v-if="hasActiveFilters" @click="resetFilters" class="clear-btn">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        Clear Filters
                    </button>
                </div>

                <!-- Row 1 -->
                <div class="filter-row">
                    <div class="search-wrap" style="grid-column:span 2">
                        <svg class="search-ico" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input v-model="form.search" @input="search" type="text" placeholder="Search by name, description, address or city…" class="ep-input search-pad">
                    </div>
                    <select v-model="form.type" @change="search" class="ep-select">
                        <option value="">All Types</option>
                        <option value="indoor">Indoor</option>
                        <option value="outdoor">Outdoor</option>
                        <option value="banquet_hall">Banquet Hall</option>
                        <option value="garden">Garden</option>
                        <option value="rooftop">Rooftop</option>
                        <option value="beach">Beach</option>
                        <option value="hotel">Hotel</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="other">Other</option>
                    </select>
                    <input v-model="form.city" @input="search" type="text" placeholder="Filter by city…" class="ep-input">
                </div>

                <!-- Row 2 -->
                <div class="filter-row" style="grid-template-columns:repeat(5,1fr)">
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">Min</span>
                        <input v-model="form.min_capacity" @input="search" type="number" min="0" placeholder="Min guests" class="ep-input prefix-pad">
                    </div>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">Max</span>
                        <input v-model="form.max_capacity" @input="search" type="number" min="0" placeholder="Max guests" class="ep-input prefix-pad">
                    </div>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">TZS</span>
                        <input v-model="form.min_price" @input="search" type="number" min="0" placeholder="Min price/day" class="ep-input prefix-pad-lg">
                    </div>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">TZS</span>
                        <input v-model="form.max_price" @input="search" type="number" min="0" placeholder="Max price/day" class="ep-input prefix-pad-lg">
                    </div>
                    <div style="display:flex;gap:6px">
                        <select v-model="form.is_verified" @change="search" class="ep-select" style="flex:1">
                            <option value="">All</option>
                            <option value="1">Verified</option>
                            <option value="0">Unverified</option>
                        </select>
                        <select v-model="form.is_featured" @change="search" class="ep-select" style="flex:1">
                            <option value="">All</option>
                            <option value="1">Featured</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ── Results count ── -->
            <div v-if="venues.data.length > 0" class="results-bar">
                Showing <strong>{{ venues.from }}</strong>–<strong>{{ venues.to }}</strong> of <strong>{{ venues.total }}</strong> venues
                <span class="results-note">sorted by featured &amp; rating</span>
            </div>

            <!-- ── Venues Grid ── -->
            <div class="venues-grid">
                <div v-for="venue in venues.data" :key="venue.id" class="venue-card">

                    <!-- Image -->
                    <div class="venue-img-wrap">
                        <img v-if="venue.images?.length" :src="venue.images[0]" :alt="venue.name" class="venue-img">
                        <div v-else class="venue-img-empty">
                            <svg width="36" height="36" fill="none" stroke="#C8C0B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>

                        <!-- Top-left badges -->
                        <div class="img-badges-left">
                            <span v-if="venue.is_featured" class="badge-featured">★ Featured</span>
                            <span v-if="venue.is_verified" class="badge-verified">✓ Verified</span>
                        </div>

                        <!-- Top-right status -->
                        <div class="img-badges-right">
                            <span class="badge-status" :style="venue.is_active ? {background:'rgba(22,163,74,.85)',color:'#fff'} : {background:'rgba(192,23,15,.8)',color:'#fff'}">
                                {{ venue.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <!-- Gradient overlay at bottom -->
                        <div class="img-gradient"></div>
                    </div>

                    <!-- Body -->
                    <div class="venue-body">
                        <!-- Type + Rating -->
                        <div class="venue-meta-row">
                            <span class="type-chip">{{ formatType(venue.type) }}</span>
                            <div v-if="venue.rating" class="rating-row">
                                <svg width="12" height="12" fill="#F9B233" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <span class="rating-val">{{ venue.rating }}</span>
                                <span class="rating-count">({{ venue.total_reviews }})</span>
                            </div>
                        </div>

                        <!-- Name -->
                        <h3 class="venue-name">{{ venue.name }}</h3>

                        <!-- Details -->
                        <div class="venue-details">
                            <div class="detail-row">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span class="detail-txt truncate">{{ [venue.city, venue.country].filter(Boolean).join(', ') }}</span>
                            </div>
                            <div class="detail-row">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <span class="detail-txt">{{ venue.capacity_min }}–{{ venue.capacity_max }} guests</span>
                            </div>
                            <div class="detail-row">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 2"/></svg>
                                <span class="detail-price">TZS {{ formatPrice(venue.base_price_per_day) }}</span>
                                <span class="detail-per">/day</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="venue-footer">
                            <div class="venue-actions">
                                <Link :href="route('venues.edit', venue.id)" class="action-icon-btn" title="Edit">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </Link>
                                <Link :href="route('venues.show', venue.id)" class="view-btn">
                                    View Details
                                </Link>
                            </div>
                            <span class="booking-count">{{ venue.total_bookings ?? 0 }} bookings</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Empty State ── -->
            <div v-if="venues.data.length === 0" class="empty-state">
                <div class="empty-icon">🏛️</div>
                <h3 class="empty-title">No venues found</h3>
                <p class="empty-sub">
                    {{ hasActiveFilters ? "Try adjusting your filters to find what you're looking for." : 'Get started by adding your first venue.' }}
                </p>
                <div class="empty-actions">
                    <Link v-if="!hasActiveFilters" :href="route('venues.create')" class="btn-cta">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        Add First Venue
                    </Link>
                    <button v-else @click="resetFilters" class="btn-ghost">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- ── Pagination ── -->
            <div v-if="venues.data.length > 0" class="pagination-wrap">
                <div class="pg-info">
                    Showing <strong>{{ venues.from }}</strong>–<strong>{{ venues.to }}</strong> of <strong>{{ venues.total }}</strong>
                </div>
                <nav class="pg-nav">
                    <template v-for="(link, i) in venues.links" :key="i">
                        <button v-if="i === 0" @click="goToPage(link.url)" :disabled="!link.url" class="pg-link" :class="{disabled:!link.url}">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button v-else-if="i !== venues.links.length - 1" @click="goToPage(link.url)" :disabled="link.active || !link.url"
                            :class="['pg-link', link.active ? 'pg-active' : '', !link.url ? 'disabled' : '']"
                            v-html="link.label">
                        </button>
                        <button v-else @click="goToPage(link.url)" :disabled="!link.url" class="pg-link" :class="{disabled:!link.url}">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </template>
                </nav>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'

const props = defineProps({ venues: Object, filters: Object })

const form = reactive({
    search:       props.filters.search       || '',
    type:         props.filters.type         || '',
    city:         props.filters.city         || '',
    min_capacity: props.filters.min_capacity || '',
    max_capacity: props.filters.max_capacity || '',
    min_price:    props.filters.min_price    || '',
    max_price:    props.filters.max_price    || '',
    is_verified:  props.filters.is_verified  ?? '',
    is_featured:  props.filters.is_featured  ?? '',
})

const hasActiveFilters = computed(() =>
    form.search || form.type || form.city ||
    form.min_capacity || form.max_capacity ||
    form.min_price || form.max_price ||
    form.is_verified !== '' || form.is_featured !== ''
)

const search = () => router.get(route('venues.index'), form, { preserveState: true, preserveScroll: true })

const resetFilters = () => {
    Object.assign(form, { search:'', type:'', city:'', min_capacity:'', max_capacity:'', min_price:'', max_price:'', is_verified:'', is_featured:'' })
    search()
}

const goToPage = (url) => {
    if (!url) return
    router.visit(url, { preserveState: true, preserveScroll: true })
}

const formatType  = t => t ? t.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : ''
const formatPrice = p => p ? Number(p).toLocaleString('en-US', { minimumFractionDigits:0, maximumFractionDigits:0 }) : '0'
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}

/* ── Header ── */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(22px,3vw,30px);font-weight:900;color:#1A1410;line-height:1.15;margin-bottom:4px}
.page-sub{font-size:13px;color:#9E9890;font-family:'DM Mono',monospace}

/* ── Stats ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
@media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:500px){.stats-grid{grid-template-columns:1fr}}
.stat-card{background:#fff;border:1px solid #E8E2DA;border-top:3px solid var(--accent);border-radius:16px;padding:16px;display:flex;align-items:flex-start;gap:12px;box-shadow:0 1px 8px rgba(0,0,0,.04);transition:transform .18s,box-shadow .18s}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.08)}
.stat-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:#9E9890;margin-bottom:4px}
.stat-value{font-family:'Playfair Display',serif;font-size:26px;font-weight:900;color:#1A1410;line-height:1}
.stat-note{font-family:'DM Mono',monospace;font-size:9px;color:#9E9890;margin-top:4px}

/* ── Filters ── */
.filter-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04);margin-bottom:16px}
.filter-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:#F0EDE8;border-bottom:1px solid #E8E2DA}
.filter-title{font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:#1A1410}
.filter-sub{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px}
.clear-btn{display:inline-flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#C0170F;background:none;border:none;cursor:pointer;padding:5px 10px;border-radius:8px;transition:background .15s}
.clear-btn:hover{background:rgba(192,23,15,.07)}
.filter-row{display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;padding:14px 20px;border-bottom:1px solid #F0EDE8}
.filter-row:last-child{border-bottom:none}
@media(max-width:768px){.filter-row,.filter-row[style]{grid-template-columns:1fr !important}}
.ep-input,.ep-select{padding:9px 12px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;width:100%}
.ep-input:focus,.ep-select:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.search-wrap{position:relative}
.search-ico{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9E9890;pointer-events:none}
.search-pad{padding-left:32px !important}
.input-prefix-wrap{position:relative}
.input-prefix{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-family:'DM Mono',monospace;font-size:9px;color:#9E9890;pointer-events:none;letter-spacing:.08em}
.prefix-pad{padding-left:32px !important}
.prefix-pad-lg{padding-left:38px !important}

/* ── Results bar ── */
.results-bar{font-family:'DM Mono',monospace;font-size:11px;color:#6B6560;margin-bottom:14px}
.results-note{color:#9E9890;margin-left:6px}

/* ── Venues grid ── */
.venues-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
@media(max-width:1024px){.venues-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:640px){.venues-grid{grid-template-columns:1fr}}

/* ── Venue card ── */
.venue-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04);transition:transform .2s,box-shadow .2s}
.venue-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.1)}
.venue-img-wrap{position:relative;height:180px;background:#F0EDE8;overflow:hidden}
.venue-img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
.venue-card:hover .venue-img{transform:scale(1.05)}
.venue-img-empty{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.img-badges-left{position:absolute;top:10px;left:10px;display:flex;gap:5px;flex-wrap:wrap}
.badge-featured{padding:3px 9px;border-radius:20px;background:rgba(249,178,51,.95);color:#7c2d12;font-size:10px;font-weight:700;font-family:'DM Mono',monospace;backdrop-filter:blur(4px)}
.badge-verified{padding:3px 9px;border-radius:20px;background:rgba(22,163,74,.9);color:#fff;font-size:10px;font-weight:700;font-family:'DM Mono',monospace}
.img-badges-right{position:absolute;top:10px;right:10px}
.badge-status{padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;font-family:'DM Mono',monospace;backdrop-filter:blur(4px)}
.img-gradient{position:absolute;bottom:0;left:0;right:0;height:60px;background:linear-gradient(to top,rgba(26,20,16,.35),transparent);pointer-events:none}

/* Venue body */
.venue-body{padding:14px 16px}
.venue-meta-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:7px}
.type-chip{display:inline-flex;padding:2px 10px;border-radius:20px;background:rgba(29,92,150,.1);border:1px solid rgba(29,92,150,.2);color:#1D5C96;font-family:'DM Mono',monospace;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:capitalize}
.rating-row{display:flex;align-items:center;gap:3px}
.rating-val{font-size:12px;font-weight:700;color:#1A1410;font-family:'DM Mono',monospace}
.rating-count{font-size:11px;color:#9E9890}
.venue-name{font-family:'Playfair Display',serif;font-size:16px;font-weight:900;color:#1A1410;margin-bottom:10px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
.venue-details{display:flex;flex-direction:column;gap:5px;margin-bottom:12px}
.detail-row{display:flex;align-items:center;gap:6px;color:#6B6560}
.detail-txt{font-size:12px;font-family:'DM Mono',monospace}
.detail-price{font-size:12px;font-weight:700;color:#1A1410;font-family:'DM Mono',monospace}
.detail-per{font-size:11px;color:#9E9890}
.venue-footer{display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid #F0EDE8}
.venue-actions{display:flex;align-items:center;gap:6px}
.action-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;border:1.5px solid #E8E2DA;background:#F7F5F2;color:#6B6560;transition:all .15s;text-decoration:none}
.action-icon-btn:hover{border-color:#C0170F;color:#C0170F;background:rgba(192,23,15,.05)}
.view-btn{display:inline-flex;align-items:center;padding:5px 12px;border-radius:8px;background:rgba(192,23,15,.08);border:1px solid rgba(192,23,15,.2);color:#C0170F;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s;font-family:'DM Sans',sans-serif}
.view-btn:hover{background:rgba(192,23,15,.14);border-color:#C0170F}
.booking-count{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890}

/* ── Empty state ── */
.empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:72px 24px;text-align:center;background:#fff;border:1px solid #E8E2DA;border-radius:18px}
.empty-icon{font-size:52px;margin-bottom:12px;opacity:.4}
.empty-title{font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:#1A1410;margin-bottom:8px}
.empty-sub{font-size:13px;color:#9E9890;margin-bottom:20px;max-width:320px}
.empty-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:center}

/* ── Pagination ── */
.pagination-wrap{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:20px;flex-wrap:wrap}
.pg-info{font-family:'DM Mono',monospace;font-size:11px;color:#6B6560}
.pg-nav{display:flex;gap:3px;flex-wrap:wrap}
.pg-link{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 8px;border-radius:8px;font-family:'DM Mono',monospace;font-size:11px;font-weight:600;color:#6B6560;background:#fff;border:1px solid #E8E2DA;cursor:pointer;transition:all .15s}
.pg-link:hover:not(.disabled):not(.pg-active){border-color:#C0170F;color:#C0170F}
.pg-active{background:#C0170F;color:#fff;border-color:#C0170F}
.pg-link.disabled{opacity:.4;cursor:not-allowed}

/* ── Buttons ── */
.btn-cta{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:12px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;text-decoration:none}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
</style>