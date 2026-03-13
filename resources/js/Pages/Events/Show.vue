<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ event: Object, stats: Object });

const showCancelModal = ref(false);
const cancelForm = useForm({ cancellation_reason: '' });

const cancelEvent        = () => { showCancelModal.value = true; };
const submitCancellation = () => {
    cancelForm.post(route('events.cancel', props.event.id), {
        onSuccess: () => { showCancelModal.value = false; }
    });
};

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { weekday:'short', year:'numeric', month:'short', day:'numeric' })
    : 'Not set';

const formatTime = (t) => {
    if (!t) return '';
    try {
        if (t.includes('T') || t.includes(' ')) return new Date(t).toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' });
        const [h, m] = t.split(':');
        const d = new Date(); d.setHours(+h, +(m||0));
        return d.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' });
    } catch { return t; }
};

const getDaysUntilEvent = () => {
    if (!props.event.event_date) return 'No date set';
    const diff = Math.ceil((new Date(props.event.event_date) - new Date()) / 86400000);
    if (diff < 0)   return 'Event passed';
    if (diff === 0)  return 'Today!';
    if (diff === 1)  return 'Tomorrow';
    if (diff <= 7)   return `${diff} days`;
    if (diff <= 30)  return `${Math.ceil(diff/7)} weeks`;
    return `${Math.ceil(diff/30)} months`;
};

const getProgressPct = () => {
    const order = ['draft','planning','confirmed','ongoing','completed'];
    const i = order.indexOf(props.event.status);
    return i >= 0 ? (i / (order.length - 1)) * 100 : 0;
};

const getProgressText = () => {
    const p = getProgressPct();
    if (p === 0)  return 'Just started';
    if (p <= 33)  return 'Getting started';
    if (p <= 66)  return 'In progress';
    if (p < 100)  return 'Almost there';
    return 'Completed';
};

const getTimelineStatus = () => {
    const d = getDaysUntilEvent();
    if (d.includes('passed'))   return 'Overdue';
    if (d.includes('Today'))    return 'Today!';
    if (d.includes('Tomorrow')) return 'Tomorrow';
    const n = parseInt(d);
    if (!isNaN(n) && n <= 7)   return 'This week';
    if (!isNaN(n) && n <= 30)  return 'This month';
    return 'Upcoming';
};

const pct = (a, b) => b > 0 ? Math.round((a / b) * 100) : 0;

const STATUS = {
    draft:     { label:'Draft',     color:'#9E9890', bg:'rgba(158,152,144,.14)', grad:'linear-gradient(135deg,#9E9890,#6B6560)' },
    planning:  { label:'Planning',  color:'#1D5C96', bg:'rgba(29,92,150,.1)',    grad:'linear-gradient(135deg,#1D5C96,#0d3a6e)' },
    confirmed: { label:'Confirmed', color:'#16a34a', bg:'rgba(22,163,74,.1)',    grad:'linear-gradient(135deg,#16a34a,#14532d)' },
    ongoing:   { label:'Live Now',  color:'#F05A00', bg:'rgba(240,90,0,.1)',     grad:'linear-gradient(135deg,#F9B233,#F05A00)' },
    completed: { label:'Wrapped',   color:'#C0170F', bg:'rgba(192,23,15,.08)',   grad:'linear-gradient(135deg,#C0170F,#8B0000)' },
    cancelled: { label:'Cancelled', color:'#6B6560', bg:'rgba(107,101,96,.12)',  grad:'linear-gradient(135deg,#9E9890,#6B6560)' },
};
const gs = (s) => STATUS[s] || STATUS.draft;

const BOOKING_STATUS = {
    pending:   { bg:'rgba(249,178,51,.15)', color:'#b45309' },
    confirmed: { bg:'rgba(22,163,74,.1)',   color:'#16a34a' },
    cancelled: { bg:'rgba(192,23,15,.08)',  color:'#C0170F' },
    completed: { bg:'rgba(29,92,150,.1)',   color:'#1D5C96' },
};
const gbs = (s) => BOOKING_STATUS[s] || { bg:'rgba(158,152,144,.14)', color:'#6B6560' };

const GUEST_GRADS = [
    'linear-gradient(135deg,#C0170F,#8B0000)',
    'linear-gradient(135deg,#1D5C96,#0d3a6e)',
    'linear-gradient(135deg,#F9B233,#F05A00)',
    'linear-gradient(135deg,#16a34a,#14532d)',
];

const STEPS = ['draft','planning','confirmed','ongoing'];
const stepState = (step) => {
    const order = ['draft','planning','confirmed','ongoing','completed'];
    const cur = order.indexOf(props.event.status);
    const si  = order.indexOf(step);
    if (cur > si)   return 'done';
    if (cur === si)  return 'active';
    return '';
};
</script>

<template>
    <AuthenticatedLayout>

        <!-- Confetti dots -->
        <div class="confetti" aria-hidden="true">
            <div class="cdot" v-for="n in 16" :key="n" :style="{
                width: (3+(n*3)%9)+'px',
                height: (3+(n*3)%9)+'px',
                left: (n*6.1%100)+'%',
                background: ['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233'][n%6],
                animationDuration: (9+n*1.2)+'s',
                animationDelay: (n*.55)+'s',
                borderRadius: n%3===0 ? '2px' : '50%',
            }"></div>
        </div>

        <div class="page-wrap">

            <!-- ─── HERO ─── -->
            <div class="hero">
                <div class="hero-grad" style="background:linear-gradient(135deg,#C0170F 0%,#8B0000 60%,#5a0000 100%)"></div>
                <div class="hero-noise"></div>
                <div class="hero-overlay"></div>
                <div class="hero-sparkle" v-for="s in 10" :key="s" :style="{
                    left: (s*9+3)+'%',
                    top: (s%4*22+8)+'%',
                    width: s%2===0 ? '5px':'3px',
                    height: s%2===0 ? '5px':'3px',
                    animationDuration: (1.3+s*.3)+'s',
                    animationDelay: (s*.2)+'s',
                }"></div>
                <div class="hero-inner">
                    <Link :href="route('events.index')" class="back-link">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                        Back to Events
                    </Link>
                    <div class="hero-bottom">
                        <div>
                            <div class="hero-pills">
                                <span v-if="event.event_type" class="hero-pill">🏷️ {{ event.event_type.name }}</span>
                                <span class="hero-pill">📅 {{ formatDate(event.event_date) }}</span>
                                <span v-if="event.venue_name" class="hero-pill">📍 {{ event.venue_name }}</span>
                            </div>
                            <h1 class="hero-title">{{ event.title }}</h1>
                            <div class="hero-byline">Created by {{ event.user?.name }} · {{ formatDate(event.created_at) }}</div>
                        </div>
                        <div :class="['status-badge', event.status==='ongoing' ? 'live' : '']"
                             :style="{ color: gs(event.status).color }">
                            <span class="status-dot"></span>
                            {{ gs(event.status).label }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── STAT CARDS ─── -->
            <div class="stats">

                <!-- Guests -->
                <div class="stat">
                    <div class="stat-glow" style="background:#1D5C96"></div>
                    <span class="stat-emoji">🎟️</span>
                    <div class="stat-label">Guests</div>
                    <div class="stat-main">
                        <span class="stat-val">{{ stats.confirmed_guests||0 }}</span>
                        <span class="stat-sub">/ {{ stats.total_guests||0 }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" :style="{ width: pct(stats.confirmed_guests,stats.total_guests)+'%', background:'linear-gradient(90deg,#1D5C96,#0d3a6e)' }"></div>
                    </div>
                    <div class="stat-foot">
                        <span class="stat-detail">
                            <span class="dot6" style="background:#1D5C96"></span>
                            {{ pct(stats.confirmed_guests,stats.total_guests) }}% confirmed
                        </span>
                        <span class="stag" style="background:rgba(29,92,150,.1);color:#1D5C96">
                            {{ (stats.total_guests||0)-(stats.confirmed_guests||0) }} pending
                        </span>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="stat">
                    <span class="stat-emoji">✅</span>
                    <div class="stat-label">Tasks</div>
                    <div class="stat-main">
                        <span class="stat-val">{{ stats.completed_tasks||0 }}</span>
                        <span class="stat-sub">/ {{ stats.total_tasks||0 }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" :style="{ width: pct(stats.completed_tasks,stats.total_tasks)+'%', background:'linear-gradient(90deg,#16a34a,#14532d)' }"></div>
                    </div>
                    <div class="stat-foot">
                        <span class="stat-detail">
                            <span class="dot6" style="background:#16a34a"></span>
                            {{ (stats.total_tasks||0)-(stats.completed_tasks||0) }} remaining
                        </span>
                        <span class="stag"
                              :style="{ background:(stats.overdue_tasks||0)>0?'rgba(192,23,15,.1)':'rgba(22,163,74,.1)', color:(stats.overdue_tasks||0)>0?'#C0170F':'#16a34a' }">
                            {{ stats.overdue_tasks||0 }} overdue
                        </span>
                    </div>
                </div>

                <!-- Budget -->
                <div class="stat">
                    <span class="stat-emoji">💰</span>
                    <div class="stat-label">Budget</div>
                    <div style="margin-bottom:8px">
                        <span class="stat-val" style="font-size:20px;font-family:'Playfair Display',serif;font-weight:900;color:#1A1410;line-height:1">
                            TZS {{ event.estimated_budget?.toLocaleString()||'0' }}
                        </span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" :style="{
                            width: Math.min(stats.budget_used_percentage||0,100)+'%',
                            background: (stats.budget_used_percentage||0)>90
                                ? 'linear-gradient(90deg,#C0170F,#8B0000)'
                                : (stats.budget_used_percentage||0)>70
                                    ? 'linear-gradient(90deg,#F9B233,#F05A00)'
                                    : 'linear-gradient(90deg,#16a34a,#14532d)'
                        }"></div>
                    </div>
                    <div class="stat-foot">
                        <span class="stat-detail">{{ (stats.budget_used_percentage||0).toFixed(1) }}% used</span>
                        <span class="stag" :style="{
                            background: (stats.budget_used_percentage||0)>90 ? 'rgba(192,23,15,.1)' : (stats.budget_used_percentage||0)>70 ? 'rgba(249,178,51,.15)' : 'rgba(22,163,74,.1)',
                            color:      (stats.budget_used_percentage||0)>90 ? '#C0170F'           : (stats.budget_used_percentage||0)>70 ? '#b45309'              : '#16a34a'
                        }">{{ (stats.budget_used_percentage||0)>90 ? 'Over limit' : (stats.budget_used_percentage||0)>70 ? 'Near limit' : 'On track' }}</span>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="stat">
                    <span class="stat-emoji">⏳</span>
                    <div class="stat-label">Timeline</div>
                    <div style="margin-bottom:8px">
                        <span class="stat-val" style="font-size:22px;font-family:'Playfair Display',serif;font-weight:900;color:#1A1410;line-height:1">
                            {{ getDaysUntilEvent() }}
                        </span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" :style="{ width: getProgressPct()+'%', background:'linear-gradient(90deg,#F9B233,#F05A00)' }"></div>
                    </div>
                    <div class="stat-foot">
                        <span class="stat-detail">{{ getProgressText() }}</span>
                        <span class="stag" style="background:rgba(240,90,0,.1);color:#F05A00">{{ getTimelineStatus() }}</span>
                    </div>
                </div>

            </div>

            <!-- ─── TWO-COL LAYOUT ─── -->
            <div class="layout">

                <!-- Left -->
                <div>

                    <!-- Event Info card -->
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title-row">
                                <div class="card-icon" style="background:rgba(192,23,15,.1)">🎪</div>
                                <span class="card-heading">Event Information</span>
                            </div>
                            <Link :href="route('events.edit', event.id)" class="card-link">
                                Edit
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </Link>
                        </div>
                        <div class="card-body">
                            <div v-if="event.description" class="desc-box">{{ event.description }}</div>
                            <div class="info-grid">
                                <div>
                                    <div class="section-lbl">
                                        <span class="section-dot" style="background:#C0170F"></span>
                                        Date &amp; Time
                                    </div>
                                    <div class="info-row">
                                        <span class="info-lbl">Start</span>
                                        <span class="info-val">{{ formatDate(event.event_date) }} {{ formatTime(event.start_time) }}</span>
                                    </div>
                                    <div v-if="event.event_end_date" class="info-row">
                                        <span class="info-lbl">End</span>
                                        <span class="info-val">{{ formatDate(event.event_end_date) }} {{ formatTime(event.end_time) }}</span>
                                    </div>
                                    <div style="margin-top:18px">
                                        <div class="section-lbl">
                                            <span class="section-dot" style="background:#1D5C96"></span>
                                            Location
                                        </div>
                                        <div v-if="event.venue_name" class="info-row">
                                            <span class="info-lbl">Venue</span>
                                            <span class="info-val">{{ event.venue_name }}</span>
                                        </div>
                                        <div v-if="event.venue_address" class="info-row">
                                            <span class="info-lbl">Address</span>
                                            <span class="info-val">{{ event.venue_address }}</span>
                                        </div>
                                        <div v-if="event.city" class="info-row">
                                            <span class="info-lbl">City</span>
                                            <span class="info-val">{{ event.city }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="section-lbl">
                                        <span class="section-dot" style="background:#F9B233"></span>
                                        Budget &amp; Capacity
                                    </div>
                                    <div v-if="event.estimated_budget" class="info-row">
                                        <span class="info-lbl">Budget</span>
                                        <span class="info-val">TZS {{ event.estimated_budget.toLocaleString() }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-lbl">Expected</span>
                                        <span class="info-val">{{ event.expected_guests }} guests</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-lbl">Visibility</span>
                                        <span class="info-val">{{ event.is_public ? '🌐 Public' : '🔒 Private' }}</span>
                                    </div>
                                    <div v-if="event.notes" style="margin-top:18px">
                                        <div class="section-lbl">
                                            <span class="section-dot" style="background:#9E9890"></span>
                                            Notes
                                        </div>
                                        <p style="font-size:13px;color:#6B6560;line-height:1.65;white-space:pre-line">{{ event.notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks card -->
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title-row">
                                <div class="card-icon" style="background:rgba(22,163,74,.1)">📋</div>
                                <span class="card-heading">Tasks</span>
                                <span class="card-count">{{ event.tasks?.length||0 }}</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px">
                                <Link :href="route('events.tasks.index', event.id)" class="card-link">
                                    View all
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </Link>
                                <Link :href="route('events.tasks.create', event.id)" class="btn-cta" style="padding:6px 14px;font-size:11px">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                    Add Task
                                </Link>
                            </div>
                        </div>
                        <div class="card-body">
                            <template v-if="event.tasks && event.tasks.length > 0">
                                <div v-for="task in event.tasks.slice(0,5)" :key="task.id" class="task">
                                    <div :class="['task-check', task.completed ? 'done' : '']">
                                        <svg v-if="task.completed" width="10" height="10" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:3px">
                                            <span :class="['task-title', task.completed ? 'done' : '']">{{ task.title }}</span>
                                            <span class="priority-badge" :style="{
                                                background: task.priority==='high' ? 'rgba(192,23,15,.1)' : task.priority==='medium' ? 'rgba(249,178,51,.15)' : 'rgba(22,163,74,.1)',
                                                color:      task.priority==='high' ? '#C0170F'           : task.priority==='medium' ? '#b45309'              : '#16a34a'
                                            }">{{ task.priority }}</span>
                                        </div>
                                        <p v-if="task.description" class="task-desc">{{ task.description }}</p>
                                        <div class="task-meta">
                                            <span v-if="task.due_date" class="task-meta-item">📅 {{ formatDate(task.due_date) }}</span>
                                            <span v-if="task.assigned_to_user" class="task-meta-item">👤 {{ task.assigned_to_user.name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="event.tasks.length > 5" style="padding-top:12px;border-top:1px solid #E8E2DA;text-align:center;margin-top:4px">
                                    <Link :href="route('events.tasks.index', event.id)" class="card-link" style="font-size:13px">
                                        Show {{ event.tasks.length-5 }} more tasks ↓
                                    </Link>
                                </div>
                            </template>
                            <div v-else class="empty-state">
                                <span class="empty-emoji">📝</span>
                                <div class="empty-title">No tasks yet</div>
                                <p class="empty-sub">Start organising your event by adding tasks</p>
                                <Link :href="route('events.tasks.create', event.id)" class="btn-cta">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                    Create First Task
                                </Link>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div>

                    <!-- Quick Actions -->
                    <div class="card" style="margin-bottom:20px">
                        <div class="card-head">
                            <div class="card-title-row">
                                <div class="card-icon" style="background:rgba(249,178,51,.15)">⚡</div>
                                <span class="card-heading">Quick Actions</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <Link :href="route('events.edit', event.id)" class="action-item">
                                <div class="action-left">
                                    <div class="action-icon" style="background:rgba(29,92,150,.1)">✏️</div>
                                    <div>
                                        <div class="action-name">Edit Event</div>
                                        <div class="action-sub">Update details</div>
                                    </div>
                                </div>
                                <svg width="14" height="14" fill="none" stroke="#9E9890" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                            </Link>
                            <Link :href="route('events.guests.index', event.id)" class="action-item">
                                <div class="action-left">
                                    <div class="action-icon" style="background:rgba(22,163,74,.1)">🎊</div>
                                    <div>
                                        <div class="action-name">Manage Guests</div>
                                        <div class="action-sub">Invite &amp; track RSVPs</div>
                                    </div>
                                </div>
                                <svg width="14" height="14" fill="none" stroke="#9E9890" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                            </Link>
                            <Link :href="route('events.tasks.create', event.id)" class="action-item">
                                <div class="action-left">
                                    <div class="action-icon" style="background:rgba(192,23,15,.08)">📋</div>
                                    <div>
                                        <div class="action-name">Add Task</div>
                                        <div class="action-sub">Create a new task</div>
                                    </div>
                                </div>
                                <svg width="14" height="14" fill="none" stroke="#9E9890" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                            </Link>
                            <button v-if="event.status !== 'cancelled'" @click="cancelEvent" class="action-item action-danger">
                                <div class="action-left">
                                    <div class="action-icon" style="background:rgba(192,23,15,.08)">🚫</div>
                                    <div>
                                        <div class="action-name" style="color:#C0170F">Cancel Event</div>
                                        <div class="action-sub">Cannot be undone</div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Recent Guests -->
                    <div class="card" style="margin-bottom:20px">
                        <div class="card-head">
                            <div class="card-title-row">
                                <div class="card-icon" style="background:rgba(249,178,51,.15)">🎉</div>
                                <span class="card-heading">Recent Guests</span>
                            </div>
                            <Link :href="route('events.guests.index', event.id)" class="card-link">
                                View all
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </Link>
                        </div>
                        <div class="card-body">
                            <template v-if="event.bookings && event.bookings.length > 0">
                                <div v-for="(booking, bi) in event.bookings.slice(0,4)" :key="booking.id" class="guest-row">
                                    <div class="guest-av" :style="{ background: GUEST_GRADS[bi%4] }">
                                        {{ booking.guest_name?.charAt(0).toUpperCase() || '🎊' }}
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <div class="guest-name">{{ booking.guest_name || 'Guest' }}</div>
                                        <div class="guest-email">{{ booking.guest_email }}</div>
                                    </div>
                                    <span class="booking-status" :style="{ background: gbs(booking.status).bg, color: gbs(booking.status).color }">
                                        {{ booking.status }}
                                    </span>
                                </div>
                                <div v-if="event.bookings.length > 4" style="padding-top:10px;border-top:1px solid #E8E2DA;text-align:center">
                                    <Link :href="route('events.bookings.index', event.id)" class="card-link" style="font-size:12px">
                                        {{ event.bookings.length-4 }} more guests ↓
                                    </Link>
                                </div>
                            </template>
                            <div v-else class="empty-state">
                                <span class="empty-emoji">🎈</span>
                                <div class="empty-title">No guests yet</div>
                                <p class="empty-sub">Start inviting people to your event</p>
                                <Link :href="route('events.bookings.create', event.id)" class="btn-cta">Invite First Guest</Link>
                            </div>
                        </div>
                    </div>

                    <!-- Event Journey -->
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title-row">
                                <div class="card-icon" style="background:rgba(192,23,15,.08)">🗺️</div>
                                <span class="card-heading">Event Journey</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                                <span style="font-size:13px;color:#6B6560">Current Stage</span>
                                <span style="font-family:'DM Mono',monospace;font-size:11px;font-weight:700;padding:4px 10px;border-radius:7px"
                                      :style="{ background: gs(event.status).bg, color: gs(event.status).color }">
                                    {{ gs(event.status).label }}
                                </span>
                            </div>
                            <div style="background:#F0EDE8;border-radius:8px;height:8px;overflow:hidden;margin-bottom:14px">
                                <div style="height:100%;border-radius:8px;background:linear-gradient(90deg,#C0170F,#F05A00);transition:width .6s"
                                     :style="{ width: getProgressPct()+'%' }"></div>
                            </div>
                            <div class="timeline-steps">
                                <div v-for="step in STEPS" :key="step" :class="['tstep', stepState(step)]">
                                    <div class="tstep-dot"></div>
                                    <div class="tstep-label">{{ step }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ─── CANCEL MODAL ─── -->
        <Modal :show="showCancelModal" @close="showCancelModal = false">
            <div class="modal-inner">
                <div class="modal-head">
                    <div class="modal-icon">🚫</div>
                    <div>
                        <div class="modal-title">Cancel Event</div>
                        <div class="modal-sub">This action cannot be undone</div>
                    </div>
                </div>
                <p class="modal-body-text">
                    Are you sure you want to cancel <strong>{{ event.title }}</strong>?
                    Please provide a reason so guests can be notified.
                </p>
                <form @submit.prevent="submitCancellation">
                    <label class="modal-lbl">Cancellation Reason *</label>
                    <textarea v-model="cancelForm.cancellation_reason"
                              class="modal-textarea"
                              placeholder="Explain why this event is being cancelled…"
                              required></textarea>
                    <div class="modal-foot">
                        <button type="button" @click="showCancelModal = false" class="btn-ghost">Keep Event</button>
                        <button type="submit" :disabled="cancelForm.processing" class="btn-danger">
                            <span v-if="cancelForm.processing">Cancelling…</span>
                            <span v-else>Confirm Cancellation</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

<style scoped>
/* ── fonts ── */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

/* ── confetti ── */
.confetti { position:fixed; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
.cdot     { position:absolute; opacity:0; animation:rise linear infinite; }
@keyframes rise {
    0%   { transform:translateY(110vh) rotate(0deg);   opacity:0; }
    5%   { opacity:.4; }
    95%  { opacity:.2; }
    100% { transform:translateY(-80px) rotate(540deg); opacity:0; }
}

/* ── page wrapper ── */
.page-wrap {
    position:relative; z-index:1;
    background:#F7F5F2;
    min-height:100vh;
    padding:28px 24px 64px;
    font-family:'DM Sans',sans-serif;
    color:#1A1410;
}

/* ── hero ── */
.hero         { border-radius:24px; overflow:hidden; position:relative; margin-bottom:24px; box-shadow:0 12px 40px rgba(0,0,0,.13); min-height:220px; }
.hero-grad    { position:absolute; inset:0; }
.hero-noise   { position:absolute; inset:0; opacity:.08; background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }
.hero-overlay { position:absolute; inset:0; background:linear-gradient(to bottom,transparent 20%,rgba(0,0,0,.6) 100%); }
.hero-sparkle { position:absolute; border-radius:50%; background:rgba(255,255,255,.85); animation:twinkle ease-in-out infinite; }
@keyframes twinkle { 0%,100%{opacity:0;transform:scale(0);} 50%{opacity:1;transform:scale(1);} }
.hero-inner   { position:relative; z-index:1; padding:24px 28px 22px; display:flex; flex-direction:column; justify-content:space-between; min-height:220px; }
.back-link    { display:inline-flex; align-items:center; gap:6px; color:rgba(255,255,255,.8); font-size:11px; font-family:'DM Mono',monospace; letter-spacing:.1em; text-decoration:none; transition:all .18s; }
.back-link:hover { color:#fff; transform:translateX(-3px); }
.hero-bottom  { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap; }
.hero-pills   { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
.hero-pill    { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:20px; background:rgba(255,255,255,.15); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.2); font-size:11px; color:rgba(255,255,255,.9); font-family:'DM Mono',monospace; }
.hero-title   { font-family:'Playfair Display',serif; font-size:clamp(22px,3.5vw,36px); font-weight:900; color:#fff; line-height:1.1; margin-bottom:5px; text-shadow:0 2px 12px rgba(0,0,0,.3); }
.hero-byline  { font-size:11px; color:rgba(255,255,255,.6); font-family:'DM Mono',monospace; }
.status-badge { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:20px; background:rgba(0,0,0,.35); backdrop-filter:blur(8px); font-family:'DM Mono',monospace; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#fff; white-space:nowrap; }
.status-dot   { width:7px; height:7px; border-radius:50%; background:currentColor; flex-shrink:0; }
.status-badge.live .status-dot { animation:blink .8s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.1;} }

/* ── stat cards ── */
.stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
@media(max-width:900px){ .stats{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:500px){ .stats{ grid-template-columns:1fr; } }

.stat        { background:#fff; border:1px solid #E8E2DA; border-radius:20px; padding:18px 20px; box-shadow:0 2px 20px rgba(0,0,0,.07); transition:transform .22s,box-shadow .22s; position:relative; overflow:hidden; }
.stat:hover  { transform:translateY(-3px); box-shadow:0 12px 40px rgba(0,0,0,.13); }
.stat-glow   { position:absolute; top:-20px; right:-20px; width:80px; height:80px; border-radius:50%; opacity:.1; pointer-events:none; }
.stat-emoji  { font-size:22px; margin-bottom:10px; display:block; }
.stat-label  { font-family:'DM Mono',monospace; font-size:9px; letter-spacing:.18em; color:#9E9890; text-transform:uppercase; margin-bottom:8px; }
.stat-val    { font-family:'Playfair Display',serif; font-size:32px; font-weight:900; color:#1A1410; line-height:1; }
.stat-sub    { font-size:13px; color:#9E9890; margin-left:6px; }
.stat-main   { display:flex; align-items:baseline; margin-bottom:10px; }
.progress    { height:5px; border-radius:3px; background:#F0EDE8; overflow:hidden; margin-bottom:8px; }
.progress-bar{ height:100%; border-radius:3px; transition:width .6s cubic-bezier(.4,0,.2,1); }
.stat-foot   { display:flex; justify-content:space-between; align-items:center; }
.stat-detail { font-size:11px; color:#9E9890; display:flex; align-items:center; gap:5px; }
.dot6        { width:6px; height:6px; border-radius:50%; display:inline-block; }
.stag        { display:inline-block; padding:2px 8px; border-radius:5px; font-size:10px; font-family:'DM Mono',monospace; font-weight:700; }

/* ── 2-col layout ── */
.layout { display:grid; grid-template-columns:1fr 320px; gap:20px; }
@media(max-width:1024px){ .layout{ grid-template-columns:1fr; } }

/* ── cards ── */
.card       { background:#fff; border:1px solid #E8E2DA; border-radius:20px; box-shadow:0 2px 20px rgba(0,0,0,.07); overflow:hidden; margin-bottom:20px; }
.card:last-child { margin-bottom:0; }
.card-head  { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #E8E2DA; background:#F0EDE8; }
.card-title-row { display:flex; align-items:center; gap:10px; }
.card-icon  { width:32px; height:32px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
.card-heading { font-family:'Playfair Display',serif; font-size:16px; font-weight:700; color:#1A1410; }
.card-count { font-family:'DM Mono',monospace; font-size:10px; font-weight:700; padding:3px 9px; border-radius:6px; background:#E8E2DA; color:#6B6560; }
.card-link  { font-size:12px; font-weight:700; font-family:'DM Mono',monospace; color:#C0170F; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:gap .18s; }
.card-link:hover { gap:7px; }
.card-body  { padding:20px; }

/* ── info grid ── */
.info-grid  { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
@media(max-width:640px){ .info-grid{ grid-template-columns:1fr; } }
.section-lbl { display:flex; align-items:center; gap:7px; font-family:'DM Mono',monospace; font-size:9px; letter-spacing:.18em; color:#9E9890; text-transform:uppercase; margin-bottom:10px; }
.section-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.desc-box   { font-size:13px; color:#6B6560; line-height:1.7; margin-bottom:20px; padding:14px 16px; background:#F0EDE8; border-radius:12px; border-left:3px solid #C0170F; white-space:pre-line; }
.info-row   { display:flex; gap:10px; margin-bottom:9px; font-size:13px; }
.info-lbl   { color:#9E9890; flex-shrink:0; min-width:80px; font-family:'DM Mono',monospace; font-size:10px; padding-top:1px; }
.info-val   { color:#1A1410; font-weight:600; }

/* ── tasks ── */
.task       { display:flex; align-items:flex-start; gap:12px; padding:14px 16px; border:1.5px solid #E8E2DA; border-radius:14px; margin-bottom:10px; transition:all .18s; }
.task:last-child { margin-bottom:0; }
.task:hover { border-color:rgba(192,23,15,.25); background:rgba(192,23,15,.02); }
.task-check { width:18px; height:18px; border-radius:5px; flex-shrink:0; margin-top:2px; border:2px solid #E8E2DA; display:flex; align-items:center; justify-content:center; }
.task-check.done { background:#C0170F; border-color:#C0170F; }
.task-title { font-size:14px; font-weight:600; color:#1A1410; margin-bottom:3px; }
.task-title.done { text-decoration:line-through; color:#9E9890; }
.task-desc  { font-size:12px; color:#6B6560; margin-bottom:7px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.task-meta  { display:flex; flex-wrap:wrap; gap:10px; }
.task-meta-item { display:flex; align-items:center; gap:4px; font-size:11px; color:#9E9890; font-family:'DM Mono',monospace; }
.priority-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:5px; font-size:10px; font-family:'DM Mono',monospace; font-weight:700; }

/* ── action items ── */
.action-item  { display:flex; align-items:center; justify-content:space-between; padding:13px 14px; border:1.5px solid #E8E2DA; border-radius:14px; text-decoration:none; margin-bottom:8px; transition:all .2s; cursor:pointer; background:#fff; width:100%; font-family:'DM Sans',sans-serif; }
.action-item:last-child { margin-bottom:0; }
.action-item:hover  { transform:translateX(3px); box-shadow:0 4px 14px rgba(0,0,0,.08); }
.action-danger      { border-color:rgba(192,23,15,.2); }
.action-danger:hover{ background:rgba(192,23,15,.04); border-color:rgba(192,23,15,.35); }
.action-left  { display:flex; align-items:center; gap:12px; }
.action-icon  { width:36px; height:36px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.action-name  { font-size:14px; font-weight:600; color:#1A1410; text-align:left; }
.action-sub   { font-size:11px; color:#9E9890; font-family:'DM Mono',monospace; margin-top:1px; }

/* ── guests ── */
.guest-row   { display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:12px; margin-bottom:8px; transition:background .18s; }
.guest-row:hover { background:#F0EDE8; }
.guest-av    { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:900; color:#fff; flex-shrink:0; }
.guest-name  { font-size:13px; font-weight:600; color:#1A1410; }
.guest-email { font-size:11px; color:#9E9890; font-family:'DM Mono',monospace; }
.booking-status { padding:3px 9px; border-radius:6px; font-size:10px; font-family:'DM Mono',monospace; font-weight:700; white-space:nowrap; }

/* ── timeline steps ── */
.timeline-steps { display:grid; grid-template-columns:repeat(4,1fr); margin-top:14px; }
.tstep          { text-align:center; position:relative; }
.tstep::before  { content:''; position:absolute; top:7px; left:50%; right:-50%; height:2px; background:#E8E2DA; z-index:0; }
.tstep:last-child::before { display:none; }
.tstep.done::before { background:#C0170F; }
.tstep-dot      { width:16px; height:16px; border-radius:50%; border:2.5px solid #E8E2DA; background:#fff; margin:0 auto 6px; position:relative; z-index:1; }
.tstep.done  .tstep-dot  { background:#C0170F; border-color:#C0170F; }
.tstep.active .tstep-dot { background:#C0170F; border-color:#C0170F; box-shadow:0 0 0 4px rgba(192,23,15,.2); animation:blink 1.2s ease-in-out infinite; }
.tstep-label    { font-family:'DM Mono',monospace; font-size:9px; color:#9E9890; text-transform:uppercase; letter-spacing:.1em; }
.tstep.done  .tstep-label,
.tstep.active .tstep-label { color:#C0170F; font-weight:700; }

/* ── CTA button ── */
.btn-cta     { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:10px; border:none; cursor:pointer; background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%); background-size:200% auto; color:#fff; font-size:12px; font-weight:700; font-family:'DM Sans',sans-serif; text-decoration:none; box-shadow:0 4px 14px rgba(192,23,15,.3); animation:shine 3s linear infinite; transition:transform .2s,box-shadow .2s; }
@keyframes shine { 0%{background-position:0% center;} 100%{background-position:200% center;} }
.btn-cta:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(192,23,15,.4); }

.btn-ghost     { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:9px; border:1.5px solid #E8E2DA; background:#fff; color:#6B6560; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; transition:all .18s; }
.btn-ghost:hover { border-color:#9E9890; color:#1A1410; }

.btn-danger    { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border-radius:10px; border:none; cursor:pointer; background:linear-gradient(135deg,#C0170F,#8B0000); color:#fff; font-size:13px; font-weight:700; font-family:'DM Sans',sans-serif; box-shadow:0 4px 14px rgba(192,23,15,.3); transition:all .2s; }
.btn-danger:hover    { transform:translateY(-1px); box-shadow:0 6px 20px rgba(192,23,15,.4); }
.btn-danger:disabled { opacity:.55; cursor:not-allowed; transform:none; }

/* ── empty state ── */
.empty-state { text-align:center; padding:36px 20px; }
.empty-emoji { font-size:36px; display:block; margin-bottom:10px; }
.empty-title { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:#1A1410; margin-bottom:4px; }
.empty-sub   { font-size:12px; color:#9E9890; margin-bottom:16px; }

/* ── modal ── */
.modal-inner     { padding:28px; font-family:'DM Sans',sans-serif; }
.modal-head      { display:flex; align-items:center; gap:14px; margin-bottom:16px; }
.modal-icon      { width:48px; height:48px; border-radius:14px; background:rgba(192,23,15,.1); display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0; }
.modal-title     { font-family:'Playfair Display',serif; font-size:20px; font-weight:900; color:#1A1410; }
.modal-sub       { font-size:11px; color:#9E9890; font-family:'DM Mono',monospace; margin-top:2px; }
.modal-body-text { font-size:14px; color:#6B6560; margin-bottom:16px; line-height:1.6; }
.modal-lbl       { display:block; font-family:'DM Mono',monospace; font-size:10px; letter-spacing:.14em; color:#9E9890; text-transform:uppercase; margin-bottom:8px; }
.modal-textarea  { width:100%; padding:12px 14px; border:1.5px solid #E8E2DA; border-radius:12px; font-size:13px; font-family:'DM Sans',sans-serif; color:#1A1410; background:#F7F5F2; outline:none; resize:vertical; min-height:90px; transition:border-color .18s; }
.modal-textarea:focus { border-color:#C0170F; box-shadow:0 0 0 3px rgba(192,23,15,.1); }
.modal-foot      { display:flex; justify-content:flex-end; gap:10px; margin-top:18px; }
</style>