<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- ── Page Header ── -->
            <div class="page-header">
                <div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Vendor Management</div>
                    <h1 class="page-title">Vendors</h1>
                    <p class="page-sub">Manage and organise all your vendors</p>
                </div>
                <Link :href="route('vendors.create')" class="btn-cta">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    Add Vendor
                </Link>
            </div>

            <!-- ── Stat Cards ── -->
            <div class="stats-grid">
                <div class="stat-card" style="--accent:#1D5C96">
                    <div class="stat-icon" style="background:rgba(29,92,150,.12)">
                        <svg width="18" height="18" fill="none" stroke="#1D5C96" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Total Vendors</div>
                        <div class="stat-value">{{ vendors.total }}</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#16a34a">
                    <div class="stat-icon" style="background:rgba(22,163,74,.1)">
                        <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Verified</div>
                        <div class="stat-value">{{ verifiedCount }}</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#b45309">
                    <div class="stat-icon" style="background:rgba(249,178,51,.12)">
                        <svg width="18" height="18" fill="none" stroke="#b45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Pending</div>
                        <div class="stat-value">{{ pendingCount }}</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#C0170F">
                    <div class="stat-icon" style="background:rgba(192,23,15,.08)">
                        <svg width="18" height="18" fill="#C0170F" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Featured</div>
                        <div class="stat-value">{{ featuredCount }}</div>
                    </div>
                </div>
            </div>

            <!-- ── Filter Card ── -->
            <div class="ep-card filter-card">
                <div class="filter-row">
                    <div class="search-wrap">
                        <svg class="search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input v-model="form.search" @input="doSearch" type="text" placeholder="Search vendors…" class="search-input">
                    </div>
                    <select v-model="form.category_id" @change="doSearch" class="ep-select-sm">
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <select v-model="form.verification_status" @change="doSearch" class="ep-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <select v-model="form.min_rating" @change="doSearch" class="ep-select-sm">
                        <option value="">Any Rating</option>
                        <option value="4">4★ &amp; above</option>
                        <option value="3">3★ &amp; above</option>
                        <option value="2">2★ &amp; above</option>
                    </select>
                    <select v-model="form.is_featured" @change="doSearch" class="ep-select-sm">
                        <option value="">All Vendors</option>
                        <option value="1">Featured Only</option>
                    </select>
                    <button v-if="hasActiveFilters" @click="resetFilters" class="btn-clear">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        Clear
                    </button>
                </div>
                <div v-if="vendors.total > 0" class="filter-meta">
                    <span class="meta-txt">Showing <strong>{{ vendors.from }}</strong>–<strong>{{ vendors.to }}</strong> of <strong>{{ vendors.total }}</strong> vendors</span>
                    <div class="per-page-wrap">
                        <span class="meta-txt">Rows:</span>
                        <select v-model="form.per_page" @change="doSearch" class="per-page-sel">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ── Table ── -->
            <div class="ep-card table-card">
                <div class="table-scroll">
                    <table class="ep-table">
                        <thead>
                            <tr>
                                <th v-for="col in columns" :key="col.key" :class="col.thClass ?? ''">
                                    <button v-if="col.sortable" @click="sortBy(col.key)" class="sort-btn">
                                        {{ col.label }}
                                        <span class="sort-arrows">
                                            <svg width="7" height="7" :class="form.sort===col.key&&form.direction==='asc'?'sort-active':'sort-dim'" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l5 7H5l5-7z"/></svg>
                                            <svg width="7" height="7" :class="form.sort===col.key&&form.direction==='desc'?'sort-active':'sort-dim'" fill="currentColor" viewBox="0 0 20 20"><path d="M10 17l-5-7h10l-5 7z"/></svg>
                                        </span>
                                    </button>
                                    <span v-else>{{ col.label }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="vendors.data.length === 0">
                                <td :colspan="columns.length" class="empty-cell">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        </div>
                                        <p class="empty-title">No vendors found</p>
                                        <p class="empty-sub">{{ hasActiveFilters ? 'Try adjusting your filters.' : 'Add your first vendor to get started.' }}</p>
                                        <Link v-if="!hasActiveFilters" :href="route('vendors.create')" class="btn-cta" style="margin-top:8px">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                                            Add Vendor
                                        </Link>
                                        <button v-else @click="resetFilters" class="btn-link">Clear filters</button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="vendor in vendors.data" :key="vendor.id" class="ep-row">
                                <!-- Vendor name + logo -->
                                <td class="td-vendor">
                                    <div class="vendor-cell">
                                        <div class="vendor-logo">
                                            <img v-if="vendor.logo" :src="vendor.logo" :alt="vendor.business_name" class="logo-img">
                                            <div v-else class="logo-placeholder">
                                                <svg width="16" height="16" fill="none" stroke="#C8C0B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                        </div>
                                        <div class="vendor-info">
                                            <Link :href="route('vendors.show', vendor.id)" class="vendor-name">{{ vendor.business_name }}</Link>
                                            <span class="vendor-email">{{ vendor.email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Category -->
                                <td class="td-pad"><span class="cat-chip">{{ vendor.category?.name ?? '—' }}</span></td>
                                <!-- Location -->
                                <td class="td-pad td-muted">{{ vendor.city }}<span v-if="vendor.state">, {{ vendor.state }}</span></td>
                                <!-- Rating -->
                                <td class="td-pad">
                                    <div v-if="vendor.rating" class="rating-row">
                                        <svg width="13" height="13" fill="#F9B233" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        <span class="rating-num">{{ vendor.rating }}</span>
                                        <span class="rating-ct">({{ vendor.total_reviews }})</span>
                                    </div>
                                    <span v-else class="td-empty">—</span>
                                </td>
                                <!-- Min Order -->
                                <td class="td-pad">
                                    <span v-if="vendor.minimum_order_value" class="min-order">TZS {{ formatPrice(vendor.minimum_order_value) }}</span>
                                    <span v-else class="td-empty">—</span>
                                </td>
                                <!-- Verification -->
                                <td class="td-pad">
                                    <span class="status-chip" :style="VERIF_STYLE[vendor.verification_status] || VERIF_STYLE.default">
                                        <span class="status-dot" :style="{background: VERIF_DOT[vendor.verification_status] || '#9E9890'}"></span>
                                        {{ formatVerification(vendor.verification_status) }}
                                    </span>
                                </td>
                                <!-- Active -->
                                <td class="td-pad">
                                    <span class="status-chip" :style="vendor.is_active ? ACTIVE_ON : ACTIVE_OFF">
                                        <span class="status-dot" :style="{background: vendor.is_active ? '#16a34a' : '#9E9890'}"></span>
                                        {{ vendor.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <!-- Featured -->
                                <td class="td-center">
                                    <svg v-if="vendor.is_featured" width="14" height="14" fill="#F9B233" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <span v-else class="td-empty">—</span>
                                </td>
                                <!-- Bookings -->
                                <td class="td-center td-mono">{{ vendor.total_bookings ?? 0 }}</td>
                                <!-- Actions -->
                                <td class="td-actions">
                                    <div class="actions-wrap">
                                        <Link :href="route('vendors.show', vendor.id)" class="action-btn" title="View">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </Link>
                                        <Link :href="route('vendors.edit', vendor.id)" class="action-btn" title="Edit">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </Link>
                                        <button @click="confirmDelete(vendor)" class="action-btn" title="Delete">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div v-if="vendors.last_page > 1" class="pagination-bar">
                    <span class="meta-txt">Page {{ vendors.current_page }} of {{ vendors.last_page }}</span>
                    <div class="page-btns">
                        <component v-for="link in vendors.links" :key="link.label"
                            :is="link.url ? 'a' : 'span'"
                            v-html="link.label"
                            :href="link.url ?? undefined"
                            @click.prevent="link.url && router.get(link.url, form, { preserveState: true, preserveScroll: true })"
                            class="page-btn"
                            :class="link.active ? 'page-active' : link.url ? 'page-inactive' : 'page-disabled'"
                        />
                    </div>
                </div>
            </div>

            <!-- ── Delete Modal ── -->
            <Teleport to="body">
                <div v-if="deleteTarget" class="modal-backdrop" @click.self="deleteTarget = null">
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
                        <p class="modal-body">Are you sure you want to delete <strong style="color:#1A1410">{{ deleteTarget?.business_name }}</strong>? All associated data will be permanently removed.</p>
                        <div class="modal-actions">
                            <button @click="deleteTarget = null" class="btn-ghost">Cancel</button>
                            <button @click="executeDelete" class="btn-danger">Delete Vendor</button>
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
import { reactive, computed, ref } from 'vue'

const props = defineProps({ vendors: Object, categories: Array, filters: Object })

const form = reactive({
    search:              props.filters.search              || '',
    category_id:         props.filters.category_id         || '',
    city:                props.filters.city                || '',
    verification_status: props.filters.verification_status || '',
    is_featured:         props.filters.is_featured         ?? '',
    min_rating:          props.filters.min_rating          || '',
    per_page:            props.filters.per_page            || 25,
    sort:                props.filters.sort                || '',
    direction:           props.filters.direction           || 'asc',
})

const hasActiveFilters = computed(() =>
    form.search || form.category_id || form.city || form.verification_status || form.is_featured !== '' || form.min_rating
)
const doSearch = () => router.get(route('vendors.index'), form, { preserveState: true, preserveScroll: true })
const resetFilters = () => {
    Object.assign(form, { search:'', category_id:'', city:'', verification_status:'', is_featured:'', min_rating:'' })
    doSearch()
}
const sortBy = key => {
    form.direction = form.sort === key && form.direction === 'asc' ? 'desc' : 'asc'
    form.sort = key
    doSearch()
}

const verifiedCount = computed(() => props.vendors.data.filter(v => v.verification_status === 'verified').length)
const pendingCount  = computed(() => props.vendors.data.filter(v => v.verification_status === 'pending').length)
const featuredCount = computed(() => props.vendors.data.filter(v => v.is_featured).length)

const deleteTarget = ref(null)
const confirmDelete = v => { deleteTarget.value = v }
const executeDelete = () => {
    router.delete(route('vendors.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => { deleteTarget.value = null },
    })
}

const VERIF_STYLE = {
    pending:  { background:'rgba(249,178,51,.12)', color:'#b45309', border:'1px solid rgba(249,178,51,.4)'   },
    verified: { background:'rgba(22,163,74,.1)',   color:'#16a34a', border:'1px solid rgba(22,163,74,.25)'   },
    rejected: { background:'rgba(192,23,15,.08)',  color:'#C0170F', border:'1px solid rgba(192,23,15,.2)'    },
    default:  { background:'rgba(158,152,144,.12)',color:'#6B6560', border:'1px solid rgba(158,152,144,.3)'  },
}
const VERIF_DOT  = { pending:'#b45309', verified:'#16a34a', rejected:'#C0170F' }
const ACTIVE_ON  = { background:'rgba(22,163,74,.1)',   color:'#16a34a', border:'1px solid rgba(22,163,74,.25)'  }
const ACTIVE_OFF = { background:'rgba(158,152,144,.12)',color:'#6B6560', border:'1px solid rgba(158,152,144,.3)' }

const columns = [
    { key:'business_name', label:'Vendor',       sortable:true  },
    { key:'category',      label:'Category',     sortable:false },
    { key:'city',          label:'Location',     sortable:true  },
    { key:'rating',        label:'Rating',       sortable:true  },
    { key:'min_order',     label:'Min. Order',   sortable:true  },
    { key:'verification',  label:'Verification', sortable:false },
    { key:'status',        label:'Status',       sortable:false },
    { key:'featured',      label:'★',            sortable:false, thClass:'th-center' },
    { key:'bookings',      label:'Bookings',     sortable:true,  thClass:'th-center' },
    { key:'actions',       label:'',             sortable:false, thClass:'th-right'  },
]

const formatVerification = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : ''
const formatPrice = n => n ? Number(n).toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:0}) : '0'
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(22px,3vw,28px);font-weight:900;color:#1A1410;line-height:1.15;margin-bottom:4px}
.page-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
@media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr}}
.stat-card{background:#fff;border:1px solid #E8E2DA;border-radius:16px;padding:16px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 6px rgba(0,0,0,.04);position:relative;overflow:hidden}
.stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;background:var(--accent);opacity:.7;border-radius:0 0 16px 16px}
.stat-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-label{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.14em;color:#9E9890;font-weight:700;margin-bottom:2px}
.stat-value{font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:#1A1410;line-height:1}
.ep-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04)}
.filter-card{padding:14px 16px;margin-bottom:14px}
.filter-row{display:flex;align-items:center;flex-wrap:wrap;gap:8px}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-icon{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9E9890;pointer-events:none}
.search-input{width:100%;padding:8px 12px 8px 32px;border:1.5px solid #E8E2DA;border-radius:10px;font-size:12px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
.search-input:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.search-input::placeholder{color:#C8C0B8}
.ep-select-sm{padding:8px 11px;border:1.5px solid #E8E2DA;border-radius:10px;font-size:12px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;cursor:pointer;transition:border-color .15s}
.ep-select-sm:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.filter-meta{display:flex;align-items:center;justify-content:space-between;margin-top:10px;padding-top:10px;border-top:1px solid #F0EDE8}
.meta-txt{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890}
.meta-txt strong{color:#1A1410}
.per-page-wrap{display:flex;align-items:center;gap:6px}
.per-page-sel{padding:3px 8px;border:1.5px solid #E8E2DA;border-radius:7px;font-family:'DM Mono',monospace;font-size:10px;background:#fff;color:#1A1410;outline:none}
.table-card{margin-bottom:0}
.table-scroll{overflow-x:auto}
.ep-table{width:100%;border-collapse:collapse;font-size:12px}
.ep-table thead tr{background:#F0EDE8;border-bottom:1px solid #E8E2DA}
.ep-table th{padding:10px 14px;font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.12em;color:#9E9890;font-weight:700;white-space:nowrap;text-align:left}
.th-center{text-align:center}.th-right{text-align:right}
.sort-btn{display:inline-flex;align-items:center;gap:4px;background:none;border:none;cursor:pointer;font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.12em;color:#9E9890;font-weight:700;padding:0;transition:color .15s}
.sort-btn:hover{color:#1A1410}
.sort-arrows{display:flex;flex-direction:column;gap:1px}
.sort-active{color:#C0170F}.sort-dim{color:#C8C0B8}
.ep-row{border-bottom:1px solid #F7F5F2;transition:background .15s}
.ep-row:hover{background:#FDFCFB}
.ep-row:last-child{border-bottom:none}
.td-pad{padding:11px 14px;white-space:nowrap;vertical-align:middle}
.td-muted{font-size:12px;color:#6B6560}
.td-mono{font-family:'DM Mono',monospace;font-size:11px;color:#1A1410;font-weight:700}
.td-center{padding:11px 14px;text-align:center;vertical-align:middle}
.td-actions{padding:11px 14px;text-align:right;vertical-align:middle}
.td-empty{font-family:'DM Mono',monospace;font-size:11px;color:#C8C0B8}
.td-vendor{padding:10px 14px;vertical-align:middle}
.vendor-cell{display:flex;align-items:center;gap:10px}
.vendor-logo{width:34px;height:34px;border-radius:9px;overflow:hidden;border:1px solid #E8E2DA;flex-shrink:0;background:#F7F5F2}
.logo-img{width:100%;height:100%;object-fit:cover}
.logo-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.vendor-info{min-width:0}
.vendor-name{display:block;font-size:12px;font-weight:700;color:#1A1410;text-decoration:none;white-space:nowrap;max-width:180px;overflow:hidden;text-overflow:ellipsis;transition:color .15s}
.vendor-name:hover{color:#C0170F}
.vendor-email{display:block;font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;white-space:nowrap;max-width:180px;overflow:hidden;text-overflow:ellipsis}
.cat-chip{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;background:rgba(29,92,150,.1);color:#1D5C96;border:1px solid rgba(29,92,150,.25);font-family:'DM Mono',monospace;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;white-space:nowrap}
.status-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-family:'DM Mono',monospace;font-size:9px;font-weight:700;white-space:nowrap}
.status-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0}
.rating-row{display:flex;align-items:center;gap:4px}
.rating-num{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#1A1410}
.rating-ct{font-family:'DM Mono',monospace;font-size:9px;color:#9E9890}
.min-order{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#C0170F}
.actions-wrap{display:flex;align-items:center;justify-content:flex-end;gap:3px}
.action-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;cursor:pointer;text-decoration:none;transition:all .15s}
.action-btn:hover{border-color:#C0170F;color:#C0170F;background:rgba(192,23,15,.04)}
.empty-cell{padding:60px 20px;text-align:center}
.empty-state{display:flex;flex-direction:column;align-items:center;gap:8px}
.empty-icon{width:52px;height:52px;border-radius:14px;background:#F0EDE8;display:flex;align-items:center;justify-content:center;color:#C8C0B8}
.empty-title{font-family:'Playfair Display',serif;font-size:15px;font-weight:900;color:#1A1410}
.empty-sub{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890}
.btn-link{background:none;border:none;cursor:pointer;font-family:'DM Mono',monospace;font-size:10px;color:#C0170F;text-decoration:underline;padding:0}
.pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid #F0EDE8;flex-wrap:wrap;gap:8px}
.page-btns{display:flex;align-items:center;gap:4px;flex-wrap:wrap}
.page-btn{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:28px;padding:0 8px;border-radius:8px;border:1.5px solid #E8E2DA;font-family:'DM Mono',monospace;font-size:10px;font-weight:700;cursor:pointer;transition:all .15s;text-decoration:none;background:#fff}
.page-active{background:#C0170F;border-color:#C0170F;color:#fff}
.page-inactive{color:#6B6560}.page-inactive:hover{border-color:#C0170F;color:#C0170F}
.page-disabled{color:#C8C0B8;cursor:not-allowed;pointer-events:none}
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:12px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-ghost{padding:9px 18px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-danger{padding:9px 18px;border-radius:11px;border:none;background:#C0170F;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;transition:background .15s}
.btn-danger:hover{background:#a01209}
.btn-clear{display:inline-flex;align-items:center;gap:5px;padding:8px 12px;border-radius:10px;border:1.5px solid rgba(192,23,15,.3);background:rgba(192,23,15,.06);color:#C0170F;font-size:11px;font-weight:700;font-family:'DM Mono',monospace;cursor:pointer;transition:all .15s;white-space:nowrap}
.btn-clear:hover{background:rgba(192,23,15,.1)}
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;display:flex;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px)}
.modal-box{background:#fff;border-radius:20px;padding:24px;max-width:420px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.modal-head{display:flex;align-items:center;gap:14px;margin-bottom:14px}
.modal-icon-wrap{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.modal-title{font-family:'Playfair Display',serif;font-size:16px;font-weight:900;color:#1A1410}
.modal-sub{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:2px}
.modal-body{font-size:13px;color:#6B6560;line-height:1.6;margin-bottom:20px}
.modal-actions{display:flex;justify-content:flex-end;gap:10px}
</style>