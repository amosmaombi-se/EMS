<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Swal from 'sweetalert2';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import { EyeIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';

const page = usePage();

const props = defineProps({
    users:   Object,
    search:  String,
    role:    String,
    summary: Object
});

const pageNumber = ref(1);
const searchTerm = ref(usePage().props.search ?? '');
const loading    = ref(true);
const roleFilter = ref(props.role || '');

const authUser      = computed(() => page.props.auth?.user);
const canManageUsers = computed(() => ['ADMIN', 'STAFF'].includes(authUser.value?.role));

const getInitials = (name) => name ? name.split(' ').map(p => p[0]).join('').toUpperCase().slice(0, 2) : '';

const formattedDate = (d) => {
    if (!d || isNaN(new Date(d))) return '—';
    return new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(new Date(d));
};

const getRoleBadge = (role) => ({
    ADMIN: { bg: 'rgba(192,23,15,.1)',  color: '#C0170F', dot: '#C0170F' },
    STAFF: { bg: 'rgba(249,178,51,.15)', color: '#b45309', dot: '#F9B233' },
    USER:  { bg: 'rgba(29,92,150,.1)',  color: '#1D5C96', dot: '#1D5C96' },
}[role] || { bg: 'rgba(158,152,144,.12)', color: '#6B6560', dot: '#9E9890' });

onMounted(() => setTimeout(() => { loading.value = false; }, 800));

const pageNumberUpdated = (link) => {
    pageNumber.value = link.url.split('=')[1];
    visitUrl();
};

const visitUrl = (opts = {}) => {
    const url = new URL(route('users.index'));
    url.searchParams.set('page', pageNumber.value);
    if (searchTerm.value.trim()) url.searchParams.set('search', searchTerm.value);
    if (roleFilter.value)        url.searchParams.set('role', roleFilter.value);
    router.visit(url, {
        replace: true, preserveState: true, preserveScroll: true,
        onStart:  () => { loading.value = true; },
        onFinish: () => setTimeout(() => { loading.value = false; }, 400),
        ...opts
    });
};

watch(searchTerm, () => { pageNumber.value = 1; visitUrl(); });
watch(roleFilter, () => { pageNumber.value = 1; visitUrl(); });

const confirmDelete = async (userId) => {
    const result = await Swal.fire({
        title: 'Delete this user?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#C0170F',
        cancelButtonColor: '#6B6560',
    });
    if (!result.isConfirmed) return;
    loading.value = true;
    router.delete(route('users.delete', { id: userId }), {
        onSuccess: () => Swal.fire({ title: 'Deleted!', icon: 'success', timer: 1800, showConfirmButton: false }),
        onError:   () => Swal.fire({ title: 'Error!', text: 'Something went wrong.', icon: 'error', timer: 1800, showConfirmButton: false }),
        onFinish:  () => setTimeout(() => { loading.value = false; }, 400)
    });
};

const avatarColor = (role) => ({
    ADMIN: 'linear-gradient(135deg, #C0170F, #8B0000)',
    STAFF: 'linear-gradient(135deg, #F9B233, #F05A00)',
    USER:  'linear-gradient(135deg, #1D5C96, #0d3a6e)',
}[role] || 'linear-gradient(135deg, #9E9890, #6B6560)');

const viewModal = ref({ open: false, user: null });
const openViewModal  = (user) => { viewModal.value = { open: true, user }; document.body.style.overflow = 'hidden'; };
const closeViewModal = () => { viewModal.value.open = false; document.body.style.overflow = ''; };
</script>

<template>
    <Head title="Users" />
    <AuthenticatedLayout>

        <component :is="'style'">
            @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

            .ep-users * { box-sizing: border-box; }

            .ep-users {
                --crimson: #C0170F;
                --orange:  #F05A00;
                --amber:   #F9B233;
                --navy:    #1D5C96;
                --bg:      #F7F5F2;
                --bg-card: #FFFFFF;
                --bg-muted:#F0EDE8;
                --border:  #E8E2DA;
                --text:    #1A1410;
                --text-2:  #6B6560;
                --text-3:  #9E9890;
                --shadow:  0 2px 16px rgba(0,0,0,.07);
                --shadow-lg: 0 8px 32px rgba(0,0,0,.12);
                font-family: 'DM Sans', sans-serif;
                min-height: 100vh;
                background: var(--bg);
                padding: 28px 24px 48px;
            }

            .ep-users.dark {
                --bg:      #0F0D0C;
                --bg-card: #1A1714;
                --bg-muted:#242018;
                --border:  #2E2925;
                --text:    #F0EDE8;
                --text-2:  #A09890;
                --text-3:  #605850;
            }

            /* Page header */
            .ep-page-header {
                display: flex; justify-content: space-between; align-items: flex-start;
                margin-bottom: 24px; gap: 16px;
            }
            .ep-page-eyebrow {
                font-family: 'DM Mono', monospace; font-size: 10px;
                letter-spacing: .22em; color: var(--crimson);
                text-transform: uppercase; margin-bottom: 4px;
            }
            .ep-page-title {
                font-family: 'Playfair Display', serif;
                font-size: 30px; font-weight: 900;
                color: var(--text); margin: 0 0 4px; line-height: 1.1;
            }
            .ep-page-sub { font-size: 14px; color: var(--text-2); margin: 0; }

            /* Add user button */
            .ep-btn-add {
                display: inline-flex; align-items: center; gap: 7px;
                padding: 10px 18px; border: none; border-radius: 12px;
                background: linear-gradient(135deg, var(--crimson), #8B0000);
                color: #fff; font-size: 13px; font-weight: 700;
                font-family: 'DM Sans', sans-serif;
                cursor: pointer; text-decoration: none;
                box-shadow: 0 4px 14px rgba(192,23,15,.3);
                transition: all .2s; white-space: nowrap;
                position: relative; overflow: hidden;
            }
            .ep-btn-add::after { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, var(--orange), var(--crimson)); opacity: 0; transition: opacity .2s; }
            .ep-btn-add:hover::after { opacity: 1; }
            .ep-btn-add:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(192,23,15,.4); }
            .ep-btn-add > * { position: relative; z-index: 1; }

            /* Stat cards */
            .ep-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
            @media (max-width: 900px) { .ep-stats { grid-template-columns: repeat(2, 1fr); } }
            @media (max-width: 500px) { .ep-stats { grid-template-columns: 1fr; } }

            .ep-stat-card {
                border-radius: 18px; padding: 18px 20px;
                position: relative; overflow: hidden;
                box-shadow: var(--shadow);
                transition: transform .2s, box-shadow .2s;
            }
            .ep-stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
            .ep-stat-ghost {
                position: absolute; top: -14px; right: -12px;
                font-size: 64px; opacity: .1; pointer-events: none; line-height: 1;
            }
            .ep-stat-label {
                font-size: 9px; font-family: 'DM Mono', monospace;
                letter-spacing: .18em; color: rgba(255,255,255,.7);
                text-transform: uppercase; margin-bottom: 10px;
            }
            .ep-stat-value {
                font-family: 'Playfair Display', serif;
                font-size: 42px; font-weight: 900; color: #fff; line-height: 1;
            }

            /* Filter bar */
            .ep-filters {
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: 16px;
                padding: 16px 20px;
                margin-bottom: 20px;
                box-shadow: var(--shadow);
                display: grid; grid-template-columns: 1fr 220px; gap: 14px;
            }
            @media (max-width: 640px) { .ep-filters { grid-template-columns: 1fr; } }

            .ep-filter-label {
                font-family: 'DM Mono', monospace; font-size: 9px;
                letter-spacing: .16em; color: var(--text-3);
                text-transform: uppercase; margin-bottom: 5px;
            }
            .ep-filter-input-wrap { position: relative; }
            .ep-filter-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--text-3); pointer-events: none; }
            .ep-filter-input, .ep-filter-select {
                width: 100%; padding: 9px 12px 9px 36px;
                border: 1.5px solid var(--border); border-radius: 10px;
                font-size: 13px; font-family: 'DM Sans', sans-serif;
                color: var(--text); background: var(--bg);
                outline: none; transition: border-color .2s, box-shadow .2s;
            }
            .ep-filter-select { padding-left: 12px; }
            .ep-filter-input:focus, .ep-filter-select:focus {
                border-color: var(--crimson);
                box-shadow: 0 0 0 3px rgba(192,23,15,.1);
            }
            .ep-filter-input::placeholder { color: var(--text-3); }

            /* Table card */
            .ep-table-card {
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: 20px;
                overflow: hidden;
                box-shadow: var(--shadow);
            }

            /* Table header row */
            .ep-table-header {
                display: flex; align-items: center; justify-content: space-between;
                padding: 14px 22px;
                border-bottom: 1px solid var(--border);
                background: var(--bg-muted);
            }
            .ep-table-title {
                font-size: 10px; font-family: 'DM Mono', monospace;
                letter-spacing: .2em; color: var(--text-3);
                text-transform: uppercase;
            }
            .ep-count-badge {
                font-family: 'DM Mono', monospace; font-size: 10px;
                background: var(--bg-card); border: 1px solid var(--border);
                color: var(--text-2); border-radius: 20px; padding: 2px 10px;
            }

            /* Table */
            .ep-table { width: 100%; border-collapse: collapse; }
            .ep-table thead th {
                padding: 11px 18px; text-align: left;
                font-size: 10px; font-family: 'DM Mono', monospace;
                letter-spacing: .14em; color: var(--text-2); font-weight: 600;
                text-transform: uppercase;
                background: var(--bg-muted); border-bottom: 1px solid var(--border);
            }
            .ep-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
            .ep-table tbody tr:last-child { border-bottom: none; }
            .ep-table tbody tr:hover { background: var(--bg-muted); }
            .ep-table tbody td { padding: 14px 18px; font-size: 13.5px; color: var(--text-2); }

            /* Avatar */
            .ep-avatar {
                width: 34px; height: 34px; border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-size: 12px; font-weight: 700; color: #fff;
                flex-shrink: 0;
            }

            /* Role badge */
            .ep-role-badge {
                display: inline-flex; align-items: center; gap: 5px;
                padding: 4px 10px; border-radius: 6px;
                font-size: 11px; font-family: 'DM Mono', monospace;
                font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            }
            .ep-role-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }

            /* Action buttons */
            .ep-action {
                display: inline-flex; align-items: center; justify-content: center;
                width: 30px; height: 30px; border-radius: 8px;
                border: 1px solid var(--border);
                background: var(--bg); color: var(--text-3);
                cursor: pointer; text-decoration: none;
                transition: all .18s;
            }
            .ep-action:hover.view   { color: #16a34a; background: rgba(34,197,94,.08); border-color: rgba(34,197,94,.25); }
            .ep-action:hover.edit   { color: var(--navy); background: rgba(29,92,150,.08); border-color: rgba(29,92,150,.25); }
            .ep-action:hover.delete { color: var(--crimson); background: rgba(192,23,15,.08); border-color: rgba(192,23,15,.25); }

            /* Loading skeleton */
            @keyframes ep-pulse { 0%,100% { opacity:1; } 50% { opacity:.4; } }
            .ep-skeleton { animation: ep-pulse 1.6s ease-in-out infinite; }
            .ep-skel-line { background: var(--border); border-radius: 4px; }

            /* Empty state */
            .ep-empty {
                padding: 56px 24px; text-align: center;
                display: flex; flex-direction: column; align-items: center;
            }
            .ep-empty-icon {
                width: 56px; height: 56px; border-radius: 16px;
                background: var(--bg-muted); border: 1px solid var(--border);
                display: flex; align-items: center; justify-content: center;
                margin-bottom: 16px; color: var(--text-3);
            }
            .ep-empty-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
            .ep-empty-sub { font-size: 13px; color: var(--text-2); margin-bottom: 20px; }
            /* Modal */
            .ep-modal-backdrop {
                position: fixed; inset: 0; z-index: 9999;
                background: rgba(15,13,12,.55); backdrop-filter: blur(4px);
                display: flex; align-items: center; justify-content: center;
                padding: 20px;
            }
            .ep-modal {
                background: var(--bg-card); border: 1px solid var(--border);
                border-radius: 24px; width: 100%; max-width: 480px;
                box-shadow: 0 24px 64px rgba(0,0,0,.22);
                overflow: hidden; display: flex; flex-direction: column;
            }
            .ep-modal-header {
                display: flex; align-items: flex-start; justify-content: space-between;
                padding: 20px 22px 16px; border-bottom: 1px solid var(--border);
                background: var(--bg-muted);
            }
            .ep-modal-avatar {
                width: 44px; height: 44px; border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-size: 15px; font-weight: 700; color: #fff; flex-shrink: 0;
            }
            .ep-modal-name {
                font-family: 'Playfair Display', serif;
                font-size: 18px; font-weight: 700; color: var(--text);
                line-height: 1.2; margin-bottom: 5px;
            }
            .ep-modal-close {
                width: 32px; height: 32px; border-radius: 8px;
                border: 1px solid var(--border); background: var(--bg-card);
                color: var(--text-3); cursor: pointer; display: flex;
                align-items: center; justify-content: center;
                transition: all .18s; flex-shrink: 0; margin-top: 2px;
            }
            .ep-modal-close:hover { color: var(--crimson); border-color: rgba(192,23,15,.3); background: rgba(192,23,15,.05); }
            .ep-modal-body { padding: 18px 22px; overflow-y: auto; max-height: 65vh; }
            .ep-modal-section-label {
                font-family: 'DM Mono', monospace; font-size: 9px;
                letter-spacing: .2em; color: var(--text-3); text-transform: uppercase;
                margin-bottom: 10px;
            }
            .ep-modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
            .ep-modal-field {
                background: var(--bg-muted); border: 1px solid var(--border);
                border-radius: 10px; padding: 10px 12px;
            }
            .ep-modal-field-label {
                font-family: 'DM Mono', monospace; font-size: 9px;
                letter-spacing: .14em; color: var(--text-3);
                text-transform: uppercase; margin-bottom: 4px;
            }
            .ep-modal-field-value { font-size: 13px; font-weight: 600; color: var(--text); word-break: break-all; }
            .ep-modal-footer {
                display: flex; align-items: center; justify-content: flex-end; gap: 8px;
                padding: 14px 22px; border-top: 1px solid var(--border);
                background: var(--bg-muted);
            }
            .ep-modal-btn-ghost {
                padding: 9px 16px; border-radius: 10px; border: 1.5px solid var(--border);
                background: var(--bg-card); color: var(--text-2);
                font-size: 12px; font-weight: 600; font-family: 'DM Sans', sans-serif;
                cursor: pointer; transition: all .18s;
            }
            .ep-modal-btn-ghost:hover { border-color: var(--text-3); color: var(--text); }
            .ep-modal-btn-primary {
                display: inline-flex; align-items: center; gap: 6px;
                padding: 9px 18px; border-radius: 10px; border: none;
                background: linear-gradient(135deg, var(--crimson), #8B0000);
                color: #fff; font-size: 12px; font-weight: 700;
                font-family: 'DM Sans', sans-serif; cursor: pointer;
                text-decoration: none; box-shadow: 0 3px 12px rgba(192,23,15,.3);
                transition: all .2s;
            }
            .ep-modal-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(192,23,15,.4); }

            /* Modal transition */
            .ep-modal-enter-active, .ep-modal-leave-active { transition: opacity .2s ease; }
            .ep-modal-enter-active .ep-modal, .ep-modal-leave-active .ep-modal { transition: transform .2s ease, opacity .2s ease; }
            .ep-modal-enter-from, .ep-modal-leave-to { opacity: 0; }
            .ep-modal-enter-from .ep-modal { transform: scale(.95) translateY(10px); }
            .ep-modal-leave-to .ep-modal { transform: scale(.95) translateY(10px); opacity: 0; }
        </component>

        <div class="ep-users">

            <!-- Page header -->
            <div class="ep-page-header">
                <div>
                    <div class="ep-page-eyebrow">Administration</div>
                    <h1 class="ep-page-title">Users</h1>
                    <p class="ep-page-sub">Manage all registered users and their roles</p>
                </div>
                <Link :href="route('users.create')" class="ep-btn-add">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                    Add User
                </Link>
            </div>

            <!-- Stat cards -->
            <div class="ep-stats">
                <div class="ep-stat-card" :style="{ background: 'linear-gradient(135deg, #C0170F, #8B0000)' }">
                    <span class="ep-stat-ghost">👥</span>
                    <div class="ep-stat-label">Total Users</div>
                    <div class="ep-stat-value">{{ summary?.totalUsers || 0 }}</div>
                </div>
                <div class="ep-stat-card" :style="{ background: 'linear-gradient(135deg, #C0170F, #F05A00)' }">
                    <span class="ep-stat-ghost">🛡️</span>
                    <div class="ep-stat-label">Administrators</div>
                    <div class="ep-stat-value">{{ summary?.totalAdmins || 0 }}</div>
                </div>
                <div class="ep-stat-card" :style="{ background: 'linear-gradient(135deg, #F9B233, #F05A00)' }">
                    <span class="ep-stat-ghost">💼</span>
                    <div class="ep-stat-label">Staff Members</div>
                    <div class="ep-stat-value">{{ summary?.totalStaff || 0 }}</div>
                </div>
                <div class="ep-stat-card" :style="{ background: 'linear-gradient(135deg, #1D5C96, #0d3a6e)' }">
                    <span class="ep-stat-ghost">👤</span>
                    <div class="ep-stat-label">Regular Users</div>
                    <div class="ep-stat-value">{{ summary?.totalRegularUsers || 0 }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="ep-filters">
                <div>
                    <div class="ep-filter-label">Search</div>
                    <div class="ep-filter-input-wrap">
                        <svg class="ep-filter-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <input type="text" v-model="searchTerm" placeholder="Search by name or email…" class="ep-filter-input" />
                    </div>
                </div>
                <div>
                    <div class="ep-filter-label">Role</div>
                    <select v-model="roleFilter" class="ep-filter-select">
                        <option value="">All Roles</option>
                        <option value="ADMIN">Admin</option>
                        <option value="STAFF">Staff</option>
                        <option value="USER">User</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="ep-table-card">
                <div class="ep-table-header">
                    <span class="ep-table-title">📋 All Users</span>
                    <span class="ep-count-badge">{{ users?.data?.length || 0 }} shown</span>
                </div>

                <div style="overflow-x: auto">
                    <table class="ep-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th style="text-align:center">Actions</th>
                            </tr>
                        </thead>

                        <!-- Loading skeleton -->
                        <tbody v-if="loading">
                            <tr v-for="i in 6" :key="i" class="ep-skeleton">
                                <td><div class="ep-skel-line" style="width:20px;height:10px"></div></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="ep-skel-line" style="width:34px;height:34px;border-radius:50%"></div>
                                        <div>
                                            <div class="ep-skel-line" style="width:100px;height:10px;margin-bottom:5px"></div>
                                            <div class="ep-skel-line" style="width:60px;height:8px"></div>
                                        </div>
                                    </div>
                                </td>
                                <td><div class="ep-skel-line" style="width:130px;height:10px"></div></td>
                                <td><div class="ep-skel-line" style="width:90px;height:10px"></div></td>
                                <td><div class="ep-skel-line" style="width:60px;height:18px;border-radius:6px"></div></td>
                                <td><div class="ep-skel-line" style="width:80px;height:10px"></div></td>
                                <td>
                                    <div style="display:flex;justify-content:center;gap:6px">
                                        <div class="ep-skel-line" style="width:30px;height:30px;border-radius:8px"></div>
                                        <div class="ep-skel-line" style="width:30px;height:30px;border-radius:8px"></div>
                                        <div class="ep-skel-line" style="width:30px;height:30px;border-radius:8px"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                        <!-- Data -->
                        <tbody v-else>
                            <tr v-for="(user, index) in users.data" :key="user.id">
                                <td>
                                    <span :style="{ fontFamily:'DM Mono, monospace', fontSize:'12px', color:'var(--text-3)', fontWeight:500 }">{{ index + 1 }}</span>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="ep-avatar" :style="{ background: avatarColor(user.role) }">
                                            {{ getInitials(user.first_name) }}
                                        </div>
                                        <div>
                                            <div :style="{ fontWeight:700, fontSize:'14px', color:'var(--text)', fontFamily:'DM Sans, sans-serif' }">
                                                {{ user.first_name }} {{ user.last_name }}
                                            </div>
                                            <div :style="{ fontSize:'11px', color:'var(--text-3)', fontFamily:'DM Mono, monospace', marginTop:'1px' }">
                                                #{{ user.id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td :style="{ fontFamily:'DM Mono, monospace', fontSize:'12.5px', color:'var(--text-2)' }">{{ user.email }}</td>
                                <td :style="{ fontFamily:'DM Mono, monospace', fontSize:'12.5px', color:'var(--text-2)' }">{{ user.phone || '—' }}</td>
                                <td>
                                    <span class="ep-role-badge" :style="{ background: getRoleBadge(user.role).bg, color: getRoleBadge(user.role).color }">
                                        <span class="ep-role-dot" :style="{ background: getRoleBadge(user.role).dot }"></span>
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td :style="{ fontFamily:'DM Mono, monospace', fontSize:'12px', color:'var(--text-2)' }">{{ formattedDate(user.created_at) }}</td>
                                <td>
                                    <div style="display:flex;justify-content:center;gap:6px">
                                        <button @click="openViewModal(user)" class="ep-action view" title="View Details">
                                            <EyeIcon style="width:14px;height:14px" />
                                        </button>
                                        <Link :href="route('users.edit', { id: user.id })" class="ep-action edit" title="Edit User">
                                            <PencilIcon style="width:14px;height:14px" />
                                        </Link>
                                        <button @click="confirmDelete(user.id)" class="ep-action delete" title="Delete User">
                                            <TrashIcon style="width:14px;height:14px" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="users.data.length === 0">
                                <td colspan="7">
                                    <div class="ep-empty">
                                        <div class="ep-empty-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                        </div>
                                        <div class="ep-empty-title">No users found</div>
                                        <p class="ep-empty-sub">{{ searchTerm || roleFilter ? 'Try adjusting your filters' : 'No users in the system yet' }}</p>
                                        <Link :href="route('users.create')" class="ep-btn-add">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                            Add User
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="!loading && users?.data?.length > 0" style="margin-top:20px">
                <Pagination :data="users" :pageNumberUpdated="pageNumberUpdated" />
            </div>

        </div>

        <!-- View User Modal -->
        <Teleport to="body">
            <Transition name="ep-modal">
                <div v-if="viewModal.open" class="ep-modal-backdrop" @click.self="closeViewModal">
                    <div class="ep-modal">
                        <!-- Modal header -->
                        <div class="ep-modal-header">
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="ep-modal-avatar" :style="{ background: avatarColor(viewModal.user?.role) }">
                                    {{ getInitials(viewModal.user?.first_name) }}
                                </div>
                                <div>
                                    <div class="ep-modal-name">{{ viewModal.user?.first_name }} {{ viewModal.user?.last_name }}</div>
                                    <span class="ep-role-badge" :style="{ background: getRoleBadge(viewModal.user?.role).bg, color: getRoleBadge(viewModal.user?.role).color, fontSize:'10px' }">
                                        <span class="ep-role-dot" :style="{ background: getRoleBadge(viewModal.user?.role).dot }"></span>
                                        {{ viewModal.user?.role }}
                                    </span>
                                </div>
                            </div>
                            <button class="ep-modal-close" @click="closeViewModal">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="ep-modal-body">
                            <div class="ep-modal-section-label">Contact</div>
                            <div class="ep-modal-grid">
                                <div class="ep-modal-field">
                                    <div class="ep-modal-field-label">Email</div>
                                    <div class="ep-modal-field-value">{{ viewModal.user?.email || '—' }}</div>
                                </div>
                                <div class="ep-modal-field">
                                    <div class="ep-modal-field-label">Phone</div>
                                    <div class="ep-modal-field-value">{{ viewModal.user?.phone || '—' }}</div>
                                </div>
                            </div>

                            <div class="ep-modal-section-label" style="margin-top:16px">Profile</div>
                            <div class="ep-modal-grid">
                                <div class="ep-modal-field">
                                    <div class="ep-modal-field-label">Date of Birth</div>
                                    <div class="ep-modal-field-value">{{ formattedDate(viewModal.user?.date_of_birth) || '—' }}</div>
                                </div>
                                <div class="ep-modal-field">
                                    <div class="ep-modal-field-label">Gender</div>
                                    <div class="ep-modal-field-value" style="text-transform:capitalize">{{ viewModal.user?.gender || '—' }}</div>
                                </div>
                            </div>

                            <div class="ep-modal-section-label" style="margin-top:16px">Account</div>
                            <div class="ep-modal-grid">
                                <div class="ep-modal-field">
                                    <div class="ep-modal-field-label">User ID</div>
                                    <div class="ep-modal-field-value" style="font-family:'DM Mono',monospace">#{{ viewModal.user?.id }}</div>
                                </div>
                                <div class="ep-modal-field">
                                    <div class="ep-modal-field-label">Joined</div>
                                    <div class="ep-modal-field-value">{{ formattedDate(viewModal.user?.created_at) }}</div>
                                </div>
                                <div class="ep-modal-field">
                                    <div class="ep-modal-field-label">Status</div>
                                    <div class="ep-modal-field-value">
                                        <span :style="{ display:'inline-flex', alignItems:'center', gap:'5px', fontSize:'12px', fontWeight:600,
                                            color: viewModal.user?.is_active ? '#16a34a' : '#C0170F' }">
                                            <span :style="{ width:'6px', height:'6px', borderRadius:'50%', background: viewModal.user?.is_active ? '#16a34a' : '#C0170F', display:'inline-block' }"></span>
                                            {{ viewModal.user?.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ep-modal-field">
                                    <div class="ep-modal-field-label">Email Verified</div>
                                    <div class="ep-modal-field-value">
                                        <span :style="{ fontSize:'12px', fontWeight:600, color: viewModal.user?.email_verified_at ? '#16a34a' : '#9E9890' }">
                                            {{ viewModal.user?.email_verified_at ? '✓ Verified' : '✗ Unverified' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="ep-modal-footer">
                            <button class="ep-modal-btn-ghost" @click="closeViewModal">Close</button>
                            <Link :href="route('users.edit', { id: viewModal.user?.id })" class="ep-modal-btn-primary">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit User
                            </Link>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AuthenticatedLayout>
</template>