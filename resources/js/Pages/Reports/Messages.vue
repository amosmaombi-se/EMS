<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('dashboard')" class="bc-link">Dashboard</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">Messages Report</span>
                    </div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Reports</div>
                    <h1 class="page-title">Messages Dashboard</h1>
                    <p class="page-sub">Monitor and manage all SMS & push message delivery</p>
                </div>
                <div class="header-actions">
                    <button @click="refreshData" :disabled="loading" class="btn-ghost">
                        <svg :class="['ico', loading ? 'spin' : '']" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Refresh
                    </button>
                    <button @click="exportMessages" :disabled="exportLoading" class="btn-ghost">
                        <svg v-if="exportLoading" class="ico spin" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <svg v-else width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ exportLoading ? 'Exporting…' : 'Export CSV' }}
                    </button>
                </div>
            </div>

            <!-- ── Stat Cards ── -->
            <div class="stats-grid">
                <div class="stat-card" style="--accent:#1D5C96">
                    <div class="stat-icon" style="background:rgba(29,92,150,.1)">
                        <svg width="18" height="18" fill="none" stroke="#1D5C96" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Total Messages</div>
                        <div class="stat-value">{{ stats.total }}</div>
                    </div>
                </div>

                <div class="stat-card" style="--accent:#16a34a">
                    <div class="stat-icon" style="background:rgba(22,163,74,.1)">
                        <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                    <div style="flex:1">
                        <div class="stat-label">Sent</div>
                        <div class="stat-value">{{ stats.sent }}</div>
                        <div class="stat-bar-track">
                            <div class="stat-bar-fill" :style="{width: stats.total > 0 ? `${(stats.sent/stats.total)*100}%` : '0%', background:'#16a34a'}"></div>
                        </div>
                        <div class="stat-rate" style="color:#16a34a">{{ stats.total > 0 ? ((stats.sent/stats.total)*100).toFixed(1) : 0 }}% success</div>
                    </div>
                </div>

                <div class="stat-card" style="--accent:#C0170F">
                    <div class="stat-icon" style="background:rgba(192,23,15,.08)">
                        <svg width="18" height="18" fill="none" stroke="#C0170F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <div style="flex:1">
                        <div class="stat-label">Failed</div>
                        <div class="stat-value">{{ stats.failed }}</div>
                        <div class="stat-bar-track">
                            <div class="stat-bar-fill" :style="{width: stats.total > 0 ? `${(stats.failed/stats.total)*100}%` : '0%', background:'#C0170F'}"></div>
                        </div>
                        <div class="stat-rate" style="color:#C0170F">{{ stats.total > 0 ? ((stats.failed/stats.total)*100).toFixed(1) : 0 }}% failure</div>
                    </div>
                </div>

                <div class="stat-card" style="--accent:#b45309">
                    <div class="stat-icon" style="background:rgba(249,178,51,.12)">
                        <svg width="18" height="18" fill="none" stroke="#b45309" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Pending</div>
                        <div class="stat-value">{{ stats.pending }}</div>
                    </div>
                </div>
            </div>

            <!-- ── Filters ── -->
            <div class="filter-card">
                <div class="filter-head">
                    <div>
                        <div class="filter-title">Messages List</div>
                        <div class="filter-sub">Monitor and filter message delivery status</div>
                    </div>
                    <div class="filter-head-actions">
                        <select v-model="filters.event_id" class="ep-select sm">
                            <option value="">All Events</option>
                            <option v-for="event in events" :key="event.id" :value="event.id">{{ event.title }}</option>
                        </select>
                        <select v-model="filters.status" class="ep-select sm">
                            <option value="">All Status</option>
                            <option value="sent">Sent</option>
                            <option value="failed">Failed</option>
                            <option value="permanently_failed">Perm. Failed</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                        </select>
                        <select v-model="filters.provider" class="ep-select sm">
                            <option value="">All Providers</option>
                            <option value="onfonmedia_v2">OnfonMedia V2</option>
                            <option value="fcm">Firebase (Push)</option>
                        </select>
                        <button @click="showFilters = !showFilters" class="btn-ghost sm">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            More
                        </button>
                    </div>
                </div>

                <!-- Search + per-page + bulk -->
                <div class="search-row">
                    <div class="search-wrap">
                        <svg class="search-ico" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input v-model="filters.search" type="search" placeholder="Search by phone number or message…" class="ep-input search-pad">
                    </div>
                    <div class="search-controls">
                        <div class="per-page-wrap">
                            <label class="pp-label">Show</label>
                            <select v-model="perPage" class="ep-select sm">
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                                <option :value="200">200</option>
                            </select>
                        </div>
                        <button @click="selectAll" class="btn-ghost sm">
                            {{ selectedMessages.length === messages.data.length && messages.data.length > 0 ? 'Deselect All' : 'Select All' }}
                        </button>
                        <div v-if="selectedMessages.length > 0" class="bulk-wrap" ref="bulkWrapRef">
                            <button @click="toggleBulkMenu" class="btn-cta sm">
                                Bulk Actions ({{ selectedMessages.length }})
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <Teleport to="body">
                                <div v-if="showBulkActions" class="bulk-menu" :style="bulkMenuStyle">
                                    <button @click="resendReminders" class="bulk-item">
                                        <svg width="13" height="13" fill="none" stroke="#1D5C96" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                        Send Reminders
                                        <span class="bulk-badge">{{ selectedMessages.length }}</span>
                                    </button>
                                    <button @click="exportSelected" class="bulk-item">
                                        <svg width="13" height="13" fill="none" stroke="#6B6560" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Export Selected
                                    </button>
                                </div>
                            </Teleport>
                        </div>
                    </div>
                </div>

                <!-- Advanced filters -->
                <div v-if="showFilters" class="adv-filters">
                    <div class="filter-field">
                        <label class="field-label">Date From</label>
                        <input v-model="filters.date_from" type="date" class="ep-input">
                    </div>
                    <div class="filter-field">
                        <label class="field-label">Date To</label>
                        <input v-model="filters.date_to" type="date" class="ep-input">
                    </div>
                    <div class="filter-field">
                        <label class="field-label">Phone Number</label>
                        <input v-model="filters.phone" type="text" placeholder="255…" class="ep-input">
                    </div>
                </div>
            </div>

            <!-- ── Table ── -->
            <div class="table-card">
                <!-- Loading overlay -->
                <div v-if="loading" class="table-loading">
                    <div class="loading-spinner"></div>
                    <span class="loading-txt">Loading messages…</span>
                </div>

                <div class="table-scroll">
                    <table class="ep-table">
                        <thead>
                            <tr>
                                <th style="width:40px">
                                    <input type="checkbox"
                                        :checked="selectedMessages.length === messages.data.length && messages.data.length > 0"
                                        @change="toggleSelectAll"
                                        class="ep-checkbox">
                                </th>
                                <th>ID</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Provider</th>
                                <th>Event</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Empty state -->
                            <tr v-if="!messages.data?.length">
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-icon">📭</div>
                                        <div class="empty-title">No messages found</div>
                                        <div class="empty-sub">Try adjusting your filters or check back later.</div>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="msg in messages.data" :key="msg.id" class="ep-row" :class="{selected: selectedMessages.includes(msg.id)}">
                                <td>
                                    <input type="checkbox" :value="msg.id" v-model="selectedMessages" class="ep-checkbox">
                                </td>
                                <td>
                                    <span class="msg-id">#{{ msg.id }}</span>
                                </td>
                                <td>
                                    <span class="msg-phone">{{ msg.phone }}</span>
                                </td>
                                <td>
                                    <div class="msg-preview" :title="msg.message">{{ msg.message }}</div>
                                </td>
                                <td>
                                    <span class="status-chip" :style="MSG_STATUS[msg.status] || MSG_STATUS.default">
                                        {{ fmtStatus(msg.status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="provider-tag">{{ msg.provider || '—' }}</span>
                                </td>
                                <td>
                                    <span v-if="msg.event" class="event-name">{{ msg.event.title }}</span>
                                    <span v-else class="muted">—</span>
                                </td>
                                <td>
                                    <div class="msg-date">{{ fmtDate(msg.created_at) }}</div>
                                    <div class="msg-time">{{ fmtTime(msg.created_at) }}</div>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <button @click="viewMessage(msg)" class="action-btn" title="View Details">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        <button v-if="canRetry(msg.status)" @click="retryMessage(msg)" class="action-btn retry" title="Retry">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="messages.data?.length" class="pagination-bar">
                    <div class="pg-info">
                        Showing <strong>{{ messages.from }}</strong>–<strong>{{ messages.to }}</strong> of <strong>{{ messages.total }}</strong> messages
                    </div>
                    <nav class="pg-nav">
                        <template v-for="(link, i) in messages.links" :key="i">
                            <button v-if="i === 0" @click="goToPage(link.url)" :disabled="!link.url" class="pg-link" :class="{disabled: !link.url}">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button v-else-if="i !== messages.links.length - 1" @click="goToPage(link.url)" :disabled="link.active || !link.url"
                                :class="['pg-link', link.active ? 'pg-active' : '', !link.url ? 'disabled' : '']"
                                v-html="link.label">
                            </button>
                            <button v-else @click="goToPage(link.url)" :disabled="!link.url" class="pg-link" :class="{disabled: !link.url}">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </template>
                    </nav>
                </div>
            </div>

        </div>

        <!-- ════════════════════════════════════════════════ -->
        <!-- Message Details Modal                           -->
        <!-- ════════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showDetailsModal" class="modal-overlay" @click.self="showDetailsModal = false">
                <div class="modal-box wide">
                    <div class="modal-header">
                        <div>
                            <div class="modal-eyebrow">Message Details</div>
                            <div class="modal-title">ID #{{ selectedMessage?.id }}</div>
                        </div>
                        <button @click="showDetailsModal = false" class="modal-close">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div v-if="selectedMessage" class="modal-body-grid">
                        <!-- Left -->
                        <div class="modal-col">
                            <div class="modal-section-title">Basic Information</div>
                            <dl class="modal-dl">
                                <div class="modal-dl-row">
                                    <dt>Phone</dt>
                                    <dd style="font-family:'DM Mono',monospace">{{ selectedMessage.phone }}</dd>
                                </div>
                                <div class="modal-dl-row">
                                    <dt>Status</dt>
                                    <dd><span class="status-chip" :style="MSG_STATUS[selectedMessage.status] || MSG_STATUS.default">{{ fmtStatus(selectedMessage.status) }}</span></dd>
                                </div>
                                <div class="modal-dl-row">
                                    <dt>Provider</dt>
                                    <dd>{{ selectedMessage.provider || 'N/A' }}</dd>
                                </div>
                                <div v-if="selectedMessage.provider_message_id" class="modal-dl-row">
                                    <dt>Provider ID</dt>
                                    <dd style="font-family:'DM Mono',monospace;font-size:10px">{{ selectedMessage.provider_message_id }}</dd>
                                </div>
                            </dl>

                            <div class="modal-section-title" style="margin-top:18px">Timestamps</div>
                            <dl class="modal-dl">
                                <div class="modal-dl-row">
                                    <dt>Created</dt>
                                    <dd>{{ fmtDateTime(selectedMessage.created_at) }}</dd>
                                </div>
                                <div v-if="selectedMessage.sent_at" class="modal-dl-row">
                                    <dt>Sent</dt>
                                    <dd style="color:#16a34a;font-weight:700">{{ fmtDateTime(selectedMessage.sent_at) }}</dd>
                                </div>
                                <div v-if="selectedMessage.failed_at" class="modal-dl-row">
                                    <dt>Failed</dt>
                                    <dd style="color:#C0170F;font-weight:700">{{ fmtDateTime(selectedMessage.failed_at) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Right -->
                        <div class="modal-col">
                            <div class="modal-section-title">Message Content</div>
                            <div class="msg-content-box">{{ selectedMessage.message }}</div>

                            <div v-if="selectedMessage.error_message" style="margin-top:14px">
                                <div class="modal-section-title" style="color:#C0170F">Error Details</div>
                                <div class="error-box">{{ selectedMessage.error_message }}</div>
                            </div>

                            <div v-if="selectedMessage.response" style="margin-top:14px">
                                <div class="modal-section-title">API Response</div>
                                <div class="json-box"><pre>{{ fmtJson(selectedMessage.response) }}</pre></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button @click="showDetailsModal = false" class="btn-ghost">Close</button>
                        <button v-if="selectedMessage && canRetry(selectedMessage.status)" @click="retryMessage(selectedMessage)" class="btn-retry">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Retry Message
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ════════════════════════════════════════════════ -->
        <!-- Reminder Modal                                  -->
        <!-- ════════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showReminderModal" class="modal-overlay" @click.self="showReminderModal = false">
                <div class="modal-box">
                    <div class="modal-header">
                        <div>
                            <div class="modal-eyebrow">Bulk Action</div>
                            <div class="modal-title">Send Reminder Message</div>
                            <div class="modal-subtitle">Customize your reminder for {{ selectedMessages.length }} recipients</div>
                        </div>
                        <button @click="showReminderModal = false" class="modal-close">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="reminder-body">
                        <div class="field">
                            <label class="field-label">Reminder Message</label>
                            <textarea v-model="reminderMessage" rows="5" placeholder="Enter your reminder message here…" class="ep-textarea"></textarea>
                            <div class="char-count">
                                <span>{{ reminderMessage.length }} / 160 characters</span>
                                <span style="font-weight:700">{{ Math.ceil(reminderMessage.length / 160) || 1 }} SMS</span>
                            </div>
                        </div>

                        <div class="tip-box">
                            <div class="tip-icon">💡</div>
                            <div>
                                <div class="tip-title">Tips for effective reminders</div>
                                <ul class="tip-list">
                                    <li>Keep it concise and clear</li>
                                    <li>Include relevant event details</li>
                                    <li>Add a call-to-action if needed</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button @click="showReminderModal = false" class="btn-ghost">Cancel</button>
                        <button @click="sendReminders" class="btn-cta">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            Send {{ selectedMessages.length }} Reminder{{ selectedMessages.length > 1 ? 's' : '' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue'
import { debounce } from 'lodash'
import Swal from 'sweetalert2'

const props = defineProps({
    messages: Object,
    stats:    Object,
    filters:  Object,
    events:   Array,
})

const loading          = ref(false)
const exportLoading    = ref(false)
const showFilters      = ref(false)
const showDetailsModal = ref(false)
const showReminderModal= ref(false)
const selectedMessage  = ref(null)
const selectedMessages = ref([])
const showBulkActions  = ref(false)
const bulkWrapRef      = ref(null)
const reminderMessage  = ref('')
const perPage          = ref(props.messages?.per_page || 50)

// Compute fixed position for the bulk dropdown
const bulkMenuStyle = computed(() => {
    if (!bulkWrapRef.value) return {}
    const rect = bulkWrapRef.value.getBoundingClientRect()
    return {
        position: 'fixed',
        top:  `${rect.bottom + 6}px`,
        right: `${window.innerWidth - rect.right}px`,
        zIndex: 9999,
    }
})

const toggleBulkMenu = () => {
    showBulkActions.value = !showBulkActions.value
}

// Close dropdown on outside click
const onClickOutside = (e) => {
    if (showBulkActions.value && bulkWrapRef.value && !bulkWrapRef.value.contains(e.target)) {
        showBulkActions.value = false
    }
}
onMounted(() => document.addEventListener('click', onClickOutside, true))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside, true))

const filters = ref({
    search:    props.filters?.search    || '',
    status:    props.filters?.status    || '',
    provider:  props.filters?.provider  || '',
    event_id:  props.filters?.event_id  || '',
    date_from: props.filters?.date_from || '',
    date_to:   props.filters?.date_to   || '',
    phone:     props.filters?.phone     || '',
})

watch(filters, debounce((f) => {
    loading.value = true
    router.get(route('reports.messages'), { ...f, per_page: perPage.value }, {
        preserveState: true, preserveScroll: true, replace: true,
        onFinish: () => { loading.value = false },
    })
}, 300), { deep: true })

watch(perPage, (v) => {
    loading.value = true
    router.get(route('reports.messages'), { ...filters.value, per_page: v }, {
        preserveState: true, preserveScroll: true, replace: true,
        onFinish: () => { loading.value = false },
    })
})

const goToPage = (url) => {
    if (!url) return
    loading.value = true
    const page = new URL(url, window.location.origin).searchParams.get('page')
    router.get(route('reports.messages'), { ...filters.value, page }, {
        preserveState: true, preserveScroll: true, replace: true,
        onFinish: () => { loading.value = false },
    })
}

const refreshData = () => {
    loading.value = true
    router.reload({ only: ['messages', 'stats'], onFinish: () => {
        loading.value = false
        Swal.fire({ title:'Refreshed!', text:'Data has been updated.', icon:'success', timer:1500, showConfirmButton:false, toast:true, position:'top-end' })
    }})
}

const viewMessage = (msg) => { selectedMessage.value = msg; showDetailsModal.value = true }
const canRetry = (status) => status === 'failed' || status === 'permanently_failed'

const retryMessage = (msg) => {
    Swal.fire({ title:'Retry Message?', text:`Retry sending to ${msg.phone}?`, icon:'question', showCancelButton:true,
        confirmButtonColor:'#C0170F', cancelButtonColor:'#6B7280', confirmButtonText:'Yes, retry'
    }).then((r) => {
        if (!r.isConfirmed) return
        router.post(route('reports.messages.retry', msg.id), {}, {
            onSuccess: () => { showDetailsModal.value = false; Swal.fire({ title:'Queued!', text:'Message queued for retry.', icon:'success', timer:2000, showConfirmButton:false }) },
            onError:   () => { Swal.fire({ title:'Error!', text:'Failed to queue message. Please try again.', icon:'error', timer:3000 }) },
        })
    })
}

const exportMessages = () => {
    exportLoading.value = true
    window.location.href = route('reports.export', { format:'csv', ...filters.value })
    setTimeout(() => { exportLoading.value = false }, 2000)
}

const selectAll = () => {
    selectedMessages.value = selectedMessages.value.length === props.messages.data.length && props.messages.data.length > 0
        ? [] : props.messages.data.map(m => m.id)
}

const toggleSelectAll = (e) => {
    selectedMessages.value = e.target.checked ? props.messages.data.map(m => m.id) : []
}

const resendReminders = () => {
    if (!selectedMessages.value.length) return
    showBulkActions.value    = false
    showReminderModal.value  = true
    reminderMessage.value    = 'This is a reminder about your upcoming event.'
}

const sendReminders = () => {
    if (!reminderMessage.value.trim()) {
        Swal.fire({ title:'Message Required', text:'Please enter a reminder message.', icon:'warning', timer:2000 }); return
    }
    Swal.fire({ title:'Send Reminders?', text:`Send reminder to ${selectedMessages.value.length} recipients?`, icon:'question',
        showCancelButton:true, confirmButtonColor:'#C0170F', cancelButtonColor:'#6B7280', confirmButtonText:'Yes, send'
    }).then((r) => {
        if (!r.isConfirmed) return
        router.post(route('messages.bulk-resend'), { message_ids: selectedMessages.value, reminder_message: reminderMessage.value }, {
            onSuccess: () => { selectedMessages.value = []; showReminderModal.value = false; reminderMessage.value = '';
                Swal.fire({ title:'Sent!', text:'Reminders queued for sending.', icon:'success', timer:2000, showConfirmButton:false }) },
            onError: () => { Swal.fire({ title:'Error!', text:'Failed to queue reminders.', icon:'error', timer:3000 }) },
        })
    })
}

const exportSelected = () => {
    if (!selectedMessages.value.length) return
    showBulkActions.value = false
    exportLoading.value = true
    window.location.href = route('reports.export', { format:'csv', message_ids: selectedMessages.value.join(','), ...filters.value })
    setTimeout(() => { exportLoading.value = false }, 2000)
}

// ── Status map ───────────────────────────────────────────────────────────────
const MSG_STATUS = {
    sent:               { background:'rgba(22,163,74,.1)',   color:'#16a34a',  border:'1px solid rgba(22,163,74,.25)'  },
    failed:             { background:'rgba(192,23,15,.08)',  color:'#C0170F',  border:'1px solid rgba(192,23,15,.2)'   },
    permanently_failed: { background:'rgba(192,23,15,.14)',  color:'#8B0000',  border:'1px solid rgba(139,0,0,.25)'    },
    pending:            { background:'rgba(249,178,51,.12)', color:'#b45309',  border:'1px solid rgba(249,178,51,.3)'  },
    processing:         { background:'rgba(29,92,150,.1)',   color:'#1D5C96',  border:'1px solid rgba(29,92,150,.25)'  },
    default:            { background:'rgba(158,152,144,.12)',color:'#6B6560',  border:'1px solid rgba(158,152,144,.3)' },
}

// ── Formatters ───────────────────────────────────────────────────────────────
const fmtStatus   = s => (s || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
const fmtDate     = d => { if (!d) return '—'; return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }) }
const fmtTime     = d => { if (!d) return ''; return new Date(d).toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' }) }
const fmtDateTime = d => { if (!d) return '—'; const dt = new Date(d); return dt.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) + ' at ' + dt.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' }) }
const fmtJson     = v => { try { return JSON.stringify(typeof v === 'string' ? JSON.parse(v) : v, null, 2) } catch { return v } }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}

/* ── Header ── */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.header-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;flex-shrink:0}
.breadcrumb{display:flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;margin-bottom:5px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C0B8}.bc-cur{color:#6B6560}
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
.stat-value{font-family:'Playfair Display',serif;font-size:24px;font-weight:900;color:#1A1410;line-height:1}
.stat-bar-track{height:4px;background:#F0EDE8;border-radius:4px;margin-top:6px;overflow:hidden}
.stat-bar-fill{height:100%;border-radius:4px;transition:width .6s ease}
.stat-rate{font-family:'DM Mono',monospace;font-size:9px;font-weight:700;margin-top:4px;letter-spacing:.08em}

/* ── Filter card ── */
.filter-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;box-shadow:0 1px 8px rgba(0,0,0,.04);margin-bottom:16px;overflow:hidden}
.filter-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 20px;border-bottom:1px solid #F0EDE8;background:#FAFAF8;flex-wrap:wrap}
.filter-title{font-family:'Playfair Display',serif;font-size:15px;font-weight:900;color:#1A1410}
.filter-sub{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px}
.filter-head-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}

.search-row{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid #F0EDE8;flex-wrap:wrap}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-ico{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9E9890;pointer-events:none}
.search-pad{padding-left:30px !important}
.search-controls{display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0}
.per-page-wrap{display:flex;align-items:center;gap:6px}
.pp-label{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;text-transform:uppercase;letter-spacing:.1em;white-space:nowrap}

.adv-filters{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:14px 20px;background:#F7F5F2;border-top:1px solid #F0EDE8}
@media(max-width:640px){.adv-filters{grid-template-columns:1fr}}
.filter-field{display:flex;flex-direction:column;gap:5px}
.field-label{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:#6B6560;font-weight:600}

/* ── Bulk menu ── */
.bulk-wrap{position:relative;isolation:isolate}
.bulk-menu{position:fixed;background:#fff;border:1.5px solid #E8E2DA;border-radius:14px;box-shadow:0 12px 40px rgba(0,0,0,.16),0 2px 8px rgba(0,0,0,.08);z-index:9999;min-width:200px;padding:6px;animation:fadeIn .15s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}
.bulk-item{display:flex;align-items:center;gap:10px;width:100%;padding:9px 14px;border:none;background:none;font-size:13px;font-family:'DM Sans',sans-serif;font-weight:600;color:#1A1410;cursor:pointer;border-radius:9px;transition:background .15s;text-align:left}
.bulk-item:hover{background:#F7F5F2}
.bulk-badge{margin-left:auto;padding:2px 8px;background:rgba(29,92,150,.12);border-radius:20px;font-size:10px;font-weight:700;color:#1D5C96;font-family:'DM Mono',monospace}

/* ── Inputs ── */
.ep-input,.ep-select,.ep-textarea{padding:8px 12px;border:1.5px solid #E8E2DA;border-radius:10px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;width:100%}
.ep-input:focus,.ep-select:focus,.ep-textarea:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}
.ep-select.sm{width:auto;font-size:12px;padding:6px 10px}
.ep-textarea{resize:vertical;min-height:100px;line-height:1.6}

/* ── Table ── */
.table-card{background:#fff;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.04);position:relative}
.table-loading{position:absolute;inset:0;background:rgba(247,245,242,.85);display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:10;gap:10px}
.loading-spinner{width:28px;height:28px;border:3px solid #E8E2DA;border-top-color:#C0170F;border-radius:50%;animation:spin .7s linear infinite}
.loading-txt{font-family:'DM Mono',monospace;font-size:11px;color:#9E9890}
@keyframes spin{to{transform:rotate(360deg)}}
.table-scroll{overflow-x:auto}
.ep-table{width:100%;border-collapse:collapse;min-width:860px}
.ep-table thead tr{background:#F0EDE8;border-bottom:2px solid #E8E2DA}
.ep-table th{padding:10px 14px;text-align:left;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:#6B6560;font-weight:700;white-space:nowrap}
.ep-table tbody tr{border-bottom:1px solid #F0EDE8}
.ep-row{transition:background .15s}
.ep-row:hover{background:#FAFAF8}
.ep-row.selected{background:rgba(192,23,15,.04)}
.ep-row:last-child{border-bottom:none}
.ep-table td{padding:11px 14px;vertical-align:middle}
.ep-checkbox{width:14px;height:14px;accent-color:#C0170F;cursor:pointer}

/* ── Table cell content ── */
.msg-id{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#6B6560}
.msg-phone{font-family:'DM Mono',monospace;font-size:12px;font-weight:600;color:#1A1410}
.msg-preview{font-size:12px;color:#1A1410;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.status-chip{display:inline-flex;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;font-family:'DM Mono',monospace;text-transform:capitalize;letter-spacing:.03em;white-space:nowrap}
.provider-tag{font-family:'DM Mono',monospace;font-size:10px;color:#6B6560;background:#F0EDE8;padding:2px 8px;border-radius:20px;border:1px solid #E8E2DA}
.event-name{font-size:12px;color:#1A1410;font-weight:600;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block}
.muted{color:#9E9890;font-size:12px}
.msg-date{font-family:'DM Mono',monospace;font-size:11px;color:#1A1410;font-weight:600}
.msg-time{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:1px}
.row-actions{display:flex;align-items:center;gap:4px}
.action-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;border:none;background:none;color:#9E9890;cursor:pointer;transition:all .15s}
.action-btn:hover{background:rgba(192,23,15,.08);color:#C0170F}
.action-btn.retry:hover{background:rgba(249,178,51,.12);color:#b45309}

/* ── Empty state ── */
.empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 24px;text-align:center}
.empty-icon{font-size:44px;margin-bottom:10px;opacity:.35}
.empty-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#1A1410;margin-bottom:6px}
.empty-sub{font-size:13px;color:#9E9890}

/* ── Pagination ── */
.pagination-bar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 20px;background:#F0EDE8;border-top:1px solid #E8E2DA;flex-wrap:wrap}
.pg-info{font-family:'DM Mono',monospace;font-size:11px;color:#6B6560}
.pg-nav{display:flex;gap:3px;flex-wrap:wrap}
.pg-link{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 8px;border-radius:8px;font-family:'DM Mono',monospace;font-size:11px;font-weight:600;color:#6B6560;background:#fff;border:1px solid #E8E2DA;cursor:pointer;transition:all .15s}
.pg-link:hover:not(.disabled):not(.pg-active){border-color:#C0170F;color:#C0170F}
.pg-active{background:#C0170F;color:#fff;border-color:#C0170F}
.pg-link.disabled{opacity:.4;cursor:not-allowed}

/* ── Buttons ── */
.btn-cta{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:11px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;box-shadow:0 4px 14px rgba(192,23,15,.25);animation:shine 3s linear infinite;transition:transform .2s}
.btn-cta.sm{padding:7px 14px;font-size:12px}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-cta:hover{transform:translateY(-1px)}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;white-space:nowrap}
.btn-ghost.sm{padding:6px 10px;font-size:11px}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-retry{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:11px;border:none;cursor:pointer;background:rgba(249,178,51,.9);color:#7c2d12;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;transition:background .18s}
.btn-retry:hover{background:#F9B233}
.ico{display:block;flex-shrink:0}
.spin{animation:spin .7s linear infinite}

/* ── Modals ── */
.modal-overlay{position:fixed;inset:0;background:rgba(15,13,12,.55);display:flex;align-items:center;justify-content:center;z-index:50;padding:24px;backdrop-filter:blur(3px)}
.modal-box{background:#fff;border-radius:20px;padding:0;max-width:460px;width:100%;box-shadow:0 24px 60px rgba(0,0,0,.18);border:1px solid #E8E2DA;overflow:hidden;max-height:90vh;display:flex;flex-direction:column}
.modal-box.wide{max-width:780px}
.modal-header{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:20px 24px;background:#F0EDE8;border-bottom:1px solid #E8E2DA;flex-shrink:0}
.modal-eyebrow{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:#9E9890;margin-bottom:3px}
.modal-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#1A1410}
.modal-subtitle{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px}
.modal-close{width:32px;height:32px;border-radius:8px;border:1.5px solid #E8E2DA;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#6B6560;transition:all .15s;flex-shrink:0}
.modal-close:hover{border-color:#C0170F;color:#C0170F}
.modal-body-grid{display:grid;grid-template-columns:1fr 1fr;gap:0;overflow-y:auto;flex:1}
.modal-col{padding:20px 22px;border-right:1px solid #F0EDE8}
.modal-col:last-child{border-right:none}
.modal-section-title{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:#9E9890;font-weight:700;margin-bottom:10px}
.modal-dl{display:flex;flex-direction:column;gap:0}
.modal-dl-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #F7F5F2}
.modal-dl-row:last-child{border-bottom:none}
.modal-dl-row dt{font-size:11px;color:#9E9890}
.modal-dl-row dd{font-size:12px;font-weight:600;color:#1A1410;text-align:right}
.modal-footer{display:flex;justify-content:flex-end;gap:8px;padding:16px 24px;background:#FAFAF8;border-top:1px solid #E8E2DA;flex-shrink:0}
.msg-content-box{background:#F7F5F2;border:1px solid #E8E2DA;border-radius:12px;padding:14px;font-size:12px;color:#1A1410;line-height:1.7;white-space:pre-line}
.error-box{background:rgba(192,23,15,.06);border:1px solid rgba(192,23,15,.2);border-radius:12px;padding:14px;font-size:12px;color:#C0170F;line-height:1.6}
.json-box{background:#F0EDE8;border:1px solid #E8E2DA;border-radius:12px;padding:12px;max-height:180px;overflow-y:auto}
.json-box pre{font-family:'DM Mono',monospace;font-size:10px;color:#1A1410;margin:0;white-space:pre-wrap;word-break:break-all}

/* ── Reminder modal ── */
.reminder-body{padding:20px 24px;overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:14px}
.field{display:flex;flex-direction:column;gap:5px}
.char-count{display:flex;justify-content:space-between;font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-top:4px}
.tip-box{display:flex;gap:10px;background:rgba(29,92,150,.06);border:1px solid rgba(29,92,150,.15);border-radius:12px;padding:14px}
.tip-icon{font-size:18px;flex-shrink:0}
.tip-title{font-size:12px;font-weight:700;color:#1D5C96;margin-bottom:6px}
.tip-list{font-size:11px;color:#1D5C96;padding-left:14px;margin:0;display:flex;flex-direction:column;gap:3px}
</style>