<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Swal from 'sweetalert2';

const props = defineProps({ roles: Array });

const form = useForm({
    first_name: '', last_name: '', email: '', phone: '',
    role_ids: [], password: '', password_confirmation: '',
    date_of_birth: '', gender: '', is_active: true, is_verified: false,
});

const processing = ref(false);
const showPassword = ref(false);
const showConfirm  = ref(false);

const selectedRoleNames = computed(() =>
    (props.roles || []).filter(r => form.role_ids.includes(r.id)).map(r => r.name).join(', ')
);

function calcStrength(p) {
    let s = 0;
    if (p.length >= 8)        s += 25;
    if (/[A-Z]/.test(p))      s += 25;
    if (/[a-z]/.test(p))      s += 25;
    if (/[0-9]/.test(p))      s += 15;
    if (/[^A-Za-z0-9]/.test(p)) s += 10;
    return Math.min(s, 100);
}

const passwordStrength = computed(() => {
    if (!form.password) return null;
    const s = calcStrength(form.password);
    if (s >= 80) return { text: 'Strong', color: '#16a34a', bar: '#16a34a', width: '100%' };
    if (s >= 60) return { text: 'Good',   color: '#d97706', bar: '#F9B233', width: '75%'  };
    if (s >= 40) return { text: 'Fair',   color: '#F05A00', bar: '#F05A00', width: '50%'  };
    return             { text: 'Weak',   color: '#C0170F', bar: '#C0170F', width: '25%'  };
});

const passwordsMatch = computed(() =>
    form.password && form.password_confirmation
        ? form.password === form.password_confirmation
        : null
);

const roleColor = (name) => {
    const n = (name || '').toUpperCase();
    if (n.includes('ADMIN')) return { bg: 'rgba(192,23,15,.1)',  border: '#C0170F',  text: '#C0170F'  };
    if (n.includes('STAFF')) return { bg: 'rgba(249,178,51,.15)', border: '#b45309', text: '#b45309'  };
    return                          { bg: 'rgba(29,92,150,.1)',  border: '#1D5C96',  text: '#1D5C96'  };
};

const submit = () => {
    if (!form.first_name || !form.last_name || !form.email || !form.password || form.role_ids.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Required Fields', text: 'Fill in all required fields.', confirmButtonColor: '#C0170F' });
        return;
    }
    if (form.password !== form.password_confirmation) {
        Swal.fire({ icon: 'error', title: 'Password Mismatch', text: 'Passwords do not match.', confirmButtonColor: '#C0170F' });
        return;
    }

    Swal.fire({
        title: 'Create this user?',
        html: `<div style="text-align:left;font-size:13px;line-height:1.8;font-family:'DM Sans',sans-serif">
            <div><b>Name:</b> ${form.first_name} ${form.last_name}</div>
            <div><b>Email:</b> ${form.email}</div>
            <div><b>Phone:</b> ${form.phone || '—'}</div>
            <div><b>Roles:</b> ${selectedRoleNames.value || 'None'}</div>
            <div><b>Status:</b> ${form.is_active ? '✅ Active' : '⛔ Inactive'}</div>
            <div><b>Verified:</b> ${form.is_verified ? '✅ Yes' : '❌ No'}</div>
        </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#C0170F',
        cancelButtonColor: '#6B6560',
        confirmButtonText: 'Create User',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    }).then((result) => {
        if (!result.isConfirmed) return;
        processing.value = true;
        Swal.fire({ title: 'Creating user…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        form.post(route('users.store'), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success', title: 'User Created!',
                    text: `${form.first_name} ${form.last_name} has been added.`,
                    showCancelButton: true,
                    confirmButtonText: 'View Users',
                    cancelButtonText: 'Add Another',
                    confirmButtonColor: '#C0170F',
                }).then((r) => {
                    if (r.isConfirmed) { window.location.href = route('users.index'); }
                    else { form.reset(); form.is_active = true; form.is_verified = false; document.getElementById('first_name')?.focus(); }
                });
            },
            onError: (errors) => {
                const msg = errors.email?.includes('taken') ? `Email <b>${form.email}</b> is already registered.`
                          : errors.password ? errors.password
                          : errors.role_ids ? errors.role_ids
                          : 'Please check the form for errors.';
                Swal.fire({ icon: 'error', title: 'Creation Failed', html: msg, confirmButtonColor: '#C0170F' });
            },
            onFinish: () => { processing.value = false; }
        });
    });
};
</script>

<template>
    <Head title="Create User" />
    <AuthenticatedLayout>

        <component :is="'style'">
            @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

            .ep-cu * { box-sizing: border-box; }
            .ep-cu {
                --crimson: #C0170F; --orange: #F05A00; --amber: #F9B233; --navy: #1D5C96;
                --bg: #F7F5F2; --bg-card: #FFFFFF; --bg-muted: #F0EDE8;
                --border: #E8E2DA; --text: #1A1410; --text-2: #6B6560; --text-3: #9E9890;
                --shadow: 0 2px 16px rgba(0,0,0,.07);
                font-family: 'DM Sans', sans-serif;
                min-height: 100vh; background: var(--bg); padding: 28px 24px 56px;
            }

            /* Page header */
            .ep-cu-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; }
            .ep-cu-eyebrow { font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: .22em; color: var(--crimson); text-transform: uppercase; margin-bottom: 4px; }
            .ep-cu-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 900; color: var(--text); margin: 0 0 4px; line-height: 1.1; }
            .ep-cu-sub { font-size: 13px; color: var(--text-2); margin: 0; }

            .ep-btn-back {
                display: inline-flex; align-items: center; gap: 7px;
                padding: 9px 16px; border-radius: 10px;
                border: 1.5px solid var(--border); background: var(--bg-card);
                color: var(--text-2); font-size: 12px; font-weight: 600;
                font-family: 'DM Sans', sans-serif; text-decoration: none;
                transition: all .18s; white-space: nowrap;
            }
            .ep-btn-back:hover { border-color: var(--crimson); color: var(--crimson); background: rgba(192,23,15,.04); }

            /* Layout: sidebar + form */
            .ep-cu-body { display: grid; grid-template-columns: 220px 1fr; gap: 20px; align-items: start; }
            @media (max-width: 860px) { .ep-cu-body { grid-template-columns: 1fr; } }

            /* Sidebar */
            .ep-sidebar { display: flex; flex-direction: column; gap: 10px; }
            .ep-sidebar-item {
                display: flex; align-items: center; gap: 10px;
                padding: 11px 14px; border-radius: 12px;
                background: var(--bg-card); border: 1.5px solid var(--border);
                cursor: pointer; transition: all .18s; text-decoration: none;
            }
            .ep-sidebar-item.active { border-color: var(--crimson); background: rgba(192,23,15,.05); }
            .ep-sidebar-item.active .ep-sidebar-icon { background: var(--crimson); color: #fff; }
            .ep-sidebar-icon { width: 28px; height: 28px; border-radius: 8px; background: var(--bg-muted); color: var(--text-3); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .18s; }
            .ep-sidebar-label { font-size: 12px; font-weight: 600; color: var(--text); }
            .ep-sidebar-sub { font-size: 10px; color: var(--text-3); font-family: 'DM Mono', monospace; }

            /* Card */
            .ep-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow); }

            /* Section headers */
            .ep-section { padding: 22px 24px; border-bottom: 1px solid var(--border); }
            .ep-section:last-child { border-bottom: none; }
            .ep-section-head { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
            .ep-section-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
            .ep-section-title { font-family: 'Playfair Display', serif; font-size: 15px; font-weight: 700; color: var(--text); }
            .ep-section-badge { font-family: 'DM Mono', monospace; font-size: 9px; letter-spacing: .12em; text-transform: uppercase; padding: 2px 8px; border-radius: 20px; margin-left: auto; }

            /* Grid */
            .ep-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
            @media (max-width: 640px) { .ep-grid-2 { grid-template-columns: 1fr; } }

            /* Field */
            .ep-field { display: flex; flex-direction: column; gap: 5px; }
            .ep-label { font-family: 'DM Mono', monospace; font-size: 9px; letter-spacing: .16em; color: var(--text-3); text-transform: uppercase; }
            .ep-label span { color: var(--crimson); }
            .ep-input-wrap { position: relative; }
            .ep-input-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--text-3); pointer-events: none; }
            .ep-input-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-3); cursor: pointer; padding: 2px; display: flex; align-items: center; }
            .ep-input-toggle:hover { color: var(--text-2); }
            .ep-input {
                width: 100%; padding: 10px 12px 10px 36px;
                border: 1.5px solid var(--border); border-radius: 10px;
                font-size: 13px; font-family: 'DM Sans', sans-serif;
                color: var(--text); background: var(--bg); outline: none;
                transition: border-color .18s, box-shadow .18s;
            }
            .ep-input.no-icon { padding-left: 12px; }
            .ep-input.has-toggle { padding-right: 36px; }
            .ep-input::placeholder { color: var(--text-3); font-size: 12px; }
            .ep-input:focus { border-color: var(--crimson); box-shadow: 0 0 0 3px rgba(192,23,15,.1); background: #fff; }
            .ep-input.ep-err { border-color: var(--crimson); }
            .ep-input-hint { font-size: 11px; color: var(--text-3); font-family: 'DM Mono', monospace; }
            .ep-input-hint.ok  { color: #16a34a; }
            .ep-input-hint.bad { color: var(--crimson); }

            /* Password strength bar */
            .ep-strength-bar { height: 3px; border-radius: 2px; background: var(--border); margin-top: 6px; overflow: hidden; }
            .ep-strength-fill { height: 100%; border-radius: 2px; transition: width .3s, background .3s; }

            /* Role chips */
            .ep-roles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; }
            .ep-role-chip {
                display: flex; align-items: center; gap: 8px;
                padding: 10px 12px; border-radius: 10px;
                border: 1.5px solid var(--border);
                background: var(--bg); cursor: pointer;
                transition: all .18s; user-select: none;
            }
            .ep-role-chip:hover { border-color: var(--crimson); background: rgba(192,23,15,.03); }
            .ep-role-chip.selected { background: var(--active-bg); border-color: var(--active-border); }
            .ep-role-chip input[type="checkbox"] { display: none; }
            .ep-role-chip-dot { width: 16px; height: 16px; border-radius: 4px; border: 1.5px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .18s; }
            .ep-role-chip.selected .ep-role-chip-dot { border-color: var(--active-border); background: var(--active-border); }
            .ep-role-name { font-size: 11px; font-weight: 600; font-family: 'DM Sans', sans-serif; color: var(--text-2); }
            .ep-role-chip.selected .ep-role-name { color: var(--active-text); }

            /* Toggle switch */
            .ep-toggle-card {
                display: flex; align-items: flex-start; justify-content: space-between;
                gap: 12px; padding: 14px 16px; border-radius: 12px;
                border: 1.5px solid var(--border); background: var(--bg);
                cursor: pointer; transition: all .18s;
            }
            .ep-toggle-card:hover { border-color: rgba(192,23,15,.3); }
            .ep-toggle-card.on { border-color: var(--crimson); background: rgba(192,23,15,.04); }
            .ep-toggle-label { font-size: 13px; font-weight: 600; color: var(--text); }
            .ep-toggle-sub { font-size: 11px; color: var(--text-3); font-family: 'DM Mono', monospace; margin-top: 2px; }
            .ep-toggle-switch { flex-shrink: 0; width: 36px; height: 20px; border-radius: 10px; background: var(--border); transition: background .2s; position: relative; margin-top: 1px; }
            .ep-toggle-switch.on { background: var(--crimson); }
            .ep-toggle-knob { position: absolute; top: 2px; left: 2px; width: 16px; height: 16px; border-radius: 50%; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.2); transition: left .2s; }
            .ep-toggle-switch.on .ep-toggle-knob { left: 18px; }
            .ep-toggle-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
            @media (max-width: 500px) { .ep-toggle-2col { grid-template-columns: 1fr; } }

            /* Error banner */
            .ep-error-banner { display: flex; gap: 10px; padding: 12px 16px; background: rgba(192,23,15,.06); border: 1px solid rgba(192,23,15,.2); border-radius: 12px; margin-bottom: 20px; }
            .ep-error-banner ul { margin: 4px 0 0 16px; padding: 0; font-size: 12px; color: var(--crimson); }

            /* Form footer */
            .ep-form-footer { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; background: var(--bg-muted); border-top: 1px solid var(--border); gap: 12px; }
            .ep-footer-note { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--text-3); }
            .ep-footer-note span { color: var(--crimson); }
            .ep-footer-actions { display: flex; align-items: center; gap: 8px; }

            .ep-btn-ghost {
                padding: 9px 16px; border-radius: 10px; border: 1.5px solid var(--border);
                background: var(--bg-card); color: var(--text-2);
                font-size: 12px; font-weight: 600; font-family: 'DM Sans', sans-serif;
                cursor: pointer; transition: all .18s;
            }
            .ep-btn-ghost:hover { border-color: var(--text-3); color: var(--text); }

            .ep-btn-primary {
                padding: 10px 22px; border-radius: 10px; border: none;
                background: linear-gradient(135deg, var(--crimson), #8B0000);
                color: #fff; font-size: 13px; font-weight: 700;
                font-family: 'DM Sans', sans-serif; cursor: pointer;
                display: flex; align-items: center; gap: 7px;
                box-shadow: 0 4px 14px rgba(192,23,15,.3);
                transition: all .2s; position: relative; overflow: hidden;
            }
            .ep-btn-primary::after { content:''; position:absolute; inset:0; background: linear-gradient(135deg,var(--orange),var(--crimson)); opacity:0; transition:opacity .2s; }
            .ep-btn-primary:hover::after { opacity:1; }
            .ep-btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(192,23,15,.4); }
            .ep-btn-primary:disabled { opacity:.5; cursor:not-allowed; transform:none; }
            .ep-btn-primary > * { position:relative; z-index:1; }
            @keyframes ep-spin { to { transform:rotate(360deg); } }
            .ep-spin { animation: ep-spin .8s linear infinite; }
        </component>

        <div class="ep-cu">

            <!-- Header -->
            <div class="ep-cu-header">
                <div>
                    <div class="ep-cu-eyebrow">User Management</div>
                    <h1 class="ep-cu-title">Create New User</h1>
                    <p class="ep-cu-sub">Fill in the details below to add a new user</p>
                </div>
                <Link :href="route('users.index')" class="ep-btn-back">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to Users
                </Link>
            </div>

            <!-- Error banner -->
            <div v-if="Object.keys(form.errors).length > 0" class="ep-error-banner">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C0170F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#C0170F">Please fix the following errors</div>
                    <ul>
                        <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                    </ul>
                </div>
            </div>

            <!-- Body: sidebar + card -->
            <form @submit.prevent="submit">
                <div class="ep-cu-body">

                    <!-- Sidebar nav -->
                    <div class="ep-sidebar">
                        <div class="ep-sidebar-item active">
                            <div class="ep-sidebar-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div class="ep-sidebar-label">Personal Info</div>
                                <div class="ep-sidebar-sub">Name · Contact</div>
                            </div>
                        </div>
                        <div class="ep-sidebar-item">
                            <div class="ep-sidebar-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <div>
                                <div class="ep-sidebar-label">Account Setup</div>
                                <div class="ep-sidebar-sub">Roles · Password</div>
                            </div>
                        </div>
                        <div class="ep-sidebar-item">
                            <div class="ep-sidebar-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <div>
                                <div class="ep-sidebar-label">Status</div>
                                <div class="ep-sidebar-sub">Active · Verified</div>
                            </div>
                        </div>
                    </div>

                    <!-- Main form card -->
                    <div class="ep-card">

                        <!-- Section 1: Personal Information -->
                        <div class="ep-section">
                            <div class="ep-section-head">
                                <div class="ep-section-icon" style="background:rgba(192,23,15,.1)">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#C0170F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <div class="ep-section-title">Personal Information</div>
                                <span class="ep-section-badge" style="background:rgba(192,23,15,.08);color:#C0170F">Required</span>
                            </div>

                            <div class="ep-grid-2" style="margin-bottom:14px">
                                <!-- First Name -->
                                <div class="ep-field">
                                    <label class="ep-label" for="first_name">First Name <span>*</span></label>
                                    <div class="ep-input-wrap">
                                        <svg class="ep-input-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        <input id="first_name" type="text" v-model="form.first_name" required autofocus autocomplete="given-name" placeholder="John"
                                            :class="['ep-input', { 'ep-err': form.errors.first_name }]" />
                                    </div>
                                    <InputError :message="form.errors.first_name" />
                                </div>
                                <!-- Last Name -->
                                <div class="ep-field">
                                    <label class="ep-label" for="last_name">Last Name <span>*</span></label>
                                    <div class="ep-input-wrap">
                                        <svg class="ep-input-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        <input id="last_name" type="text" v-model="form.last_name" required autocomplete="family-name" placeholder="Doe"
                                            :class="['ep-input', { 'ep-err': form.errors.last_name }]" />
                                    </div>
                                    <InputError :message="form.errors.last_name" />
                                </div>
                            </div>

                            <div class="ep-grid-2" style="margin-bottom:14px">
                                <!-- Email -->
                                <div class="ep-field">
                                    <label class="ep-label" for="email">Email Address <span>*</span></label>
                                    <div class="ep-input-wrap">
                                        <svg class="ep-input-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        <input id="email" type="email" v-model="form.email" required autocomplete="email" placeholder="john@example.com"
                                            :class="['ep-input', { 'ep-err': form.errors.email }]" />
                                    </div>
                                    <InputError :message="form.errors.email" />
                                </div>
                                <!-- Phone -->
                                <div class="ep-field">
                                    <label class="ep-label" for="phone">Phone Number</label>
                                    <div class="ep-input-wrap">
                                        <svg class="ep-input-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.22 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 6.29 6.29l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <input id="phone" type="tel" v-model="form.phone" autocomplete="tel" placeholder="+255 XXX XXX XXX"
                                            :class="['ep-input', { 'ep-err': form.errors.phone }]" />
                                    </div>
                                    <InputError :message="form.errors.phone" />
                                </div>
                            </div>

                            <div class="ep-grid-2">
                                <!-- Date of Birth -->
                                <div class="ep-field">
                                    <label class="ep-label" for="date_of_birth">Date of Birth</label>
                                    <div class="ep-input-wrap">
                                        <svg class="ep-input-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <input id="date_of_birth" type="date" v-model="form.date_of_birth"
                                            :class="['ep-input', { 'ep-err': form.errors.date_of_birth }]" />
                                    </div>
                                    <InputError :message="form.errors.date_of_birth" />
                                </div>
                                <!-- Gender -->
                                <div class="ep-field">
                                    <label class="ep-label" for="gender">Gender</label>
                                    <div class="ep-input-wrap">
                                        <svg class="ep-input-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><line x1="12" y1="2" x2="12" y2="8"/><line x1="12" y1="16" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/></svg>
                                        <select id="gender" v-model="form.gender" :class="['ep-input', { 'ep-err': form.errors.gender }]">
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                    <InputError :message="form.errors.gender" />
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Account Setup -->
                        <div class="ep-section">
                            <div class="ep-section-head">
                                <div class="ep-section-icon" style="background:rgba(29,92,150,.1)">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1D5C96" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <div class="ep-section-title">Account Setup</div>
                                <span class="ep-section-badge" style="background:rgba(29,92,150,.08);color:#1D5C96">Security</span>
                            </div>

                            <!-- Roles -->
                            <div class="ep-field" style="margin-bottom:18px">
                                <label class="ep-label">Assign Roles <span style="color:#C0170F">*</span></label>
                                <div class="ep-roles-grid">
                                    <label
                                        v-for="role in roles" :key="role.id"
                                        :for="`role-${role.id}`"
                                        :class="['ep-role-chip', { selected: form.role_ids.includes(role.id) }]"
                                        :style="form.role_ids.includes(role.id) ? {
                                            '--active-bg': roleColor(role.name).bg,
                                            '--active-border': roleColor(role.name).border,
                                            '--active-text': roleColor(role.name).text
                                        } : {}"
                                    >
                                        <input :id="`role-${role.id}`" type="checkbox" :value="role.id" v-model="form.role_ids" />
                                        <div class="ep-role-chip-dot">
                                            <svg v-if="form.role_ids.includes(role.id)" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                        <span class="ep-role-name">{{ role.name }}</span>
                                    </label>
                                </div>
                                <InputError :message="form.errors.role_ids" />
                            </div>

                            <!-- Passwords -->
                            <div class="ep-grid-2">
                                <div class="ep-field">
                                    <label class="ep-label" for="password">Password <span style="color:#C0170F">*</span></label>
                                    <div class="ep-input-wrap">
                                        <svg class="ep-input-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                        <input :type="showPassword ? 'text' : 'password'" id="password" v-model="form.password" required autocomplete="new-password" placeholder="••••••••"
                                            :class="['ep-input has-toggle', { 'ep-err': form.errors.password }]" />
                                        <button type="button" class="ep-input-toggle" @click="showPassword = !showPassword">
                                            <svg v-if="!showPassword" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        </button>
                                    </div>
                                    <div v-if="passwordStrength" class="ep-strength-bar">
                                        <div class="ep-strength-fill" :style="{ width: passwordStrength.width, background: passwordStrength.bar }"></div>
                                    </div>
                                    <span v-if="passwordStrength" class="ep-input-hint" :style="{ color: passwordStrength.color }">{{ passwordStrength.text }}</span>
                                    <InputError :message="form.errors.password" />
                                </div>

                                <div class="ep-field">
                                    <label class="ep-label" for="password_confirmation">Confirm Password <span style="color:#C0170F">*</span></label>
                                    <div class="ep-input-wrap">
                                        <svg class="ep-input-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                        <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" v-model="form.password_confirmation" required autocomplete="new-password" placeholder="••••••••"
                                            :class="['ep-input has-toggle', { 'ep-err': form.errors.password_confirmation }]" />
                                        <button type="button" class="ep-input-toggle" @click="showConfirm = !showConfirm">
                                            <svg v-if="!showConfirm" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        </button>
                                    </div>
                                    <span v-if="passwordsMatch === true"  class="ep-input-hint ok">✓ Passwords match</span>
                                    <span v-if="passwordsMatch === false" class="ep-input-hint bad">✗ Passwords don't match</span>
                                    <InputError :message="form.errors.password_confirmation" />
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Status -->
                        <div class="ep-section">
                            <div class="ep-section-head">
                                <div class="ep-section-icon" style="background:rgba(249,178,51,.12)">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </div>
                                <div class="ep-section-title">Account Status</div>
                                <span class="ep-section-badge" style="background:rgba(249,178,51,.12);color:#b45309">Permissions</span>
                            </div>

                            <div class="ep-toggle-2col">
                                <div :class="['ep-toggle-card', { on: form.is_active }]" @click="form.is_active = !form.is_active">
                                    <div>
                                        <div class="ep-toggle-label">Active Account</div>
                                        <div class="ep-toggle-sub">User can log in to portal</div>
                                    </div>
                                    <div :class="['ep-toggle-switch', { on: form.is_active }]">
                                        <div class="ep-toggle-knob"></div>
                                    </div>
                                </div>
                                <div :class="['ep-toggle-card', { on: form.is_verified }]" @click="form.is_verified = !form.is_verified">
                                    <div>
                                        <div class="ep-toggle-label">Email Verified</div>
                                        <div class="ep-toggle-sub">Skip email verification</div>
                                    </div>
                                    <div :class="['ep-toggle-switch', { on: form.is_verified }]">
                                        <div class="ep-toggle-knob"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer actions -->
                        <div class="ep-form-footer">
                            <div class="ep-footer-note"><span>*</span> Required fields</div>
                            <div class="ep-footer-actions">
                                <button type="button" class="ep-btn-ghost" @click="form.reset(); form.is_active = true; form.is_verified = false;">
                                    Reset
                                </button>
                                <Link :href="route('users.index')" class="ep-btn-ghost" style="text-decoration:none">
                                    Cancel
                                </Link>
                                <button type="submit" class="ep-btn-primary" :disabled="processing">
                                    <svg v-if="processing" class="ep-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                    <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                    {{ processing ? 'Creating…' : 'Create User' }}
                                </button>
                            </div>
                        </div>

                    </div><!-- /ep-card -->
                </div><!-- /ep-cu-body -->
            </form>

        </div>
    </AuthenticatedLayout>
</template>