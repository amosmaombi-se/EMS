<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    maskedPhone:        { type: String, default: '***-***-1234' },
    maskedEmail:        { type: String, default: null },
    initialTimer:       { type: Number, default: 300 },
    userId:             { type: Number },
    notificationStatus: { type: Object, default: () => ({}) },
    message:            { type: String, default: null },
    cooldownPeriod:     { type: Number, default: 300 },
    maxResendAttempts:  { type: Number, default: 3 },
    remainingAttempts:  { type: Number, default: 3 }
});

const emit = defineEmits(['verified', 'resend']);

const otpDigits   = ref(Array(6).fill(''));
const inputRefs   = ref([]);
const error       = ref('');
const loading     = ref(false);
const timer       = ref(props.cooldownPeriod || props.initialTimer);
let timerInterval = null;

const isComplete = computed(() =>
    otpDigits.value.every(d => /^[0-9]$/.test(d))
);

const handleInput = (event, index) => {
    const val = event.target.value;
    if (!/^[0-9]$/.test(val)) { otpDigits.value[index] = ''; return; }
    if (index < 5) inputRefs.value[index + 1]?.focus();
    error.value = '';
};

const handleKeydown = (event, index) => {
    if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0)
        inputRefs.value[index - 1]?.focus();
};

const handlePaste = (event) => {
    event.preventDefault();
    const nums = event.clipboardData.getData('text').match(/\d/g);
    if (nums) {
        nums.slice(0, 6).forEach((n, i) => { otpDigits.value[i] = n; });
        inputRefs.value[Math.min(nums.length, 5)]?.focus();
    }
};

const handleSubmit = async () => {
    if (!isComplete.value) return;
    if (!props.userId) { error.value = 'User ID is missing.'; return; }

    loading.value = true;
    error.value   = '';

    try {
        const response = await axios.post('/verify-otp', {
            otp: otpDigits.value.join(''),
            userId: props.userId
        });
        if (response.data.redirect) window.location.href = response.data.redirect;
        else window.location.href = '/dashboard';
    } catch (err) {
        error.value = err.response?.data?.message || 'Invalid code. Please try again.';
        otpDigits.value = Array(6).fill('');
        inputRefs.value[0]?.focus();
        loading.value = false;
    }
};

const handleResend = () => {
    timer.value = props.cooldownPeriod || props.initialTimer;
    startTimer();
    emit('resend');
    error.value = '';
    otpDigits.value = Array(6).fill('');
    inputRefs.value[0]?.focus();
};

const formatTime = (s) => `${Math.floor(s / 60)}:${(s % 60).toString().padStart(2, '0')}`;

const startTimer = () => {
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        if (timer.value > 0) timer.value--;
        else clearInterval(timerInterval);
    }, 1000);
};

onMounted(() => { inputRefs.value[0]?.focus(); startTimer(); });
onUnmounted(() => clearInterval(timerInterval));
</script>

<template>
    <div class="ep-otp-root">

        <!-- Left brand panel -->
        <div class="ep-otp-left">
            <div class="ep-otp-blob1"></div>
            <div class="ep-otp-blob2"></div>
            <div class="ep-accent-bar"></div>
            <div class="ep-otp-left-body">
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
                <div class="ep-otp-hero">
                    <div class="ep-otp-shield">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h2 class="ep-otp-title">Two-step<br/>verification</h2>
                    <p class="ep-otp-sub">
                        We sent a 6-digit code to<br/>
                        <strong>{{ maskedEmail || maskedPhone }}</strong>
                    </p>
                </div>

                <!-- Footer -->
                <div class="ep-left-footer">
                    <div class="ep-foot-dot"></div>
                    <span>All systems operational · Dar es Salaam, TZ</span>
                </div>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="ep-otp-right">
            <div class="ep-accent-bar"></div>
            <div class="ep-otp-right-body">

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
                    <div class="ep-eyebrow">Verification</div>
                    <h1 class="ep-title">Enter your code</h1>
                    <p class="ep-subtitle">
                        Sent to <strong>{{ maskedEmail || maskedPhone }}</strong>
                    </p>
                </div>

                <!-- Message banner -->
                <div v-if="message" class="ep-banner">{{ message }}</div>

                <form @submit.prevent="handleSubmit" class="ep-otp-form">

                    <!-- 6 OTP boxes -->
                    <div class="ep-otp-boxes">
                        <template v-for="i in 6" :key="i">
                            <input
                                :ref="el => inputRefs[i - 1] = el"
                                v-model="otpDigits[i - 1]"
                                @input="handleInput($event, i - 1)"
                                @keydown="handleKeydown($event, i - 1)"
                                @paste="handlePaste"
                                type="text"
                                inputmode="numeric"
                                maxlength="1"
                                :class="['ep-otp-box', { 'ep-otp-box--err': error, 'ep-otp-box--filled': otpDigits[i-1] }]"
                            />
                        </template>
                    </div>

                    <!-- Error -->
                    <p v-if="error" class="ep-err-msg">{{ error }}</p>

                    <!-- Resend timer -->
                    <div class="ep-resend">
                        <span v-if="timer > 0" class="ep-timer">
                            Resend in <strong>{{ formatTime(timer) }}</strong>
                        </span>
                        <button v-else type="button" @click="handleResend" class="ep-resend-btn">
                            Resend Code
                        </button>
                        <span v-if="remainingAttempts <= props.maxResendAttempts" class="ep-attempts">
                            {{ remainingAttempts }} attempt{{ remainingAttempts !== 1 ? 's' : '' }} left
                        </span>
                    </div>

                    <!-- Submit -->
                    <button type="submit" :disabled="!isComplete || loading" class="ep-btn">
                        <svg v-if="loading" class="ep-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                        {{ loading ? 'Verifying...' : 'Verify Code' }}
                    </button>

                    <!-- Back -->
                    <div class="ep-back">
                        <Link :href="route('login')" class="ep-back-link">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            Back to login
                        </Link>
                    </div>

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

/* Spinner */
@keyframes ep-spin { to { transform: rotate(360deg); } }
.ep-spin { animation: ep-spin .8s linear infinite; }

/* Root */
.ep-otp-root {
    display: flex;
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
}

/* Accent bar */
.ep-accent-bar {
    height: 3px;
    background: linear-gradient(90deg, #C0170F, #F05A00, #F9B233, #1D5C96);
    flex-shrink: 0;
}

/* ── LEFT PANEL ── */
.ep-otp-left {
    display: none;
    flex-direction: column;
    width: 48%;
    background: #C0170F;
    position: relative;
    overflow: hidden;
}
@media (min-width: 1024px) { .ep-otp-left { display: flex; } }

.ep-otp-blob1 {
    position: absolute; top: -100px; right: -80px;
    width: 400px; height: 400px; border-radius: 50%;
    background: rgba(240,90,0,.3); filter: blur(70px);
    pointer-events: none;
}
.ep-otp-blob2 {
    position: absolute; bottom: -60px; left: -60px;
    width: 300px; height: 300px; border-radius: 50%;
    background: rgba(249,178,51,.2); filter: blur(60px);
    pointer-events: none;
}

.ep-otp-left-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 40px 48px;
    position: relative;
    z-index: 1;
}

/* Logo */
.ep-logo { display: flex; align-items: center; gap: 14px; margin-bottom: auto; }
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
.ep-otp-hero {
    margin: auto 0;
    padding: 48px 0 40px;
    text-align: center;
}
.ep-otp-shield {
    width: 72px; height: 72px;
    background: rgba(255,255,255,.15);
    border: 1.5px solid rgba(255,255,255,.25);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px;
}
.ep-otp-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(30px, 3.5vw, 44px);
    font-weight: 900;
    color: #fff; line-height: 1.15;
    margin: 0 0 14px;
}
.ep-otp-sub {
    font-size: 15px;
    color: rgba(255,255,255,.65);
    line-height: 1.6;
}
.ep-otp-sub strong { color: rgba(255,255,255,.9); }

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
.ep-otp-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #F7F5F2;
}
.ep-otp-right-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 32px;
}

/* Mobile logo */
.ep-mobile-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 36px; }
.ep-mobile-icon {
    width: 42px; height: 42px;
    background: #C0170F; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}
.ep-mobile-name { font-family: 'Playfair Display', serif; font-size: 20px; color: #1A1410; }
.ep-mobile-name b { color: #C0170F; }
@media (min-width: 1024px) { .ep-mobile-logo { display: none; } }

/* Form head */
.ep-form-head { width: 100%; max-width: 400px; margin-bottom: 28px; }
.ep-eyebrow {
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: .22em;
    color: #C0170F; text-transform: uppercase; margin-bottom: 8px;
}
.ep-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(26px, 4vw, 34px);
    font-weight: 900; color: #1A1410;
    line-height: 1.1; margin: 0 0 6px;
}
.ep-subtitle { font-size: 14px; color: #6B6560; margin: 0; }
.ep-subtitle strong { color: #1A1410; }

/* Banner */
.ep-banner {
    width: 100%; max-width: 400px;
    background: rgba(29,92,150,.08);
    border-left: 3px solid #1D5C96;
    border-radius: 8px;
    padding: 11px 14px;
    font-size: 13px; color: #1D5C96;
    margin-bottom: 20px;
}

/* OTP form */
.ep-otp-form { width: 100%; max-width: 400px; }

/* OTP boxes */
.ep-otp-boxes {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-bottom: 16px;
}
.ep-otp-box {
    width: 52px; height: 58px;
    text-align: center;
    font-size: 22px; font-weight: 700;
    font-family: 'DM Mono', monospace;
    color: #1A1410;
    background: #fff;
    border: 1.5px solid #E8E2DA;
    border-radius: 14px;
    outline: none;
    transition: border-color .18s, box-shadow .18s, transform .18s;
    caret-color: #C0170F;
}
.ep-otp-box:focus {
    border-color: #C0170F;
    box-shadow: 0 0 0 3px rgba(192,23,15,.12);
    transform: translateY(-2px);
}
.ep-otp-box--filled { border-color: #C0170F; background: rgba(192,23,15,.04); }
.ep-otp-box--err { border-color: #C0170F !important; background: rgba(192,23,15,.06) !important; }

/* Suppress spinners */
.ep-otp-box::-webkit-outer-spin-button,
.ep-otp-box::-webkit-inner-spin-button { -webkit-appearance: none; }

/* Error */
.ep-err-msg {
    font-family: 'DM Mono', monospace;
    font-size: 11px; color: #C0170F;
    text-align: center; margin-bottom: 12px;
}

/* Resend */
.ep-resend {
    display: flex; align-items: center; justify-content: center;
    gap: 12px; margin-bottom: 22px;
    font-size: 13px; color: #6B6560;
}
.ep-timer { font-family: 'DM Mono', monospace; font-size: 12px; }
.ep-timer strong { color: #C0170F; }
.ep-resend-btn {
    font-family: 'DM Mono', monospace;
    font-size: 11px; letter-spacing: .06em;
    color: #C0170F; background: none;
    border: none; cursor: pointer; padding: 0;
    text-decoration: underline; text-underline-offset: 3px;
}
.ep-resend-btn:hover { opacity: .7; }
.ep-attempts {
    font-family: 'DM Mono', monospace;
    font-size: 10px; color: #9E9890;
    letter-spacing: .06em;
}

/* Submit button */
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
    margin-bottom: 16px;
}
.ep-btn::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, #F05A00, #C0170F);
    opacity: 0; transition: opacity .22s;
}
.ep-btn:hover::after { opacity: 1; }
.ep-btn:hover { box-shadow: 0 6px 24px rgba(192,23,15,.45); transform: translateY(-1px); }
.ep-btn:disabled { opacity: .4; cursor: not-allowed; transform: none; }
.ep-btn > * { position: relative; z-index: 1; }

/* Back link */
.ep-back { text-align: center; padding-top: 14px; border-top: 1px solid #E8E2DA; }
.ep-back-link {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; color: #6B6560; text-decoration: none;
    font-family: 'DM Sans', sans-serif;
    transition: color .2s;
}
.ep-back-link:hover { color: #C0170F; }

/* Form footer */
.ep-form-footer {
    width: 100%; max-width: 400px;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid #E8E2DA;
    display: flex; gap: 8px;
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: .08em;
    color: #9E9890;
}
</style>