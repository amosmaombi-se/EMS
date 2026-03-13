<template>
    <AuthenticatedLayout>
        <div class="page-wrap">

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <Link :href="route('bookings.index')" class="bc-link">Bookings</Link>
                        <span class="bc-sep">›</span>
                        <span class="bc-cur">{{ booking.booking_number }}</span>
                    </div>
                    <div class="page-eyebrow"><span class="eyebrow-dot"></span>Booking Detail</div>
                    <div class="title-row">
                        <h1 class="page-title">{{ booking.booking_number }}</h1>
                        <span class="status-pill" :style="BOOKING_STATUS[booking.status] || BOOKING_STATUS.pending">
                            <span class="pill-dot"></span>
                            {{ booking.status?.replace('_', ' ') }}
                        </span>
                        <span class="status-pill" :style="PAYMENT_STATUS[booking.payment_status] || PAYMENT_STATUS.unpaid">
                            {{ booking.payment_status?.replace('_', ' ') }}
                        </span>
                    </div>
                    <p class="page-sub">
                        Created {{ fmtDate(booking.booking_date) }}
                        <span v-if="booking.due_date">
                            · Due
                            <span :style="{color: isPastDue(booking.due_date) ? '#C0170F' : '#6B6560', fontWeight: isPastDue(booking.due_date) ? 700 : 400}">
                                {{ fmtDate(booking.due_date) }}
                            </span>
                        </span>
                    </p>
                </div>
                <div class="header-actions">
                    <Link :href="route('bookings.edit', booking.id)" class="btn-cta">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Booking
                    </Link>
                    <button @click="showDeleteModal = true" class="btn-ghost btn-danger-ghost">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            </div>

            <!-- ── Body ── -->
            <div class="body-grid">

                <!-- ════════════════════════════════════════════════════ -->
                <!-- LEFT / MAIN                                         -->
                <!-- ════════════════════════════════════════════════════ -->
                <div class="main-col">

                    <!-- Event Details -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(192,23,15,.1)">📅</span>
                            <div>
                                <div class="card-title">Event Details</div>
                                <div class="card-sub">Event, schedule, and venue information</div>
                            </div>
                        </div>
                        <div class="card-body two-detail-cols">

                            <!-- Event -->
                            <div>
                                <div class="detail-label">Event</div>
                                <div class="detail-val-lg">{{ booking.event?.title || '—' }}</div>
                                <div v-if="booking.event?.event_type" class="detail-val-sm capitalize" style="margin-top:2px">
                                    {{ booking.event.event_type.replace('_', ' ') }}
                                </div>
                                <div class="meta-pills">
                                    <span class="meta-pill" v-if="booking.event_date">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                        {{ fmtDate(booking.event_date) }}
                                    </span>
                                    <span class="meta-pill" v-if="booking.event_end_date">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        {{ fmtDate(booking.event_end_date) }}
                                    </span>
                                    <span class="meta-pill" v-if="booking.start_time">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                        {{ fmtTime(booking.start_time) }}<span v-if="booking.end_time"> – {{ fmtTime(booking.end_time) }}</span>
                                    </span>
                                    <span class="meta-pill" v-if="booking.guest_count">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        {{ booking.guest_count }} guests
                                    </span>
                                </div>
                            </div>

                            <!-- Venue -->
                            <div>
                                <div class="detail-label">Venue</div>
                                <div v-if="booking.venue" class="venue-card">
                                    <div class="venue-name">{{ booking.venue.name }}</div>
                                    <div class="venue-meta">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ [booking.venue.city, booking.venue.state].filter(Boolean).join(', ') || 'Location TBD' }}
                                    </div>
                                    <div class="venue-meta capitalize">
                                        {{ booking.venue.type?.replace('_', ' ') }}
                                        <span v-if="booking.venue.capacity_max"> · Cap. {{ booking.venue.capacity_max?.toLocaleString() }}</span>
                                    </div>
                                </div>
                                <div v-else class="detail-val-sm" style="color:#9E9890;font-style:italic">No venue assigned</div>
                            </div>

                        </div>
                    </div>

                    <!-- Booked Items -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(29,92,150,.1)">📦</span>
                            <div>
                                <div class="card-title">Booked Items</div>
                                <div class="card-sub">{{ (booking.items ?? []).length }} item{{ (booking.items ?? []).length !== 1 ? 's' : '' }}</div>
                            </div>
                        </div>

                        <div v-if="!booking.items?.length" class="empty-state">
                            <div class="empty-icon">📭</div>
                            <div class="empty-txt">No items added to this booking</div>
                        </div>

                        <div v-else class="items-list">
                            <div v-for="item in booking.items" :key="item.id" class="item-row">
                                <div class="item-avatar">
                                    <svg width="15" height="15" fill="none" stroke="#1D5C96" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ item.itemable?.name || 'Service' }}</div>
                                    <div class="item-cat">{{ item.category || item.itemable?.category || 'Vendor Service' }}<span v-if="item.vendor_name"> · {{ item.vendor_name }}</span></div>
                                </div>
                                <div class="item-unit">
                                    <div class="item-label">Unit price</div>
                                    <div class="item-num">TZS {{ fmt(item.unit_price) }}</div>
                                </div>
                                <div class="item-qty">
                                    <div class="item-label">Qty</div>
                                    <div class="item-num">{{ item.quantity }}</div>
                                </div>
                                <div class="item-sub">
                                    <div class="item-label">Subtotal</div>
                                    <div class="item-num" style="font-weight:800;color:#1A1410">TZS {{ fmt(item.unit_price * item.quantity) }}</div>
                                </div>
                            </div>

                            <div class="items-footer">
                                <span class="items-footer-label">Items Total</span>
                                <span class="items-footer-val">TZS {{ fmt(itemsTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div v-if="booking.customer_notes || booking.internal_notes" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(249,178,51,.12)">📝</span>
                            <div>
                                <div class="card-title">Notes</div>
                                <div class="card-sub">Customer requests and internal remarks</div>
                            </div>
                        </div>
                        <div class="card-body two-detail-cols">
                            <div v-if="booking.customer_notes">
                                <div class="detail-label">Customer Notes</div>
                                <p class="note-txt">{{ booking.customer_notes }}</p>
                            </div>
                            <div v-if="booking.internal_notes">
                                <div class="detail-label" style="display:flex;align-items:center;gap:4px">
                                    <svg width="10" height="10" fill="none" stroke="#9E9890" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                    Internal Notes (Staff Only)
                                </div>
                                <p class="note-txt">{{ booking.internal_notes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(107,101,96,.1)">⏱️</span>
                            <div>
                                <div class="card-title">Activity</div>
                                <div class="card-sub">Booking history and events</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <ol class="timeline">

                                <li class="tl-item">
                                    <div class="tl-dot" style="background:rgba(29,92,150,.15)">
                                        <svg width="10" height="10" fill="#1D5C96" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div class="tl-content">
                                        <div class="tl-title">Booking Created</div>
                                        <div class="tl-time">{{ fmtDateTime(booking.created_at) }}</div>
                                        <div v-if="booking.user" class="tl-by">by {{ booking.user.name }}</div>
                                    </div>
                                </li>

                                <li v-if="booking.confirmed_at" class="tl-item">
                                    <div class="tl-dot" style="background:rgba(22,163,74,.12)">
                                        <svg width="10" height="10" fill="#16a34a" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div class="tl-content">
                                        <div class="tl-title">Confirmed</div>
                                        <div class="tl-time">{{ fmtDateTime(booking.confirmed_at) }}</div>
                                    </div>
                                </li>

                                <li v-if="booking.updated_at !== booking.created_at" class="tl-item">
                                    <div class="tl-dot" style="background:rgba(158,152,144,.15)">
                                        <svg width="10" height="10" fill="none" stroke="#6B6560" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div class="tl-content">
                                        <div class="tl-title">Last Updated</div>
                                        <div class="tl-time">{{ fmtDateTime(booking.updated_at) }}</div>
                                    </div>
                                </li>

                                <li v-if="booking.status === 'cancelled'" class="tl-item">
                                    <div class="tl-dot" style="background:rgba(192,23,15,.1)">
                                        <svg width="10" height="10" fill="#C0170F" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div class="tl-content">
                                        <div class="tl-title" style="color:#C0170F">Cancelled</div>
                                        <div v-if="booking.cancellation_reason" class="tl-by">{{ booking.cancellation_reason }}</div>
                                    </div>
                                </li>

                            </ol>
                        </div>
                    </div>

                </div>

                <!-- ════════════════════════════════════════════════════ -->
                <!-- RIGHT / SIDEBAR                                     -->
                <!-- ════════════════════════════════════════════════════ -->
                <div class="sidebar-col">

                    <!-- Financial Summary -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(22,163,74,.1)">💰</span>
                            <div>
                                <div class="card-title">Financials</div>
                                <div class="card-sub">Payment progress &amp; balance</div>
                            </div>
                        </div>
                        <div class="card-body">

                            <!-- Progress bar -->
                            <div class="prog-wrap">
                                <div class="prog-labels">
                                    <span>Payment Progress</span>
                                    <span style="font-weight:700;color:#1A1410">{{ paymentPercent }}%</span>
                                </div>
                                <div class="prog-track">
                                    <div class="prog-fill"
                                        :style="{width: Math.min(paymentPercent, 100) + '%', background: paymentPercent >= 100 ? '#16a34a' : 'linear-gradient(90deg,#C0170F,#F05A00)'}">
                                    </div>
                                </div>
                            </div>

                            <div class="fin-rows">
                                <div class="fin-row">
                                    <span class="fin-label">Total Amount</span>
                                    <span class="fin-val" style="font-weight:800;color:#1A1410">TZS {{ fmt(booking.total_amount) }}</span>
                                </div>
                                <div class="fin-row">
                                    <span class="fin-label">Amount Paid</span>
                                    <span class="fin-val" style="color:#16a34a;font-weight:700">TZS {{ fmt(booking.paid_amount) }}</span>
                                </div>
                                <div class="fin-row fin-balance">
                                    <span class="fin-label" style="font-weight:700;color:#1A1410">Balance Due</span>
                                    <span class="fin-val" :style="{color: dueAmount > 0 ? '#C0170F' : '#16a34a', fontWeight:800}">
                                        {{ dueAmount > 0 ? 'TZS ' + fmt(dueAmount) : '✓ Fully Paid' }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="booking.due_date" class="due-row">
                                <span class="due-label">Payment Due</span>
                                <span :style="{color: isPastDue(booking.due_date) ? '#C0170F' : '#6B6560', fontWeight: isPastDue(booking.due_date) ? 700 : 400}" style="font-size:11px;font-family:'DM Mono',monospace">
                                    {{ fmtDate(booking.due_date) }}
                                    <span v-if="isPastDue(booking.due_date)" class="overdue-badge">Overdue</span>
                                </span>
                            </div>

                            <button @click="downloadInvoice" class="invoice-btn">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download Invoice
                            </button>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(192,23,15,.1)">📋</span>
                            <div>
                                <div class="card-title">Booking Details</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="detail-list">
                                <div class="dl-row">
                                    <dt>Number</dt>
                                    <dd style="font-family:'DM Mono',monospace;font-size:11px">{{ booking.booking_number }}</dd>
                                </div>
                                <div class="dl-row">
                                    <dt>Status</dt>
                                    <dd><span class="status-chip-sm" :style="BOOKING_STATUS[booking.status] || BOOKING_STATUS.pending">{{ booking.status?.replace('_', ' ') }}</span></dd>
                                </div>
                                <div class="dl-row">
                                    <dt>Payment</dt>
                                    <dd><span class="status-chip-sm" :style="PAYMENT_STATUS[booking.payment_status] || PAYMENT_STATUS.unpaid">{{ booking.payment_status?.replace('_', ' ') }}</span></dd>
                                </div>
                                <div class="dl-row">
                                    <dt>Event Date</dt>
                                    <dd>{{ fmtDate(booking.event_date) }}</dd>
                                </div>
                                <div v-if="booking.event_end_date" class="dl-row">
                                    <dt>End Date</dt>
                                    <dd>{{ fmtDate(booking.event_end_date) }}</dd>
                                </div>
                                <div v-if="booking.guest_count" class="dl-row">
                                    <dt>Guests</dt>
                                    <dd>{{ booking.guest_count }}</dd>
                                </div>
                                <div class="dl-row">
                                    <dt>Created</dt>
                                    <dd>{{ fmtDate(booking.booking_date) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Client -->
                    <div v-if="booking.user" class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(29,92,150,.1)">👤</span>
                            <div>
                                <div class="card-title">Client</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="client-row">
                                <div class="client-avatar">{{ booking.user.name?.charAt(0).toUpperCase() }}</div>
                                <div class="client-info">
                                    <div class="client-name">{{ booking.user.name }}</div>
                                    <div class="client-email">{{ booking.user.email }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="ep-card">
                        <div class="card-head">
                            <span class="card-icon" style="background:rgba(107,101,96,.1)">⚡</span>
                            <div>
                                <div class="card-title">Quick Actions</div>
                            </div>
                        </div>
                        <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
                            <Link :href="route('bookings.edit', booking.id)" class="action-row-btn">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Booking
                            </Link>
                            <button @click="downloadInvoice" class="action-row-btn">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download Invoice
                            </button>
                            <button v-if="booking.status !== 'cancelled'" @click="confirmCancel = true" class="action-row-btn" style="color:#b45309">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M4.93 4.93l14.14 14.14"/></svg>
                                Cancel Booking
                            </button>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="danger-card">
                        <div class="danger-head">⚠️ Danger Zone</div>
                        <p class="danger-txt">Permanently delete this booking and all associated records. This cannot be undone.</p>
                        <button @click="showDeleteModal = true" class="btn-delete">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Booking
                        </button>
                    </div>

                </div>
            </div>

        </div>

        <!-- ── Delete Modal ── -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
                <div class="modal-box">
                    <div class="modal-icon-wrap" style="background:rgba(192,23,15,.1)">
                        <svg width="22" height="22" fill="none" stroke="#C0170F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="modal-title">Delete Booking</div>
                    <p class="modal-body">
                        Are you sure you want to permanently delete booking
                        <strong style="color:#1A1410">{{ booking.booking_number }}</strong>?
                        All associated items and records will be removed. This action cannot be undone.
                    </p>
                    <div class="modal-actions">
                        <button @click="showDeleteModal = false" class="btn-ghost">Cancel</button>
                        <button @click="deleteBooking" class="btn-cta-danger">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Booking
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ── Cancel Confirm Modal ── -->
        <Teleport to="body">
            <div v-if="confirmCancel" class="modal-overlay" @click.self="confirmCancel = false">
                <div class="modal-box">
                    <div class="modal-icon-wrap" style="background:rgba(249,178,51,.15)">
                        <svg width="22" height="22" fill="none" stroke="#b45309" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M4.93 4.93l14.14 14.14"/></svg>
                    </div>
                    <div class="modal-title">Cancel Booking</div>
                    <p class="modal-body">
                        Are you sure you want to cancel booking
                        <strong style="color:#1A1410">{{ booking.booking_number }}</strong>?
                        This will notify the client and mark the booking as cancelled.
                    </p>
                    <div class="modal-actions">
                        <button @click="confirmCancel = false" class="btn-ghost">Keep Booking</button>
                        <button @click="cancelBooking" class="btn-cta-warn">
                            Cancel Booking
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
import { ref, computed } from 'vue'

const props = defineProps({ booking: Object })

const showDeleteModal = ref(false)
const confirmCancel   = ref(false)

const itemsTotal = computed(() =>
    (props.booking.items ?? []).reduce((sum, i) => sum + parseFloat(i.unit_price || 0) * (i.quantity ?? 1), 0)
)

const dueAmount = computed(() =>
    Math.max(0, parseFloat(props.booking.total_amount || 0) - parseFloat(props.booking.paid_amount || 0))
)

const paymentPercent = computed(() => {
    const total = parseFloat(props.booking.total_amount || 0)
    const paid  = parseFloat(props.booking.paid_amount  || 0)
    if (!total) return 0
    return Math.round((paid / total) * 100)
})

const deleteBooking = () => {
    router.delete(route('bookings.destroy', props.booking.id), {
        onSuccess: () => { showDeleteModal.value = false },
    })
}

const cancelBooking = () => {
    router.patch(route('bookings.cancel', props.booking.id), {}, {
        onSuccess: () => { confirmCancel.value = false },
    })
}

const downloadInvoice = () => router.get(route('bookings.invoice', props.booking.id))

// ── Status maps ──────────────────────────────────────────────────────────────
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
    refunded:       { background:'rgba(29,92,150,.1)',   color:'#1D5C96',  border:'1px solid rgba(29,92,150,.2)'  },
}

// ── Formatters ───────────────────────────────────────────────────────────────
const fmt = v => parseFloat(v||0).toLocaleString('en-US', { minimumFractionDigits:0, maximumFractionDigits:0 })

const fmtDate = d => {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' })
}

const fmtDateTime = dt => {
    if (!dt) return '—'
    return new Date(dt).toLocaleString('en-US', { year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'2-digit' })
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

const today = new Date().toISOString().split('T')[0]
const isPastDue = d => d && d.split('T')[0] < today
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

*{box-sizing:border-box}
.page-wrap{background:#F7F5F2;min-height:100vh;padding:28px 24px 72px;font-family:'DM Sans',sans-serif;color:#1A1410}

/* ── Header ── */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:26px;flex-wrap:wrap}
.header-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;flex-shrink:0}
.breadcrumb{display:flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.12em;margin-bottom:5px}
.bc-link{color:#9E9890;text-decoration:none;transition:color .15s}.bc-link:hover{color:#C0170F}
.bc-sep{color:#C8C0B8}.bc-cur{color:#6B6560}
.page-eyebrow{display:flex;align-items:center;gap:7px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;color:#9E9890;text-transform:uppercase;margin-bottom:5px}
.eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#C0170F;animation:blink .9s ease-in-out infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.title-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:5px}
.page-title{font-family:'Playfair Display',serif;font-size:clamp(20px,3vw,28px);font-weight:900;color:#1A1410;line-height:1.15;margin:0}
.page-sub{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}
.status-pill{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;font-family:'DM Mono',monospace;text-transform:capitalize;letter-spacing:.04em}
.pill-dot{width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0}

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
.card-body{padding:18px}

/* ── Detail sections ── */
.two-detail-cols{display:grid;grid-template-columns:1fr 1fr;gap:22px}
@media(max-width:640px){.two-detail-cols{grid-template-columns:1fr}}
.detail-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.13em;color:#9E9890;font-weight:600;margin-bottom:6px}
.detail-val-lg{font-size:15px;font-weight:700;color:#1A1410;line-height:1.3}
.detail-val-sm{font-size:12px;color:#6B6560;font-family:'DM Mono',monospace}
.meta-pills{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}
.meta-pill{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;background:#F0EDE8;border:1px solid #E8E2DA;border-radius:20px;font-family:'DM Mono',monospace;font-size:10px;color:#6B6560;font-weight:500}

/* Venue mini-card */
.venue-card{background:#F7F5F2;border:1px solid #E8E2DA;border-radius:12px;padding:12px 14px}
.venue-name{font-size:13px;font-weight:700;color:#1A1410;margin-bottom:5px}
.venue-meta{display:flex;align-items:center;gap:4px;font-size:11px;color:#6B6560;font-family:'DM Mono',monospace;margin-bottom:2px}

/* Notes */
.note-txt{font-size:13px;color:#4A4440;line-height:1.7;white-space:pre-line}

/* ── Items ── */
.empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:36px;gap:6px}
.empty-icon{font-size:32px;opacity:.35}
.empty-txt{font-size:12px;color:#9E9890;font-family:'DM Mono',monospace}
.items-list{display:flex;flex-direction:column}
.item-row{display:flex;align-items:center;gap:14px;padding:13px 18px;border-bottom:1px solid #F0EDE8;transition:background .15s}
.item-row:hover{background:#FAFAF8}
.item-row:last-child{border-bottom:none}
.item-avatar{width:36px;height:36px;border-radius:9px;background:rgba(29,92,150,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.item-info{flex:1;min-width:0}
.item-name{font-size:13px;font-weight:700;color:#1A1410;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.item-cat{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:1px}
.item-label{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890;margin-bottom:2px}
.item-num{font-family:'DM Mono',monospace;font-size:12px;font-weight:600;color:#6B6560;white-space:nowrap}
.item-unit,.item-qty,.item-sub{text-align:right;flex-shrink:0}
.item-qty{min-width:36px}
.item-sub{min-width:100px}
.items-footer{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:#F0EDE8;border-top:1px solid #E8E2DA}
.items-footer-label{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#6B6560}
.items-footer-val{font-family:'DM Mono',monospace;font-size:13px;font-weight:800;color:#1A1410}

/* ── Timeline ── */
.timeline{position:relative;list-style:none;padding:0;margin:0;border-left:2px solid #F0EDE8;padding-left:20px;display:flex;flex-direction:column;gap:18px}
.tl-item{position:relative}
.tl-dot{position:absolute;left:-30px;top:1px;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid #fff;box-shadow:0 0 0 1px #E8E2DA}
.tl-title{font-size:13px;font-weight:700;color:#1A1410;margin-bottom:2px}
.tl-time{font-family:'DM Mono',monospace;font-size:10px;color:#9E9890}
.tl-by{font-size:11px;color:#6B6560;margin-top:2px}

/* ── Financials sidebar ── */
.prog-wrap{margin-bottom:16px}
.prog-labels{display:flex;justify-content:space-between;font-family:'DM Mono',monospace;font-size:10px;color:#9E9890;margin-bottom:6px}
.prog-track{height:7px;background:#F0EDE8;border-radius:10px;overflow:hidden}
.prog-fill{height:100%;border-radius:10px;transition:width .5s ease}
.fin-rows{display:flex;flex-direction:column;gap:6px}
.fin-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #F7F5F2}
.fin-balance{padding-top:8px;border-top:1px solid #E8E2DA;border-bottom:none;margin-top:2px}
.fin-label{font-size:12px;color:#6B6560}
.fin-val{font-family:'DM Mono',monospace;font-size:12px;font-weight:600}
.due-row{display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-top:12px;border-top:1px solid #F0EDE8}
.due-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890}
.overdue-badge{display:inline-flex;padding:1px 7px;background:rgba(192,23,15,.1);border:1px solid rgba(192,23,15,.2);border-radius:20px;font-size:9px;font-weight:700;color:#C0170F;margin-left:4px}
.invoice-btn{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:9px;border-radius:10px;border:1.5px solid #E8E2DA;background:#F7F5F2;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;margin-top:14px}
.invoice-btn:hover{border-color:#C0170F;color:#C0170F;background:rgba(192,23,15,.04)}

/* ── Detail list (sidebar) ── */
.detail-list{display:flex;flex-direction:column;gap:0}
.dl-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #F7F5F2}
.dl-row:last-child{border-bottom:none}
.dl-row dt{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#9E9890}
.dl-row dd{font-size:12px;font-weight:600;color:#1A1410;font-family:'DM Mono',monospace}
.status-chip-sm{display:inline-flex;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;font-family:'DM Mono',monospace;text-transform:capitalize}

/* ── Client ── */
.client-row{display:flex;align-items:center;gap:12px}
.client-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#C0170F,#F05A00);display:flex;align-items:center;justify-content:center;color:#fff;font-family:'DM Mono',monospace;font-size:15px;font-weight:700;flex-shrink:0}
.client-name{font-size:13px;font-weight:700;color:#1A1410}
.client-email{font-size:11px;color:#9E9890;font-family:'DM Mono',monospace;margin-top:2px}

/* ── Quick Actions ── */
.action-row-btn{display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:10px;border:1.5px solid #E8E2DA;background:#F7F5F2;color:#6B6560;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;text-decoration:none;width:100%}
.action-row-btn:hover{border-color:#9E9890;background:#fff;color:#1A1410}

/* ── Danger Zone ── */
.danger-card{background:#fff;border:1.5px solid rgba(192,23,15,.2);border-radius:16px;padding:16px;box-shadow:0 1px 8px rgba(192,23,15,.05)}
.danger-head{font-family:'DM Mono',monospace;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#C0170F;margin-bottom:8px}
.danger-txt{font-size:12px;color:#9E9890;margin-bottom:12px;line-height:1.5}
.btn-delete{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:9px;border-radius:10px;border:1.5px solid rgba(192,23,15,.3);background:rgba(192,23,15,.06);color:#C0170F;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s}
.btn-delete:hover{background:rgba(192,23,15,.12);border-color:#C0170F}

/* ── Buttons ── */
.btn-cta{display:inline-flex;align-items:center;gap:7px;padding:10px 18px;border-radius:11px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;text-decoration:none;box-shadow:0 4px 14px rgba(192,23,15,.28);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-cta:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(192,23,15,.38)}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:11px;border:1.5px solid #E8E2DA;background:#fff;color:#6B6560;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .18s;text-decoration:none;white-space:nowrap}
.btn-ghost:hover{border-color:#9E9890;color:#1A1410}
.btn-danger-ghost{border-color:rgba(192,23,15,.25);color:#C0170F}
.btn-danger-ghost:hover{border-color:#C0170F;background:rgba(192,23,15,.05)}

/* ── Modals ── */
.modal-overlay{position:fixed;inset:0;background:rgba(15,13,12,.55);display:flex;align-items:center;justify-content:center;z-index:50;padding:24px;backdrop-filter:blur(3px)}
.modal-box{background:#fff;border-radius:20px;padding:28px;max-width:420px;width:100%;box-shadow:0 24px 60px rgba(0,0,0,.2);border:1px solid #E8E2DA}
.modal-icon-wrap{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.modal-title{font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:#1A1410;text-align:center;margin-bottom:10px}
.modal-body{font-size:13px;color:#6B6560;text-align:center;line-height:1.6;margin-bottom:22px}
.modal-actions{display:flex;justify-content:center;gap:10px}
.btn-cta-danger{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:11px;border:none;cursor:pointer;background:#C0170F;color:#fff;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;transition:background .18s}
.btn-cta-danger:hover{background:#a31209}
.btn-cta-warn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:11px;border:none;cursor:pointer;background:rgba(249,178,51,.9);color:#7c2d12;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;transition:background .18s}
.btn-cta-warn:hover{background:#F9B233}
</style>