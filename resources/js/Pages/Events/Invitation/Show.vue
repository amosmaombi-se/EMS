<template>
    <div class="page-bg">

        <!-- Confetti -->
        <div class="confetti" aria-hidden="true">
            <div class="cdot" v-for="n in 18" :key="n" :style="{
                width:(4+(n*3)%9)+'px', height:(4+(n*3)%9)+'px', left:(n*5.55%100)+'%',
                background:['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233'][n%6],
                animationDuration:(10+n*1.05)+'s', animationDelay:(n*.45)+'s',
                borderRadius:n%3===0?'3px':'50%',
            }"></div>
        </div>

        <div class="page-inner">

            <!-- ── Invitation Card ── -->
            <div class="inv-card">

                <!-- Brand accent bar -->
                <div class="accent-bar"></div>

                <!-- Hero -->
                <div class="hero">
                    <!-- Uploaded image sits behind gradient overlay -->
                    <img v-if="hasImage" :src="imageUrl" class="hero-img" alt="">
                    <div class="hero-overlay"></div>

                    <!-- Hero content -->
                    <div class="hero-content">
                        <div class="hero-badge">🎉 You're Invited</div>
                        <h1 class="hero-title">{{ event.title }}</h1>
                        <div class="hero-meta-row">
                            <div class="hero-meta">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                {{ formatDate(event.event_date) }}
                            </div>
                            <div v-if="event.start_time" class="hero-meta">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                {{ formatTime(event.start_time) }}
                                <span v-if="event.end_time"> — {{ formatTime(event.end_time) }}</span>
                            </div>
                            <div v-if="event.city" class="hero-meta">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ event.city }}<span v-if="event.state">, {{ event.state }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body">

                    <!-- Greeting -->
                    <div class="greeting">
                        <div class="greeting-salute">Dear {{ guest.first_name }} {{ guest.last_name }},</div>
                        <p class="greeting-text">We would be honoured to have you join us for this special occasion.</p>
                    </div>

                    <!-- QR Section -->
                    <div class="qr-section">
                        <div class="qr-icon-wrap">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h.01M18 14h.01M14 18h.01M18 18h.01M14 14v4M18 14v4"/></svg>
                        </div>
                        <div class="qr-text">
                            <div class="qr-title">Your Digital Pass</div>
                            <div class="qr-sub">Show this code at the entrance for quick check-in</div>
                        </div>

                        <div class="qr-code-wrap">
                            <img v-if="qrCode && qrCode.startsWith('data:image')" :src="qrCode" alt="QR Code" class="qr-code-img">
                            <div v-else-if="qrCode" v-html="qrCode" class="qr-code-svg"></div>
                            <div v-else class="qr-placeholder">
                                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" opacity=".35"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h.01M18 14h.01M14 18h.01M18 18h.01M14 14v4M18 14v4"/></svg>
                            </div>
                        </div>

                        <div class="qr-guest-id">Guest ID: <span class="mono">#{{ guest.id }}</span></div>

                        <button @click="downloadQr" class="btn-download">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            Download QR Code
                        </button>
                    </div>

                    <!-- RSVP Section -->
                    <div class="rsvp-section">
                        <div class="section-head">
                            <div class="section-title">Will you be attending?</div>
                        </div>

                        <div v-if="!rsvpSubmitted">
                            <!-- RSVP chips -->
                            <div class="rsvp-chips">
                                <div @click="selectedRsvp='attending'"
                                     :class="['rsvp-chip chip-yes', selectedRsvp==='attending'?'chip-yes-on':'']">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Yes, I'll be there!</span>
                                </div>
                                <div @click="selectedRsvp='not_attending'"
                                     :class="['rsvp-chip chip-no', selectedRsvp==='not_attending'?'chip-no-on':'']">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Can't make it</span>
                                </div>
                                <div @click="selectedRsvp='maybe'"
                                     :class="['rsvp-chip chip-maybe', selectedRsvp==='maybe'?'chip-maybe-on':'']">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Maybe</span>
                                </div>
                            </div>

                            <!-- Plus ones -->
                            <div v-if="guest.plus_ones > 0 && selectedRsvp==='attending'" class="plus-ones-field">
                                <label class="field-label">How many additional guests will you bring? <span class="max-label">(max {{ guest.plus_ones }})</span></label>
                                <input v-model.number="plusOnesCount" type="number" :min="0" :max="guest.plus_ones" class="ep-input" style="max-width:120px">
                            </div>

                            <button @click="submitRsvp" :disabled="!selectedRsvp||submitting" class="btn-rsvp">
                                <svg v-if="submitting" class="spin-ico" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                {{ submitting ? 'Submitting…' : 'Confirm RSVP' }}
                            </button>
                        </div>

                        <!-- Confirmed state -->
                        <div v-else class="rsvp-confirmed">
                            <div class="confirmed-icon">
                                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="confirmed-title">Thank You!</div>
                            <div class="confirmed-sub">
                                {{ guest.rsvp_status==='attending'
                                    ? 'We look forward to seeing you at the event!'
                                    : 'Thank you for letting us know.' }}
                            </div>
                        </div>
                    </div>

                    <!-- Event Details accordion -->
                    <details class="details-block" open>
                        <summary class="details-summary">
                            <span class="ds-label">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                                Event Details
                            </span>
                            <svg class="ds-chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <div class="details-body">

                            <div class="detail-row">
                                <div class="detail-icon" style="background:rgba(29,92,150,.1);color:#1D5C96">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                </div>
                                <div>
                                    <div class="detail-lbl">Date &amp; Time</div>
                                    <div class="detail-val">{{ formatDate(event.event_date) }}</div>
                                    <div class="detail-sub">
                                        {{ formatTime(event.start_time) }}
                                        <span v-if="event.end_time"> — {{ formatTime(event.end_time) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="event.venue_name" class="detail-row">
                                <div class="detail-icon" style="background:rgba(192,23,15,.08);color:#C0170F">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <div>
                                    <div class="detail-lbl">Venue</div>
                                    <div class="detail-val">{{ event.venue_name }}</div>
                                    <div v-if="event.venue_address" class="detail-sub">{{ event.venue_address }}</div>
                                    <div v-if="event.city" class="detail-sub">{{ event.city }}<span v-if="event.state">, {{ event.state }}</span></div>
                                </div>
                            </div>

                            <div v-if="event.description" class="detail-row">
                                <div class="detail-icon" style="background:rgba(249,178,51,.15);color:#b45309">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <div class="detail-lbl">About This Event</div>
                                    <div class="detail-desc">{{ event.description }}</div>
                                </div>
                            </div>

                            <div v-if="guest.plus_ones > 0" class="detail-row">
                                <div class="detail-icon" style="background:rgba(22,163,74,.1);color:#16a34a">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </div>
                                <div>
                                    <div class="detail-lbl">Plus Ones</div>
                                    <div class="detail-val">You may bring {{ guest.plus_ones }} additional guest{{ guest.plus_ones!==1?'s':'' }}</div>
                                </div>
                            </div>

                        </div>
                    </details>

                    <!-- Footer -->
                    <div class="inv-footer">
                        <div class="footer-accent-bar"></div>
                        <div class="footer-brand">
                            <span class="footer-ep">Event Portal</span>
                            <span class="footer-tagline">Invite. Inform. Remind. Track.</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
    event: Object,
    guest: Object,
    qrCode: String,
})

const selectedRsvp  = ref(props.guest.rsvp_status || null)
const plusOnesCount = ref(props.guest.plus_ones || 0)
const submitting    = ref(false)
const rsvpSubmitted = ref(props.guest.rsvp_status !== 'pending')

// Image: check for a real hosted image on the event
const hasImage  = computed(() => !!(props.event.image_url || props.event.cover_image))
const imageUrl  = computed(() => props.event.image_url || props.event.cover_image || '')

const formatDate = d => {
    if (!d) return ''
    return new Date(d).toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' })
}
const formatTime = t => {
    if (!t) return ''
    const [h, m] = t.split(':').map(Number)
    const period = h >= 12 ? 'PM' : 'AM'
    return `${h%12||12}:${String(m).padStart(2,'0')} ${period}`
}

const submitRsvp = () => {
    if (!selectedRsvp.value) {
        Swal.fire({ title:'Please choose an option', icon:'info', confirmButtonColor:'#C0170F' })
        return
    }
    submitting.value = true
    router.post(route('events.rsvp.update', [props.event.id, props.guest.id]), {
        rsvp_status: selectedRsvp.value,
        plus_ones: selectedRsvp.value === 'attending' ? plusOnesCount.value : 0,
    }, {
        onSuccess: () => {
            submitting.value = false
            rsvpSubmitted.value = true
            Swal.fire({
                title: 'RSVP Confirmed! 🎉',
                text: selectedRsvp.value === 'attending' ? 'We look forward to seeing you!' : 'Thank you for letting us know.',
                icon: 'success',
                confirmButtonColor: '#C0170F',
                timer: 3000,
            })
        },
        onError: () => {
            submitting.value = false
            Swal.fire({ title:'Error', text:'Failed to submit RSVP. Please try again.', icon:'error', confirmButtonColor:'#C0170F' })
        },
    })
}

const downloadQr = () => {
    if (!props.qrCode) return
    if (props.qrCode.startsWith('data:image')) {
        const a = document.createElement('a')
        a.href = props.qrCode
        a.download = `qr-guest-${props.guest.id}.png`
        a.click()
    } else {
        const blob = new Blob([props.qrCode], { type:'image/svg+xml;charset=utf-8' })
        const url  = URL.createObjectURL(blob)
        const a    = document.createElement('a')
        a.href = url; a.download = `qr-guest-${props.guest.id}.svg`; a.click()
        URL.revokeObjectURL(url)
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

/* ── Page bg ── */
.page-bg{min-height:100vh;background:#F7F5F2;position:relative;overflow:hidden;padding:32px 16px 64px;display:flex;align-items:flex-start;justify-content:center}

/* ── Confetti ── */
.confetti{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.cdot{position:absolute;opacity:0;animation:rise linear infinite}
@keyframes rise{0%{transform:translateY(110vh) rotate(0deg);opacity:0}5%{opacity:.3}95%{opacity:.1}100%{transform:translateY(-80px) rotate(540deg);opacity:0}}

.page-inner{position:relative;z-index:1;width:100%;max-width:640px}

/* ── Card ── */
.inv-card{background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 8px 48px rgba(0,0,0,.12);border:1px solid #E8E2DA}
.accent-bar{height:4px;background:linear-gradient(90deg,#C0170F,#F05A00,#F9B233,#1D5C96)}

/* ── Hero ── */
.hero{position:relative;background:#0a0504;display:flex;flex-direction:column}
.hero-img{width:100%;max-height:640px;object-fit:contain;display:block}
.hero-overlay{position:absolute;bottom:0;left:0;right:0;height:160px;background:linear-gradient(to top,rgba(10,4,2,.95) 0%,transparent 100%);pointer-events:none}
.hero-content{position:relative;z-index:1;padding:16px 24px 22px;background:rgba(10,4,2,.82)}
.hero-badge{display:inline-flex;align-items:center;gap:6px;padding:3px 12px;background:rgba(249,178,51,.2);border:1px solid rgba(249,178,51,.45);border-radius:20px;font-family:'DM Mono',monospace;font-size:10px;font-weight:700;color:#F9B233;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px}
.hero-title{font-family:'Playfair Display',serif;font-size:clamp(18px,4vw,26px);font-weight:900;color:#fff;line-height:1.15;margin-bottom:10px;text-shadow:0 2px 16px rgba(0,0,0,.5)}
.hero-meta-row{display:flex;flex-wrap:wrap;gap:8px}
.hero-meta{display:inline-flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:11px;color:rgba(255,255,255,.8);background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:20px;padding:4px 11px}

/* ── Body ── */
.card-body{padding:20px 24px;display:flex;flex-direction:column;gap:20px;font-family:'DM Sans',sans-serif;color:#1A1410}

/* ── Greeting ── */
.greeting{border-left:3px solid #C0170F;padding-left:16px}
.greeting-salute{font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:#1A1410;margin-bottom:6px}
.greeting-text{font-size:14px;color:#6B6560;line-height:1.7}

/* ── QR Section ── */
.qr-section{background:linear-gradient(135deg,rgba(192,23,15,.05) 0%,rgba(29,92,150,.05) 100%);border:2px solid rgba(192,23,15,.15);border-radius:18px;padding:24px;text-align:center;display:flex;flex-direction:column;align-items:center;gap:10px}
.qr-icon-wrap{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#C0170F,#F05A00);display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 4px 14px rgba(192,23,15,.3)}
.qr-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#1A1410}
.qr-sub{font-size:13px;color:#6B6560;font-family:'DM Mono',monospace;max-width:280px;line-height:1.5}
.qr-code-wrap{background:#fff;padding:16px;border-radius:14px;box-shadow:0 4px 20px rgba(0,0,0,.1);border:1px solid #E8E2DA}
.qr-code-img{width:200px;height:200px;object-fit:contain;display:block}
.qr-code-svg{width:200px;height:200px;display:flex;align-items:center;justify-content:center}
.qr-placeholder{width:200px;height:200px;display:flex;align-items:center;justify-content:center;background:#F7F5F2;border-radius:8px}
.qr-guest-id{font-family:'DM Mono',monospace;font-size:12px;color:#9E9890}.mono{font-weight:700;color:#6B6560}
.btn-download{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:11px;border:none;cursor:pointer;background:#1A1410;color:#fff;font-size:12px;font-weight:700;font-family:'DM Mono',monospace;letter-spacing:.05em;transition:all .2s;box-shadow:0 3px 10px rgba(0,0,0,.18)}
.btn-download:hover{background:#C0170F;transform:translateY(-1px);box-shadow:0 5px 16px rgba(192,23,15,.3)}

/* ── RSVP ── */
.rsvp-section{background:#F7F5F2;border-radius:18px;padding:22px;border:1px solid #E8E2DA}
.section-head{margin-bottom:16px}
.section-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#1A1410;text-align:center}
.rsvp-chips{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:14px}
@media(max-width:480px){.rsvp-chips{grid-template-columns:1fr}}
.rsvp-chip{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;padding:16px 10px;border:2px solid #E8E2DA;border-radius:14px;cursor:pointer;font-size:13px;font-weight:700;font-family:'DM Sans',sans-serif;color:#6B6560;background:#fff;transition:all .2s;text-align:center;user-select:none}
.rsvp-chip:hover{transform:translateY(-2px)}
.chip-yes:hover{border-color:#16a34a;background:rgba(22,163,74,.05)}
.chip-yes-on{border-color:#16a34a;background:rgba(22,163,74,.08);color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.15);transform:translateY(-2px) scale(1.02)}
.chip-no:hover{border-color:#C0170F;background:rgba(192,23,15,.04)}
.chip-no-on{border-color:#C0170F;background:rgba(192,23,15,.06);color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.12);transform:translateY(-2px) scale(1.02)}
.chip-maybe:hover{border-color:#1D5C96;background:rgba(29,92,150,.04)}
.chip-maybe-on{border-color:#1D5C96;background:rgba(29,92,150,.07);color:#1D5C96;box-shadow:0 0 0 3px rgba(29,92,150,.15);transform:translateY(-2px) scale(1.02)}

.plus-ones-field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
.field-label{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:#6B6560;font-weight:500}
.max-label{text-transform:none;letter-spacing:0;font-size:10px;color:#9E9890}
.ep-input{padding:9px 13px;border:1.5px solid #E8E2DA;border-radius:11px;font-size:13px;font-family:'DM Sans',sans-serif;color:#1A1410;background:#fff;outline:none;transition:border-color .15s}
.ep-input:focus{border-color:#C0170F;box-shadow:0 0 0 3px rgba(192,23,15,.09)}

.btn-rsvp{width:100%;padding:13px;border-radius:13px;border:none;cursor:pointer;background-image:linear-gradient(135deg,#C0170F 0%,#F05A00 50%,#F9B233 100%);background-size:200% auto;color:#fff;font-size:15px;font-weight:700;font-family:'DM Sans',sans-serif;box-shadow:0 4px 16px rgba(192,23,15,.3);animation:shine 3s linear infinite;transition:transform .2s,box-shadow .2s;display:flex;align-items:center;justify-content:center;gap:8px}
@keyframes shine{0%{background-position:0% center}100%{background-position:200% center}}
.btn-rsvp:hover{transform:translateY(-1px);box-shadow:0 6px 22px rgba(192,23,15,.4)}
.btn-rsvp:disabled{opacity:.5;cursor:not-allowed;transform:none;animation:none}

.rsvp-confirmed{text-align:center;padding:24px 0}
.confirmed-icon{width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#14532d);display:flex;align-items:center;justify-content:center;color:#fff;margin:0 auto 12px;box-shadow:0 4px 16px rgba(22,163,74,.35)}
.confirmed-title{font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:#1A1410;margin-bottom:6px}
.confirmed-sub{font-size:14px;color:#6B6560}

/* ── Event Details ── */
.details-block{background:#F7F5F2;border:1px solid #E8E2DA;border-radius:18px;overflow:hidden}
.details-summary{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;cursor:pointer;list-style:none;font-family:'DM Mono',monospace;font-size:11px;letter-spacing:.12em;text-transform:uppercase;font-weight:700;color:#1A1410;background:#F0EDE8;border-bottom:1px solid #E8E2DA;user-select:none}
.details-summary::-webkit-details-marker{display:none}
.ds-label{display:flex;align-items:center;gap:7px}
.ds-chevron{transition:transform .25s}
.details-block[open] .ds-chevron{transform:rotate(180deg)}
.details-body{padding:16px;display:flex;flex-direction:column;gap:14px}
.detail-row{display:flex;align-items:flex-start;gap:12px}
.detail-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.detail-lbl{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:#9E9890;margin-bottom:2px}
.detail-val{font-size:14px;font-weight:700;color:#1A1410}
.detail-sub{font-size:13px;color:#6B6560;margin-top:1px}
.detail-desc{font-size:13px;color:#6B6560;line-height:1.7}

/* ── Footer ── */
.inv-footer{text-align:center;padding-top:8px}
.footer-accent-bar{height:3px;background:linear-gradient(90deg,#C0170F,#F05A00,#F9B233,#1D5C96);border-radius:2px;margin-bottom:16px}
.footer-brand{display:flex;flex-direction:column;align-items:center;gap:3px}
.footer-ep{font-family:'Playfair Display',serif;font-size:16px;font-weight:900;color:#1A1410}
.footer-tagline{font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:#9E9890}

.spin-ico{animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
</style>