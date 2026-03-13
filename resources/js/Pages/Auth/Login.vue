<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
    canResetPassword: { type: Boolean, default: false },
    status:           { type: String,  default: null  },
    canLogin:         { type: Boolean },
    canRegister:      { type: Boolean },
    laravelVersion:   { type: String,  required: true },
    phpVersion:       { type: String,  required: true },
    errors:           Object,
    username:         String
});

const form = useForm({
    username: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onError: (errors) => {
            if (errors.username || errors.password || errors.auth) {
                Swal.fire({
                    title: 'Authentication Failed',
                    text: errors.username || errors.password || errors.auth || 'Invalid credentials.',
                    icon: 'error',
                    confirmButtonColor: '#C0170F',
                    confirmButtonText: 'Try Again'
                });
            }
        }
    });
};
</script>

<template>
    <Head title="Login — Event Portal" />

    <div class="ep-root">

        <!-- ── LEFT BRAND PANEL ── -->
        <div class="ep-left">
            <div class="ep-blob1"></div>
            <div class="ep-blob2"></div>

            <div class="ep-accent-bar"></div>

            <div class="ep-left-body">
                <!-- Logo -->
                <div class="ep-logo">
                    <div class="ep-logo-icon">
                        <svg width="26" height="26" viewBox="0 0 80 80" fill="none">
                            <path d="M22 12 L22 68 L34 74 L34 6 Z" fill="white" opacity="0.95"/>
                            <circle cx="48" cy="34" r="18" fill="white"/>
                            <path d="M22 56 L14 61 L14 71 L22 68 Z" fill="white" opacity="0.7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="ep-logo-name">Event Portal</div>
                        <div class="ep-logo-tag">Invite · Inform · Remind · Track</div>
                    </div>
                </div>

                <!-- Hero -->
                <div class="ep-hero">
                    <h2 class="ep-hero-title">Your events,<br/>perfectly managed.</h2>
                    <p class="ep-hero-sub">One portal to invite guests, send reminders, and track every RSVP.</p>
                </div>

                <!-- 4 pills -->
                <div class="ep-pills">
                    <div class="ep-pill"><span>✉️</span> Invite</div>
                    <div class="ep-pill"><span>📢</span> Inform</div>
                    <div class="ep-pill"><span>🔔</span> Remind</div>
                    <div class="ep-pill"><span>📊</span> Track</div>
                </div>

                <!-- Footer -->
                <div class="ep-left-footer">
                    <div class="ep-foot-dot"></div>
                    <span>All systems operational · Dar es Salaam, TZ</span>
                </div>
            </div>
        </div>

        <!-- ── RIGHT FORM PANEL ── -->
        <div class="ep-right">
            <div class="ep-accent-bar"></div>

            <div class="ep-right-body">

                <!-- Mobile logo -->
                <div class="ep-mobile-logo">
                    <div class="ep-mobile-icon">
                        <svg width="22" height="22" viewBox="0 0 80 80" fill="none">
                            <path d="M22 12 L22 68 L34 74 L34 6 Z" fill="white" opacity="0.95"/>
                            <circle cx="48" cy="34" r="18" fill="white"/>
                            <path d="M22 56 L14 61 L14 71 L22 68 Z" fill="white" opacity="0.7"/>
                        </svg>
                    </div>
                    <span class="ep-mobile-name">Event <b>Portal</b></span>
                </div>

                <!-- Heading -->
                <div class="ep-form-head">
                    <div class="ep-eyebrow">Admin Access</div>
                    <h1 class="ep-title">Welcome Back</h1>
                    <p class="ep-subtitle">Sign in to manage your events</p>
                </div>

                <!-- Status -->
                <div v-if="status" class="ep-status">{{ status }}</div>

                <form @submit.prevent="submit" class="ep-form">

                    <!-- Username -->
                    <div class="ep-field">
                        <label class="ep-label" for="username">Username</label>
                        <div class="ep-input-wrap">
                            <svg class="ep-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            <input
                                id="username"
                                type="text"
                                v-model="form.username"
                                required
                                placeholder="Enter your username"
                                :class="['ep-input', { 'ep-input--err': form.errors.username }]"
                                autocomplete="username"
                            />
                        </div>
                        <InputError :message="form.errors.username" class="ep-err" />
                    </div>

                    <!-- Password -->
                    <div class="ep-field">
                        <label class="ep-label" for="password">Password</label>
                        <div class="ep-input-wrap">
                            <svg class="ep-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                placeholder="Enter your password"
                                :class="['ep-input', { 'ep-input--err': form.errors.password }]"
                                autocomplete="current-password"
                            />
                        </div>
                        <InputError :message="form.errors.password" class="ep-err" />
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="ep-row">
                        <label class="ep-remember">
                            <input type="checkbox" v-model="form.remember" class="ep-check" />
                            <span>Remember me</span>
                        </label>
                        <Link v-if="canResetPassword" :href="route('password.request')" class="ep-forgot">
                            Forgot password?
                        </Link>
                    </div>

                    <!-- Submit -->
                    <button type="submit" :disabled="form.processing" class="ep-btn">
                        <svg v-if="form.processing" class="ep-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                        {{ form.processing ? 'Signing in...' : 'Sign In' }}
                    </button>

                </form>

                <!-- Footer -->
                <div class="ep-form-footer">
                    <span>Event Portal © 2026</span>
                    <span>·</span>
                    <span>Dar es Salaam, TZ</span>
                </div>

            </div>
        </div>

    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

*, *::before, *::after { box-sizing: border-box; }

.ep-root {
    display: flex;
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
}

/* ── ACCENT BAR ── */
.ep-accent-bar {
    height: 3px;
    background: linear-gradient(90deg, #C0170F, #F05A00, #F9B233, #1D5C96);
    flex-shrink: 0;
}

/* ── LEFT PANEL ── */
.ep-left {
    display: none;
    flex-direction: column;
    width: 48%;
    background: #C0170F;
    position: relative;
    overflow: hidden;
}

@media (min-width: 1024px) {
    .ep-left { display: flex; }
}

.ep-blob1 {
    position: absolute; top: -100px; right: -80px;
    width: 400px; height: 400px; border-radius: 50%;
    background: rgba(240,90,0,.3); filter: blur(70px);
    pointer-events: none;
}
.ep-blob2 {
    position: absolute; bottom: -60px; left: -60px;
    width: 300px; height: 300px; border-radius: 50%;
    background: rgba(249,178,51,.2); filter: blur(60px);
    pointer-events: none;
}

.ep-left-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 40px 48px;
    gap: 0;
    position: relative;
    z-index: 1;
}

/* Logo */
.ep-logo {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: auto;
}
.ep-logo-icon {
    width: 50px; height: 50px;
    background: rgba(255,255,255,.15);
    border: 1.5px solid rgba(255,255,255,.25);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ep-logo-name {
    font-family: 'Playfair Display', serif;
    font-size: 22px; font-weight: 900;
    color: #fff; line-height: 1.1;
}
.ep-logo-tag {
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: .18em;
    color: rgba(255,255,255,.55);
    text-transform: uppercase; margin-top: 3px;
}

/* Hero */
.ep-hero {
    margin: auto 0;
    padding: 48px 0 40px;
}
.ep-hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(30px, 3.5vw, 44px);
    font-weight: 900;
    color: #fff; line-height: 1.15;
    margin: 0 0 14px;
}
.ep-hero-sub {
    font-size: 15px;
    color: rgba(255,255,255,.65);
    line-height: 1.6;
    max-width: 300px;
    margin: 0;
}

/* Pills */
.ep-pills {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 40px;
}
.ep-pill {
    display: flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px;
    padding: 7px 14px;
    font-size: 13px; font-weight: 600;
    color: #fff;
}

/* Left footer */
.ep-left-footer {
    display: flex; align-items: center; gap: 8px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,.15);
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: .1em;
    color: rgba(255,255,255,.45);
}
.ep-foot-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: #4ade80; box-shadow: 0 0 6px #4ade80;
    flex-shrink: 0;
}

/* ── RIGHT PANEL ── */
.ep-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #F7F5F2;
}

.ep-right-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 32px;
}

/* Mobile logo */
.ep-mobile-logo {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 36px;
}
.ep-mobile-icon {
    width: 42px; height: 42px;
    background: #C0170F; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}
.ep-mobile-name {
    font-family: 'Playfair Display', serif;
    font-size: 20px; color: #1A1410;
}
.ep-mobile-name b { color: #C0170F; }

@media (min-width: 1024px) { .ep-mobile-logo { display: none; } }

/* Form container */
.ep-form-head { width: 100%; max-width: 400px; margin-bottom: 28px; }

.ep-eyebrow {
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: .22em;
    color: #C0170F; text-transform: uppercase;
    margin-bottom: 8px;
}
.ep-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(28px, 4vw, 36px);
    font-weight: 900; color: #1A1410;
    line-height: 1.1; margin: 0 0 6px;
}
.ep-subtitle {
    font-size: 14px; color: #6B6560; margin: 0;
}

/* Status */
.ep-status {
    width: 100%; max-width: 400px;
    background: rgba(29,92,150,.08);
    border-left: 3px solid #1D5C96;
    border-radius: 8px;
    padding: 11px 14px;
    font-size: 13px; color: #1D5C96;
    margin-bottom: 20px;
}

/* Form */
.ep-form { width: 100%; max-width: 400px; }

.ep-field { margin-bottom: 18px; }

.ep-label {
    display: block;
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: .14em;
    color: #6B6560; text-transform: uppercase;
    margin-bottom: 6px;
}

.ep-input-wrap { position: relative; }

.ep-icon {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%);
    color: #9E9890; pointer-events: none;
}

.ep-input {
    width: 100%;
    padding: 11px 14px 11px 40px;
    border: 1.5px solid #E8E2DA;
    border-radius: 12px;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    color: #1A1410;
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.ep-input::placeholder { color: #C0B8B0; }
.ep-input:focus {
    border-color: #C0170F;
    box-shadow: 0 0 0 3px rgba(192,23,15,.1);
}
.ep-input--err { border-color: #C0170F; }

.ep-err {
    font-size: 11px !important;
    color: #C0170F !important;
    margin-top: 4px;
    font-family: 'DM Mono', monospace;
}

/* Row */
.ep-row {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
}
.ep-remember {
    display: flex; align-items: center; gap: 7px;
    font-size: 13px; color: #6B6560; cursor: pointer;
}
.ep-check { accent-color: #C0170F; width: 15px; height: 15px; }

.ep-forgot {
    font-family: 'DM Mono', monospace;
    font-size: 11px; letter-spacing: .06em;
    color: #C0170F; text-decoration: none;
}
.ep-forgot:hover { opacity: .7; }

/* Button */
.ep-btn {
    width: 100%;
    padding: 13px;
    border: none; border-radius: 12px;
    background: linear-gradient(135deg, #C0170F, #8B0000);
    color: #fff;
    font-size: 14px; font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 18px rgba(192,23,15,.35);
    transition: all .22s;
    position: relative; overflow: hidden;
}
.ep-btn::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, #F05A00, #C0170F);
    opacity: 0; transition: opacity .22s;
}
.ep-btn:hover::after { opacity: 1; }
.ep-btn:hover { box-shadow: 0 6px 24px rgba(192,23,15,.45); transform: translateY(-1px); }
.ep-btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }
.ep-btn > * { position: relative; z-index: 1; }

@keyframes ep-spin { to { transform: rotate(360deg); } }
.ep-spin { animation: ep-spin .8s linear infinite; }

/* Form footer */
.ep-form-footer {
    width: 100%; max-width: 400px;
    margin-top: 28px;
    padding-top: 18px;
    border-top: 1px solid #E8E2DA;
    display: flex; gap: 8px;
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: .08em;
    color: #9E9890;
}
</style>