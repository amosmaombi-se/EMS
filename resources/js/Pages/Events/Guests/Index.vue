<template>
    <AuthenticatedLayout>

        <!-- Confetti -->
        <div class="confetti" aria-hidden="true">
            <div class="cdot" v-for="n in 14" :key="n" :style="{
                width:(3+(n*3)%9)+'px', height:(3+(n*3)%9)+'px',
                left:(n*7.1%100)+'%',
                background:['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233'][n%6],
                animationDuration:(10+n*1.1)+'s', animationDelay:(n*.6)+'s',
                borderRadius:n%3===0?'2px':'50%',
            }"></div>
        </div>

        <div class="page-wrap">

            <!-- ── Page header ── -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('events.index')" class="bc-link">Events</Link>
                        <span class="bc-sep">›</span>
                        <Link :href="route('events.show', event.id)" class="bc-link">{{ event.title }}</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">Guests</span>
                    </div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Guest Management</div>
                    <h1 class="page-title">{{ event.title }}</h1>
                </div>
                <div class="header-actions">
                    <button @click="exportGuests" :disabled="exportLoading" class="btn-secondary">
                        <svg v-if="exportLoading" class="spin-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        <svg v-else width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ exportLoading ? 'Generating…' : 'Export PDF' }}
                    </button>
                    <Link :href="route('events.guests.create', event.id)" class="btn-cta">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        Add Guest
                    </Link>
                </div>
            </div>

            <!-- ── Stat cards ── -->
            <div class="stats-grid">
                <div class="stat-card" style="--accent:#1D5C96">
                    <div class="stat-icon" style="background:rgba(29,92,150,.12)">👥</div>
                    <div class="stat-body">
                        <div class="stat-label">Total Guests</div>
                        <div class="stat-value">{{ stats.total }}</div>
                        <div class="stat-sub">Including +1s: {{ stats.total_guests_count }}</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#16a34a">
                    <div class="stat-icon" style="background:rgba(22,163,74,.12)">✅</div>
                    <div class="stat-body">
                        <div class="stat-label">Attending</div>
                        <div class="stat-value">{{ stats.attending }}</div>
                        <div class="stat-bar-wrap">
                            <div class="stat-bar" style="background:#16a34a" :style="{width: stats.total>0?((stats.attending/stats.total)*100)+'%':'0%'}"></div>
                        </div>
                        <div class="stat-sub">{{ stats.total>0?((stats.attending/stats.total)*100).toFixed(1):0 }}% of total</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#F9B233">
                    <div class="stat-icon" style="background:rgba(249,178,51,.15)">⭐</div>
                    <div class="stat-body">
                        <div class="stat-label">VIP Guests</div>
                        <div class="stat-value">{{ stats.vip }}</div>
                        <div class="stat-sub">{{ stats.total>0?((stats.vip/stats.total)*100).toFixed(1):0 }}% of total</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#C0170F">
                    <div class="stat-icon" style="background:rgba(192,23,15,.08)">✉️</div>
                    <div class="stat-body">
                        <div class="stat-label">Invitations Sent</div>
                        <div class="stat-value">{{ stats.invitations_sent }}</div>
                        <div class="stat-bar-wrap">
                            <div class="stat-bar" style="background:#C0170F" :style="{width:stats.total>0?((stats.invitations_sent/stats.total)*100)+'%':'0%'}"></div>
                        </div>
                        <div class="stat-sub">{{ stats.total>0?((stats.invitations_sent/stats.total)*100).toFixed(1):0 }}% sent</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#F05A00">
                    <div class="stat-icon" style="background:rgba(240,90,0,.1)">🎫</div>
                    <div class="stat-body">
                        <div class="stat-label">Checked In</div>
                        <div class="stat-value">{{ checkedInCount }}</div>
                        <div class="stat-sub">{{ stats.attending>0?((checkedInCount/stats.attending)*100).toFixed(1):0 }}% check-in rate</div>
                    </div>
                </div>
            </div>

            <!-- ── Filter & search card ── -->
            <div class="filter-card">
                <div class="filter-top">
                    <div>
                        <div class="fc-title">Guest List</div>
                        <div class="fc-sub">Manage and filter your event guests</div>
                    </div>
                    <div class="filter-selects">
                        <select v-model="filters.category" class="ep-select">
                            <option value="">All Categories</option>
                            <option value="vip">VIP</option>
                            <option value="family">Family</option>
                            <option value="friends">Friends</option>
                            <option value="colleagues">Colleagues</option>
                            <option value="business">Business</option>
                            <option value="media">Media</option>
                            <option value="sponsors">Sponsors</option>
                            <option value="other">Other</option>
                        </select>
                        <select v-model="filters.rsvp_status" class="ep-select">
                            <option value="">All RSVP</option>
                            <option value="pending">Pending</option>
                            <option value="attending">Attending</option>
                            <option value="not_attending">Not Attending</option>
                            <option value="maybe">Maybe</option>
                        </select>
                        <button @click="showFilters=!showFilters" :class="['btn-filter', showFilters?'active':'']">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 4h18M7 10h10M11 16h2"/></svg>
                            More Filters
                        </button>
                    </div>
                </div>

                <!-- Search + bulk -->
                <div class="search-row">
                    <div class="search-wrap">
                        <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input v-model="filters.search" type="search" placeholder="Search by name, email or phone…" class="search-input">
                    </div>
                    <div class="bulk-row">
                        <div class="show-row">
                            <span class="show-lbl">Show:</span>
                            <select v-model="perPage" class="ep-select-sm">
                                <option :value="5">5</option>
                                <option :value="10">10</option>
                                <option :value="15">15</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                            </select>
                        </div>
                        <button @click="selectAll" class="btn-ghost-sm">
                            {{ selectedGuests.length===guests.data.length && guests.data.length>0 ? 'Deselect All' : 'Select All' }}
                        </button>
                        <div class="relative" v-if="selectedGuests.length>0">
                            <button @click="showBulkActions=!showBulkActions" class="btn-bulk">
                                Bulk Actions <span class="bulk-badge">{{ selectedGuests.length }}</span>
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div v-if="showBulkActions" class="bulk-dropdown">
                                <Link :href="route('events.guests.invitation-composer',{eventId:event.id,preselected:selectedGuests})" @click="showBulkActions=false" class="bulk-item">
                                    Send Invitations
                                    <span class="bulk-badge-sm">{{ selectedGuests.length }}</span>
                                </Link>
                                <button @click="updateBulkStatus('attending')" class="bulk-item">Mark as Attending</button>
                                <button @click="updateBulkStatus('not_attending')" class="bulk-item">Mark as Not Attending</button>
                                <button @click="exportSelected" class="bulk-item">Export Selected</button>
                                <button @click="deleteSelected" class="bulk-item danger">Delete Selected</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced filters -->
                <div v-if="showFilters" class="adv-filters">
                    <div class="field">
                        <label class="field-label">Guest Type</label>
                        <select v-model="filters.guest_type" class="ep-select">
                            <option value="">All Types</option>
                            <option value="primary">Primary</option>
                            <option value="plus_one">Plus One</option>
                            <option value="child">Child</option>
                            <option value="vendor">Vendor</option>
                            <option value="staff">Staff</option>
                            <option value="speaker">Speaker</option>
                            <option value="performer">Performer</option>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field-label">Special Needs</label>
                        <select v-model="filters.has_special_needs" class="ep-select">
                            <option value="">All Guests</option>
                            <option value="yes">Has Special Needs</option>
                            <option value="no">No Special Needs</option>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field-label">Invitation Status</label>
                        <select v-model="filters.invitation_status" class="ep-select">
                            <option value="">All Status</option>
                            <option value="not_sent">Not Sent</option>
                            <option value="sent_pending">Sent – Pending</option>
                            <option value="responded">Responded</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ── Guest table ── -->
            <div class="table-card">
                <div class="table-scroll">
                    <table class="ep-table">
                        <thead>
                            <tr>
                                <th class="th-check">
                                    <input type="checkbox"
                                        :checked="selectedGuests.length===guests.data.length&&guests.data.length>0"
                                        @change="toggleSelectAll"
                                        class="ep-checkbox">
                                </th>
                                <th>Guest</th>
                                <th>Category</th>
                                <th>RSVP</th>
                                <th>Invitation</th>
                                <th>Check-in</th>
                                <th class="th-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="guest in guests.data" :key="guest.id"
                                :class="['tr-row', guest.is_vip?'tr-vip':'']">
                                <td class="td-check">
                                    <input type="checkbox" :value="guest.id" v-model="selectedGuests" class="ep-checkbox">
                                </td>
                                <td class="td-guest">
                                    <div class="guest-avatar" :style="{background:AVATAR_COLORS[guest.category]||'#9E9890'}">
                                        {{ getInitials(guest.first_name, guest.last_name) }}
                                    </div>
                                    <div class="guest-info">
                                        <div class="guest-name">
                                            {{ guest.first_name }} {{ guest.last_name }}
                                            <span v-if="guest.is_vip" class="vip-badge">VIP</span>
                                            <span v-if="guest.plus_ones>0" class="plus-badge">+{{ guest.plus_ones }}</span>
                                        </div>
                                        <a :href="`mailto:${guest.email}`" class="guest-email">{{ guest.email||'No email' }}</a>
                                        <div v-if="guest.phone" class="guest-phone">{{ guest.phone }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="cat-badge" :style="CAT_STYLE[guest.category]||CAT_STYLE.other">{{ guest.category }}</span>
                                    <div v-if="guest.guest_type!=='primary'" class="type-sub">{{ guest.guest_type }}</div>
                                </td>
                                <td>
                                    <span class="rsvp-badge" :style="RSVP_STYLE[guest.rsvp_status]||RSVP_STYLE.pending">{{ RSVP_LABEL[guest.rsvp_status]||guest.rsvp_status }}</span>
                                    <div v-if="guest.rsvp_responded_at" class="date-sub">{{ fmtDate(guest.rsvp_responded_at) }}</div>
                                </td>
                                <td>
                                    <div class="inv-status" :style="{color:guest.invitation_sent?'#16a34a':'#9E9890'}">
                                        {{ guest.invitation_sent?'✓ Sent':'Not Sent' }}
                                    </div>
                                    <div v-if="guest.invitation_sent" class="date-sub">{{ guest.invitation_method }}</div>
                                    <div v-if="guest.invitation_sent_at" class="date-sub">{{ fmtDate(guest.invitation_sent_at) }}</div>
                                </td>
                                <td>
                                    <div v-if="guest.check_in_time" class="checkin-info">
                                        <span class="checkin-yes">✓ Checked In</span>
                                        <div class="date-sub">{{ fmtTime(guest.check_in_time) }}</div>
                                        <div v-if="guest.check_out_time" class="date-sub">Out: {{ fmtTime(guest.check_out_time) }}</div>
                                    </div>
                                    <span v-else class="checkin-no">Not checked in</span>
                                </td>
                                <td class="td-actions">
                                    <div class="action-btns">
                                        <Link :href="route('events.guests.edit',[event.id,guest.id])" class="action-btn edit" title="Edit">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </Link>
                                        <button @click="sendEmailInvitation(guest)" :disabled="guest.invitation_sent"
                                            :class="['action-btn', guest.invitation_sent?'disabled':'invite']" title="Send Invitation">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </button>
                                        <button @click="showGuestDetails(guest)" class="action-btn view" title="View Details">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button @click="deleteGuest(guest)" class="action-btn del" title="Delete">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="guests.data&&guests.data.length>0" class="pagination">
                    <div class="pag-info">
                        Showing <strong>{{ guests.from||0 }}</strong> to <strong>{{ guests.to||0 }}</strong> of <strong>{{ guests.total||0 }}</strong> guests
                    </div>
                    <nav class="pag-nav">
                        <template v-for="(link,idx) in guests.links" :key="idx">
                            <button v-if="idx===0" @click="goToPage(link.url)" :disabled="!link.url"
                                :class="['pag-btn pag-prev', !link.url?'disabled':'']">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                                Prev
                            </button>
                            <button v-else-if="idx!==guests.links.length-1" @click="goToPage(link.url)"
                                :disabled="link.active||!link.url"
                                :class="['pag-btn', link.active?'active':'', !link.url?'disabled':'']"
                                v-html="link.label"/>
                            <button v-else @click="goToPage(link.url)" :disabled="!link.url"
                                :class="['pag-btn pag-next', !link.url?'disabled':'']">
                                Next
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </template>
                    </nav>
                </div>

                <!-- Empty state -->
                <div v-if="!guests.data||guests.data.length===0" class="empty-state">
                    <div class="empty-icon">👥</div>
                    <div class="empty-title">No guests yet</div>
                    <p class="empty-sub">Get started by inviting your first guest.</p>
                    <Link :href="route('events.guests.create', event.id)" class="btn-cta">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        Add First Guest
                    </Link>
                </div>
            </div>

        </div>

        <!-- ── Guest Details Modal ── -->
        <Modal :show="showDetailsModal" @close="showDetailsModal=false" max-width="2xl">
            <div class="modal-wrap" v-if="selectedGuest">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">
                            {{ selectedGuest.first_name }} {{ selectedGuest.last_name }}
                            <span v-if="selectedGuest.is_vip" class="vip-badge ml-2">VIP</span>
                        </h2>
                        <p class="modal-sub">{{ selectedGuest.email }}</p>
                    </div>
                    <button @click="showDetailsModal=false" class="modal-close">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-col">
                        <div class="modal-section">
                            <div class="ms-title">Basic Information</div>
                            <div class="ms-row"><span class="ms-lbl">Category</span><span class="ms-val capitalize">{{ selectedGuest.category }}</span></div>
                            <div class="ms-row"><span class="ms-lbl">Guest Type</span><span class="ms-val capitalize">{{ selectedGuest.guest_type }}</span></div>
                            <div class="ms-row"><span class="ms-lbl">Phone</span><span class="ms-val">{{ selectedGuest.phone||'N/A' }}</span></div>
                            <div class="ms-row"><span class="ms-lbl">Language</span><span class="ms-val">{{ selectedGuest.language_preference||'English' }}</span></div>
                        </div>
                        <div class="modal-section">
                            <div class="ms-title">RSVP</div>
                            <div class="ms-row"><span class="ms-lbl">Status</span>
                                <span class="rsvp-badge" :style="RSVP_STYLE[selectedGuest.rsvp_status]||RSVP_STYLE.pending">{{ RSVP_LABEL[selectedGuest.rsvp_status]||selectedGuest.rsvp_status }}</span>
                            </div>
                            <div class="ms-row"><span class="ms-lbl">Plus Ones</span><span class="ms-val">{{ selectedGuest.plus_ones }} allowed</span></div>
                            <div v-if="selectedGuest.rsvp_responded_at" class="ms-row"><span class="ms-lbl">Responded</span><span class="ms-val">{{ fmtDateTime(selectedGuest.rsvp_responded_at) }}</span></div>
                        </div>
                        <div v-if="selectedGuest.dietary_preference||selectedGuest.allergies||selectedGuest.special_requirements" class="modal-section">
                            <div class="ms-title">Special Requirements</div>
                            <div v-if="selectedGuest.dietary_preference" class="ms-row"><span class="ms-lbl">Dietary</span><span class="ms-val">{{ selectedGuest.dietary_preference }}</span></div>
                            <div v-if="selectedGuest.allergies" class="ms-row"><span class="ms-lbl">Allergies</span><span class="ms-val">{{ selectedGuest.allergies }}</span></div>
                            <div v-if="selectedGuest.special_requirements" class="ms-row"><span class="ms-lbl">Requirements</span><span class="ms-val">{{ selectedGuest.special_requirements }}</span></div>
                        </div>
                    </div>
                    <div class="modal-col">
                        <div class="modal-section">
                            <div class="ms-title">Invitation</div>
                            <div class="ms-row"><span class="ms-lbl">Status</span>
                                <span :style="{fontWeight:600,color:selectedGuest.invitation_sent?'#16a34a':'#9E9890'}">{{ selectedGuest.invitation_sent?'✓ Sent':'Not Sent' }}</span>
                            </div>
                            <div v-if="selectedGuest.invitation_sent" class="ms-row"><span class="ms-lbl">Method</span><span class="ms-val capitalize">{{ selectedGuest.invitation_method }}</span></div>
                            <div v-if="selectedGuest.invitation_sent_at" class="ms-row"><span class="ms-lbl">Sent At</span><span class="ms-val">{{ fmtDateTime(selectedGuest.invitation_sent_at) }}</span></div>
                            <div v-if="selectedGuest.invitation_link" class="ms-row"><span class="ms-lbl">Link</span><a :href="selectedGuest.invitation_link" target="_blank" class="ms-link">View Invitation</a></div>
                        </div>
                        <div class="modal-section">
                            <div class="ms-title">Check-in</div>
                            <div v-if="selectedGuest.check_in_time" class="ms-row"><span class="ms-lbl">Check-in</span><span class="ms-val" style="color:#16a34a">{{ fmtDateTime(selectedGuest.check_in_time) }}</span></div>
                            <div v-if="selectedGuest.check_out_time" class="ms-row"><span class="ms-lbl">Check-out</span><span class="ms-val">{{ fmtDateTime(selectedGuest.check_out_time) }}</span></div>
                            <div v-else class="ms-val" style="color:#9E9890">Not checked in yet</div>
                        </div>
                        <div v-if="selectedGuest.notes" class="modal-section">
                            <div class="ms-title">Notes</div>
                            <p style="font-size:13px;color:#6B6560;line-height:1.7;white-space:pre-line">{{ selectedGuest.notes }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <Link :href="route('events.guests.edit',[event.id,selectedGuest.id])" class="btn-cta">Edit Guest</Link>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import { debounce } from 'lodash'
import Swal from 'sweetalert2'

const page = usePage()
const props = defineProps({ event:Object, guests:Object, stats:Object, categories:Object, filters:Object })

const selectedGuests   = ref([])
const showBulkActions  = ref(false)
const showFilters      = ref(false)
const showDetailsModal = ref(false)
const selectedGuest    = ref(null)
const exportLoading    = ref(false)
const loading          = ref(false)

const checkedInCount = computed(() => props.guests.data.filter(g => g.check_in_time).length)

const filters = ref({
    search: props.filters?.search||'', rsvp_status: props.filters?.rsvp_status||'',
    category: props.filters?.category||'', guest_type: props.filters?.guest_type||'',
    has_special_needs: props.filters?.has_special_needs||'',
    invitation_status: props.filters?.invitation_status||''
})
const perPage = ref(props.guests?.per_page||15)

watch(perPage, v => {
    loading.value = true
    router.get(route('events.guests.index', props.event.id), { ...filters.value, per_page:v },
        { preserveState:true, preserveScroll:true, replace:true, onFinish:()=>{ loading.value=false } })
})

watch(filters, debounce(f => {
    loading.value = true
    router.get(route('events.guests.index', props.event.id), f,
        { preserveState:true, preserveScroll:true, replace:true, onFinish:()=>{ loading.value=false } })
}, 300), { deep:true })

watch(() => props.filters, nf => {
    if (nf && JSON.stringify(nf)!==JSON.stringify(filters.value)) filters.value = { ...nf }
}, { deep:true, immediate:false })

const goToPage = url => {
    if (!url) return
    loading.value = true
    const p = new URL(url, window.location.origin).searchParams.get('page')
    router.get(route('events.guests.index', props.event.id), { ...filters.value, page:p },
        { preserveState:true, preserveScroll:true, replace:true, onFinish:()=>{ loading.value=false } })
}

const selectAll = () => {
    selectedGuests.value = selectedGuests.value.length===props.guests.data.length ? [] : props.guests.data.map(g=>g.id)
}
const toggleSelectAll = e => {
    selectedGuests.value = e.target.checked ? props.guests.data.map(g=>g.id) : []
}

const updateBulkStatus = status => {
    if (!selectedGuests.value.length) return
    Swal.fire({ title:'Update Status?', text:`Mark ${selectedGuests.value.length} guests as ${status.replace(/_/g,' ')}?`, icon:'question',
        showCancelButton:true, confirmButtonColor:'#C0170F', cancelButtonColor:'#9E9890',
        confirmButtonText:'Update', cancelButtonText:'Cancel' })
    .then(r => {
        if (r.isConfirmed) {
            router.post(route('events.guests.bulk-update',{eventId:props.event.id}),
                { guest_ids:selectedGuests.value, rsvp_status:status },
                { onSuccess:()=>{ selectedGuests.value=[]; showBulkActions.value=false;
                    Swal.fire({ title:'Updated!', icon:'success', timer:2000, showConfirmButton:false }) },
                  onError:()=>Swal.fire({ title:'Error', text:'Failed to update.', icon:'error' }) })
        }
    })
}

const exportSelected = async () => {
    if (!selectedGuests.value.length) {
        Swal.fire({ title:'No Guests Selected', text:'Please select guests to export.', icon:'info', confirmButtonColor:'#C0170F' })
        return
    }
    await exportToPdf(props.guests.data.filter(g => selectedGuests.value.includes(g.id)), true)
}

const deleteSelected = () => {
    Swal.fire({ title:'Delete Guests?', text:`Delete ${selectedGuests.value.length} selected guests? Cannot be undone.`, icon:'warning',
        showCancelButton:true, confirmButtonColor:'#C0170F', cancelButtonColor:'#9E9890',
        confirmButtonText:'Delete', cancelButtonText:'Cancel' })
    .then(r => {
        if (r.isConfirmed) {
            router.post(route('events.guests.bulk-destroy', props.event.id), { guest_ids:selectedGuests.value },
                { onSuccess:()=>{ selectedGuests.value=[]; showBulkActions.value=false;
                    Swal.fire({ title:'Deleted!', icon:'success', timer:2000, showConfirmButton:false }) },
                  onError:()=>Swal.fire({ title:'Error', text:'Failed to delete.', icon:'error' }) })
        }
    })
}

const exportGuests = async () => await exportToPdf(props.guests.data, false)

const exportToPdf = async (guestsData, isSelected=false) => {
    if (!guestsData.length) {
        Swal.fire({ title:'No Data', text:'Nothing to export.', icon:'info', confirmButtonColor:'#C0170F' })
        return
    }
    exportLoading.value = true
    try {
        const { jsPDF } = await import('jspdf')
        const { default: autoTable } = await import('jspdf-autotable')
        const doc = new jsPDF({ orientation:'landscape', unit:'mm', format:'a4' })
        doc.setFontSize(20); doc.setTextColor(192,23,15)
        doc.text(`${props.event.title} – Guest Report`, 14, 20)
        doc.setFontSize(10); doc.setTextColor(0,0,0)
        doc.text(`Generated: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}`, 14, 28)
        const rows = guestsData.map(g => [
            `${g.first_name} ${g.last_name}`, g.email||'N/A', g.phone||'N/A',
            g.category, g.guest_type, g.is_vip?'Yes':'No',
            g.rsvp_status, g.invitation_sent?'Yes':'No', g.check_in_time?'Yes':'No', g.plus_ones||0
        ])
        autoTable(doc, {
            startY:35,
            head:[['Name','Email','Phone','Category','Type','VIP','RSVP','Invited','Checked In','+1s']],
            body: rows,
            styles:{ fontSize:8, cellPadding:3 },
            headStyles:{ fillColor:[192,23,15], textColor:255, fontStyle:'bold' },
            alternateRowStyles:{ fillColor:[253,242,242] }
        })
        doc.save(`guests_${props.event.id}_${new Date().toISOString().slice(0,10)}.pdf`)
        Swal.fire({ title:'Exported!', text:`${guestsData.length} guests exported.`, icon:'success',
            timer:2000, showConfirmButton:false, toast:true, position:'top-end' })
    } catch(e) {
        Swal.fire({ title:'Error', text:'Failed to generate PDF.', icon:'error', confirmButtonColor:'#C0170F' })
    } finally { exportLoading.value = false }
}

const sendEmailInvitation = guest => {
    Swal.fire({ title:'Send Invitation?', text:`Send to ${guest.first_name} ${guest.last_name}?`, icon:'question',
        showCancelButton:true, confirmButtonColor:'#C0170F', cancelButtonColor:'#9E9890',
        confirmButtonText:'Send' })
    .then(r => {
        if (r.isConfirmed) {
            router.post(route('events.guests.send-invitations',[props.event.id]), { guest_ids:[guest.id] },
                { onSuccess:()=>Swal.fire({ title:'Sent!', icon:'success', timer:2000, showConfirmButton:false }),
                  onError:()=>Swal.fire({ title:'Error', text:'Failed to send.', icon:'error', confirmButtonColor:'#C0170F' }) })
        }
    })
}

const showGuestDetails = guest => { selectedGuest.value=guest; showDetailsModal.value=true }

const deleteGuest = guest => {
    Swal.fire({ title:'Delete Guest?', text:`Delete ${guest.first_name} ${guest.last_name}? Cannot be undone.`, icon:'warning',
        showCancelButton:true, confirmButtonColor:'#C0170F', cancelButtonColor:'#9E9890',
        confirmButtonText:'Delete' })
    .then(r => {
        if (r.isConfirmed) {
            router.delete(route('events.guests.destroy',[props.event.id,guest.id]),
                { onSuccess:()=>Swal.fire({ title:'Deleted!', icon:'success', timer:2000, showConfirmButton:false }),
                  onError:()=>Swal.fire({ title:'Error', text:'Failed to delete.', icon:'error', confirmButtonColor:'#C0170F' }) })
        }
    })
}

const getInitials = (f,l) => `${f?.[0]||''}${l?.[0]||''}`.toUpperCase()
const fmtDate = d => d ? new Date(d).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : 'N/A'
const fmtTime = d => d ? new Date(d).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}) : 'N/A'
const fmtDateTime = d => d ? fmtDate(d)+' at '+fmtTime(d) : 'N/A'

const AVATAR_COLORS = { vip:'#b45309', family:'#7c3aed', friends:'#1D5C96', colleagues:'#16a34a', business:'#1D5C96', media:'#db2777', sponsors:'#C0170F', other:'#9E9890' }
const CAT_STYLE = {
    vip:         { background:'rgba(249,178,51,.15)', color:'#b45309',  border:'1px solid rgba(249,178,51,.4)'  },
    family:      { background:'rgba(124,58,237,.1)',  color:'#7c3aed',  border:'1px solid rgba(124,58,237,.25)' },
    friends:     { background:'rgba(29,92,150,.1)',   color:'#1D5C96',  border:'1px solid rgba(29,92,150,.25)'  },
    colleagues:  { background:'rgba(22,163,74,.1)',   color:'#16a34a',  border:'1px solid rgba(22,163,74,.25)'  },
    business:    { background:'rgba(29,92,150,.1)',   color:'#1D5C96',  border:'1px solid rgba(29,92,150,.25)'  },
    media:       { background:'rgba(219,39,119,.1)',  color:'#db2777',  border:'1px solid rgba(219,39,119,.25)' },
    sponsors:    { background:'rgba(192,23,15,.08)',  color:'#C0170F',  border:'1px solid rgba(192,23,15,.2)'   },
    other:       { background:'rgba(158,152,144,.12)',color:'#6B6560',  border:'1px solid rgba(158,152,144,.3)' },
}
const RSVP_STYLE = {
    pending:      { background:'rgba(249,178,51,.12)', color:'#b45309', border:'1px solid rgba(249,178,51,.3)' },
    attending:    { background:'rgba(22,163,74,.1)',   color:'#16a34a', border:'1px solid rgba(22,163,74,.25)' },
    not_attending:{ background:'rgba(192,23,15,.08)',  color:'#C0170F', border:'1px solid rgba(192,23,15,.2)'  },
    maybe:        { background:'rgba(29,92,150,.1)',   color:'#1D5C96', border:'1px solid rgba(29,92,150,.2)'  },
}
const RSVP_LABEL = { pending:'Pending', attending:'Attending', not_attending:'Not Attending', maybe:'Maybe' }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

/* confetti */
.confetti{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.cdot{position:absolute;opacity:0;animation:rise linear infinite}
@keyframes rise{0%{transform:translateY(110vh) rotate(0deg);opacity:0}5%{opacity:.35}95%{opacity:.15}100%{transform:translateY(-80px) rotate(540deg);opacity:0}}

/* page */
.page-wrap{position:relative;z-index:1;background:#F7F5F2;min-height:100vh;padding:28px 24px 64px;font-family:'DM Sans',sans-serif;color:#1A1410}

/* header */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px}
.breadcrumb{display:flex;align-items:center;gap:6px;font-family:'DM Mono',monospace;font-size:11px;margin-bottom:6px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C2BA}.bc-cur{color:#6B6560;font-weight:500}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(20px,3vw,28px);font-weight:900;color:#1A1410;line-height:1.15}
.header-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}

/* stat cards */
.stats-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px}
@media(max-width:1200px){.stats-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
.stat-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;padding:16px;display:flex;align-items:flex-start;gap:12px;box-shadow:0 2px 12px rgba(0,0,0,.05);transition:transform .18s,box-shadow .18s}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 6px 24px rgba(0,0,0,.09)}
.stat-icon{width:38px;height:38px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.stat-body{flex:1;min-width:0}
.stat-label{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#9E9890;margin-bottom:4px}
.stat-value{font-family:'Playfair Display',serif;font-size:26px;font-weight:900;color:#1A1410;line-height:1}
.stat-sub{font-size:11px;color:#9E9890;margin-top:4px}
.stat-bar-wrap{height:3px;background:#F0EDE8;border-radius:2px;overflow:hidden;margin-top:6px}
.stat-bar{height:100%;border-radius:2px;transition:width .4s}

/* filter card */
.filter-card{background:#fff;border:1px solid #E8E2DA;border-radius:20px;padding:20px;margin-bottom:16px;box-shadow:0 2px 12px rgba(0,0,0,.05)}
.filter-top{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap}
.fc-title{font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#1A1410}
.fc-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px}
.filter-selects{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.ep-select{padding:8px 12px;border:1.5px solid #E8E2DA;border-radius:10px;font-size:12px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;cursor:pointer;transition:border-color .15s}
.ep-select:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.08)}
.ep-select-sm{padding:6px 10px;border:1.5px solid #E8E2DA;border-radius:9px;font-size:12px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;cursor:pointer}
.btn-filter{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1.5px solid #E8E2DA;border-radius:10px;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Mono',monospace;cursor:pointer;transition:all .15s}
.btn-filter:hover,.btn-filter.active{border-color:#C0170F;color:#C0170F;background:rgba(192,23,15,.05)}
.search-row{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:0}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9E9890;pointer-events:none}
.search-input{width:100%;padding:9px 12px 9px 36px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;outline:none;background:#fff;transition:border-color .15s}
.search-input:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.08)}
.bulk-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.show-row{display:flex;align-items:center;gap:6px}
.show-lbl{font-size:12px;color:#6B6560;font-family:'DM Mono',monospace;white-space:nowrap}
.btn-ghost-sm{padding:7px 13px;border:1.5px solid #E8E2DA;border-radius:9px;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .15s;white-space:nowrap}
.btn-ghost-sm:hover{border-color:#9E9890;color:#1A1410}
.btn-bulk{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:none;background:linear-gradient(135deg,#C0170F,#F05A00);color:#fff;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;box-shadow:0 3px 10px rgba(192,23,15,.25)}
.bulk-badge{background:rgba(255,255,255,.3);border-radius:20px;padding:1px 7px;font-size:11px;font-family:'DM Mono',monospace}
.bulk-dropdown{position:absolute;right:0;top:calc(100% + 6px);background:#fff;border:1px solid #E8E2DA;border-radius:14px;box-shadow:0 8px 28px rgba(0,0,0,.12);z-index:50;overflow:hidden;min-width:190px}
.bulk-item{display:flex;align-items:center;justify-content:space-between;width:100%;padding:10px 16px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:none;border:none;cursor:pointer;text-align:left;text-decoration:none;transition:background .12s}
.bulk-item:hover{background:#F7F5F2}
.bulk-item.danger{color:#C0170F}.bulk-item.danger:hover{background:rgba(192,23,15,.06)}
.bulk-badge-sm{background:rgba(192,23,15,.1);color:#C0170F;border-radius:20px;padding:1px 7px;font-size:11px;font-family:'DM Mono',monospace;font-weight:700}
.adv-filters{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:14px;padding-top:14px;border-top:1px solid #F0EDE8}
@media(max-width:640px){.adv-filters{grid-template-columns:1fr}}
.field{display:flex;flex-direction:column;gap:5px}
.field-label{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#6B6560}

/* table */
.table-card{background:#fff;border:1px solid #E8E2DA;border-radius:20px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.05)}
.table-scroll{overflow-x:auto}
.ep-table{width:100%;border-collapse:collapse}
.ep-table thead tr{background:#F0EDE8;border-bottom:2px solid #E8E2DA}
.ep-table th{padding:12px 16px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#6B6560;text-align:left;white-space:nowrap}
.th-check{width:44px}.th-actions{text-align:right}
.ep-table tbody tr{border-bottom:1px solid #F0EDE8;transition:background .12s}
.tr-row:hover{background:#FAFAF8}
.tr-vip{background:rgba(249,178,51,.05)!important}
.tr-vip:hover{background:rgba(249,178,51,.09)!important}
.td-check,.td-guest,.td-actions{padding:14px 16px}
.ep-table td{padding:12px 16px;vertical-align:middle}
.ep-checkbox{width:15px;height:15px;accent-color:#C0170F;cursor:pointer;border-radius:4px}
.guest-avatar{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-family:'DM Mono',monospace;font-size:13px;font-weight:700;flex-shrink:0}
.td-guest .guest-avatar{float:left;margin-right:12px}
.guest-info{overflow:hidden}
.guest-name{font-size:13px;font-weight:700;color:#1A1410;display:flex;align-items:center;flex-wrap:wrap;gap:5px}
.guest-email{font-size:12px;color:#9E9890;text-decoration:none;transition:color .15s;display:block}
.guest-email:hover{color:#C0170F}
.guest-phone{font-size:11px;color:#C8C2BA;font-family:'DM Mono',monospace}
.vip-badge{display:inline-flex;padding:1px 7px;background:rgba(249,178,51,.18);color:#b45309;border-radius:20px;font-size:10px;font-weight:700;font-family:'DM Mono',monospace;border:1px solid rgba(249,178,51,.4)}
.plus-badge{display:inline-flex;padding:1px 7px;background:rgba(29,92,150,.1);color:#1D5C96;border-radius:20px;font-size:10px;font-weight:700;font-family:'DM Mono',monospace}
.cat-badge,.rsvp-badge{display:inline-flex;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;font-family:'DM Mono',monospace;text-transform:capitalize;white-space:nowrap}
.type-sub,.date-sub{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px;text-transform:capitalize}
.inv-status{font-size:12px;font-weight:600}
.checkin-yes{font-size:12px;font-weight:600;color:#16a34a}
.checkin-no{font-size:12px;color:#9E9890}
.action-btns{display:flex;align-items:center;justify-content:flex-end;gap:4px}
.action-btn{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:none;cursor:pointer;transition:all .18s;background:transparent}
.action-btn.edit  {color:#1D5C96}.action-btn.edit:hover  {background:rgba(29,92,150,.1);color:#1D5C96}
.action-btn.invite{color:#16a34a}.action-btn.invite:hover{background:rgba(22,163,74,.1)}
.action-btn.view  {color:#6B6560}.action-btn.view:hover  {background:#F0EDE8;color:#1A1410}
.action-btn.del   {color:#C0170F}.action-btn.del:hover   {background:rgba(192,23,15,.08)}
.action-btn.disabled{color:#C8C2BA;cursor:not-allowed;opacity:.5}

/* pagination */
.pagination{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid #F0EDE8;flex-wrap:wrap;gap:12px}
.pag-info{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}
.pag-info strong{color:#1A1410}
.pag-nav{display:flex;align-items:center;gap:4px}
.pag-btn{display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid #E8E2DA;border-radius:9px;background:#fff;color:#6B6560;font-size:12px;font-family:'DM Mono',monospace;cursor:pointer;transition:all .15s}
.pag-btn:hover:not(.disabled):not(.active){border-color:#9E9890;color:#1A1410}
.pag-btn.active{background:#C0170F;border-color:#C0170F;color:#fff;font-weight:700}
.pag-btn.disabled{opacity:.4;cursor:not-allowed}

/* empty */
.empty-state{text-align:center;padding:64px 24px}
.empty-icon{font-size:52px;margin-bottom:14px;opacity:.4}
.empty-title{font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:#1A1410;margin-bottom:8px}
.empty-sub{font-size:13px;color:#9E9890;margin-bottom:24px}

/* buttons */
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:11px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-secondary{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Mono',monospace;cursor:pointer;transition:all .18s}
.btn-secondary:hover{border-color:#9E9890;color:#1A1410}
.btn-secondary:disabled{opacity:.5;cursor:not-allowed}
.spin-icon{animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* modal */
.modal-wrap{padding:28px;font-family:'DM Sans',sans-serif}
.modal-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px}
.modal-title{font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:#1A1410;display:flex;align-items:center;flex-wrap:wrap;gap:8px}
.modal-sub{font-size:13px;color:#9E9890;margin-top:3px}
.modal-close{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:9px;border:1.5px solid #E8E2DA;background:#fff;color:#9E9890;cursor:pointer;transition:all .15s;flex-shrink:0}
.modal-close:hover{border-color:#C0170F;color:#C0170F}
.modal-body{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
@media(max-width:640px){.modal-body{grid-template-columns:1fr}}
.modal-col{display:flex;flex-direction:column;gap:16px}
.modal-section{}
.ms-title{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.15em;text-transform:uppercase;color:#9E9890;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid #F0EDE8}
.ms-row{display:flex;justify-content:space-between;align-items:baseline;padding:5px 0;gap:10px}
.ms-lbl{font-size:12px;color:#9E9890;flex-shrink:0}
.ms-val{font-size:13px;font-weight:600;color:#1A1410;text-align:right}
.ms-link{font-size:13px;color:#C0170F;text-decoration:none;font-weight:600}.ms-link:hover{text-decoration:underline}
.modal-footer{padding-top:16px;border-top:1px solid #F0EDE8;display:flex;justify-content:flex-end}
</style>