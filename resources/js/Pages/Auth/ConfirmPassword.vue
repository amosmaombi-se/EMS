<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ password: '' });

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirm Password — Event Portal" />

    <div class="ep-root">

        <!-- Left panel -->
        <div class="ep-left">
            <div class="ep-blob1"></div>
            <div class="ep-blob2"></div>
            <div class="ep-accent-bar"></div>
            <div class="ep-left-body">
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

                <div class="ep-hero">
                    <div class="ep-hero-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <h2 class="ep-hero-title">Secure<br/>area</h2>
                    <p class="ep-hero-sub">Confirm your password to access this protected section of Event Portal.</p>
                </div>

                <div class="ep-left-footer">
                    <div class="ep-foot-dot"></div>
                    <span>All systems operational · Dar es Salaam, TZ</span>
                </div>
            </div>
        </div>

        <!-- Right panel -->
        <div class="ep-right">
            <div class="ep-accent-bar"></div>
            <div class="ep-right-body">

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

                <div class="ep-form-head">
                    <div class="ep-eyebrow">Security Check</div>
                    <h1 class="ep-title">Confirm Password</h1>
                    <p class="ep-subtitle">Please re-enter your password to continue.</p>
                </div>

                <form @submit.prevent="submit" class="ep-form">
                    <div class="ep-field">
                        <label class="ep-label" for="password">Password</label>
                        <div class="ep-input-wrap">
                            <svg class="ep-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <input
                                id="password" type="password"
                                v-model="form.password"
                                required autofocus autocomplete="current-password"
                                placeholder="Enter your password"
                                :class="['ep-input', { 'ep-input--err': form.errors.password }]"
                            />
                        </div>
                        <InputError :message="form.errors.password" class="ep-err" />
                    </div>

                    <button type="submit" :disabled="form.processing" class="ep-btn">
                        <svg v-if="form.processing" class="ep-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                        {{ form.processing ? 'Confirming...' : 'Confirm Password' }}
                    </button>

                    <div class="ep-back">
                        <Link :href="route('login')" class="ep-back-link">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            Back to login
                        </Link>
                    </div>
                </form>

                <div class="ep-form-footer">
                    <span>Event Portal © 2026</span><span>·</span><span>Dar es Salaam, TZ</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
*, *::before, *::after { box-sizing: border-box; }
@keyframes ep-spin { to { transform: rotate(360deg); } }
.ep-spin { animation: ep-spin .8s linear infinite; }

.ep-root { display: flex; min-height: 100vh; font-family: 'DM Sans', sans-serif; }
.ep-accent-bar { height: 3px; background: linear-gradient(90deg, #C0170F, #F05A00, #F9B233, #1D5C96); flex-shrink: 0; }

.ep-left { display: none; flex-direction: column; width: 48%; background: #C0170F; position: relative; overflow: hidden; }
@media (min-width: 1024px) { .ep-left { display: flex; } }
.ep-blob1 { position: absolute; top: -100px; right: -80px; width: 400px; height: 400px; border-radius: 50%; background: rgba(240,90,0,.3); filter: blur(70px); pointer-events: none; }
.ep-blob2 { position: absolute; bottom: -60px; left: -60px; width: 300px; height: 300px; border-radius: 50%; background: rgba(249,178,51,.2); filter: blur(60px); pointer-events: none; }
.ep-left-body { flex: 1; display: flex; flex-direction: column; padding: 40px 48px; position: relative; z-index: 1; }
.ep-logo { display: flex; align-items: center; gap: 14px; margin-bottom: auto; }
.ep-logo-icon { width: 50px; height: 50px; background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.25); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ep-logo-name { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 900; color: #fff; line-height: 1.1; }
.ep-logo-tag { font-family: 'DM Mono', monospace; font-size: 9px; letter-spacing: .18em; color: rgba(255,255,255,.55); text-transform: uppercase; margin-top: 3px; }
.ep-hero { margin: auto 0; padding: 48px 0 40px; text-align: center; }
.ep-hero-icon { width: 72px; height: 72px; background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.25); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
.ep-hero-title { font-family: 'Playfair Display', serif; font-size: clamp(30px, 3.5vw, 44px); font-weight: 900; color: #fff; line-height: 1.15; margin: 0 0 14px; }
.ep-hero-sub { font-size: 15px; color: rgba(255,255,255,.65); line-height: 1.6; max-width: 280px; margin: 0 auto; }
.ep-left-footer { display: flex; align-items: center; gap: 8px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,.15); font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: .1em; color: rgba(255,255,255,.45); }
.ep-foot-dot { width: 6px; height: 6px; border-radius: 50%; background: #4ade80; box-shadow: 0 0 6px #4ade80; flex-shrink: 0; }

.ep-right { flex: 1; display: flex; flex-direction: column; background: #F7F5F2; }
.ep-right-body { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 32px; }
.ep-mobile-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 36px; }
.ep-mobile-icon { width: 42px; height: 42px; background: #C0170F; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.ep-mobile-name { font-family: 'Playfair Display', serif; font-size: 20px; color: #1A1410; }
.ep-mobile-name b { color: #C0170F; }
@media (min-width: 1024px) { .ep-mobile-logo { display: none; } }
.ep-form-head { width: 100%; max-width: 400px; margin-bottom: 28px; }
.ep-eyebrow { font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: .22em; color: #C0170F; text-transform: uppercase; margin-bottom: 8px; }
.ep-title { font-family: 'Playfair Display', serif; font-size: clamp(26px, 4vw, 34px); font-weight: 900; color: #1A1410; line-height: 1.1; margin: 0 0 6px; }
.ep-subtitle { font-size: 14px; color: #6B6560; margin: 0; }
.ep-form { width: 100%; max-width: 400px; }
.ep-field { margin-bottom: 20px; }
.ep-label { display: block; font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: .14em; color: #6B6560; text-transform: uppercase; margin-bottom: 6px; }
.ep-input-wrap { position: relative; }
.ep-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #9E9890; pointer-events: none; }
.ep-input { width: 100%; padding: 11px 14px 11px 40px; border: 1.5px solid #E8E2DA; border-radius: 12px; font-size: 14px; font-family: 'DM Sans', sans-serif; color: #1A1410; background: #fff; outline: none; transition: border-color .2s, box-shadow .2s; }
.ep-input::placeholder { color: #C0B8B0; }
.ep-input:focus { border-color: #C0170F; box-shadow: 0 0 0 3px rgba(192,23,15,.1); }
.ep-input--err { border-color: #C0170F; }
.ep-err { font-family: 'DM Mono', monospace; font-size: 11px; color: #C0170F; margin-top: 4px; display: block; }
.ep-btn { width: 100%; padding: 13px; border: none; border-radius: 12px; background: linear-gradient(135deg, #C0170F, #8B0000); color: #fff; font-size: 14px; font-weight: 700; font-family: 'DM Sans', sans-serif; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 18px rgba(192,23,15,.35); transition: all .22s; position: relative; overflow: hidden; margin-bottom: 16px; }
.ep-btn::after { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, #F05A00, #C0170F); opacity: 0; transition: opacity .22s; }
.ep-btn:hover::after { opacity: 1; }
.ep-btn:hover { box-shadow: 0 6px 24px rgba(192,23,15,.45); transform: translateY(-1px); }
.ep-btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }
.ep-btn > * { position: relative; z-index: 1; }
.ep-back { text-align: center; padding-top: 14px; border-top: 1px solid #E8E2DA; }
.ep-back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #6B6560; text-decoration: none; transition: color .2s; }
.ep-back-link:hover { color: #C0170F; }
.ep-form-footer { width: 100%; max-width: 400px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #E8E2DA; display: flex; gap: 8px; font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: .08em; color: #9E9890; }
</style>