<template>
    <!-- Invitation Card Preview Component -->
    <div class="invitation-card-wrapper">
        <div class="invitation-card">
            <!-- Top Decorative Section with Couple Photo -->
            <div class="card-header" :style="headerStyle">
                <!-- Floral Decoration (Top Left) -->
                <div class="floral-decoration top-left">
                    <svg class="w-24 h-24 text-pink-300 opacity-40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                    </svg>
                </div>

                <!-- Floral Decoration (Top Right) -->
                <div class="floral-decoration top-right">
                    <svg class="w-24 h-24 text-pink-300 opacity-40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                    </svg>
                </div>

                <!-- Parents Names -->
                <div class="parents-names">
                    <p class="text-xs text-gray-600 mb-1">{{ form.groomParents || 'Familia ya PRAT W. MASSAWE' }}</p>
                    <p class="text-xs text-gray-600">{{ form.brideParents || 'Vs MACHAME KISHI ERNEST na MAMA LUCY ERNEST' }}</p>
                </div>

                <!-- Hosts -->
                <div class="hosts-section">
                    <p class="text-sm font-bold text-gray-700 mb-1">{{ form.hostName || 'PATRICK SANGE' }}</p>
                    <p class="text-xs text-gray-600">{{ form.hostRelation || 'Karibu HARUSI' }}</p>
                    <p class="text-xs text-gray-600">{{ form.hostMessage || 'Ya Vijana Wao Wapendwa' }}</p>
                </div>

                <!-- Couple Names (Large Script Font) -->
                <div class="couple-names">
                    <h1 class="couple-script">
                        {{ form.groomName || 'Joel' }} <span class="ampersand">&</span> {{ form.brideName || 'Gloria' }}
                    </h1>
                </div>

                <!-- Couple Photo -->
                <div class="couple-photo" v-if="imagePreview">
                    <img :src="imagePreview" alt="Couple" class="couple-image">
                </div>
                <div v-else class="couple-photo-placeholder">
                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-xs text-gray-400 mt-2">Upload couple photo</p>
                </div>

                <!-- Date Banner -->
                <div class="date-banner">
                    <div class="date-content">
                        <span class="date-day">{{ formatDay(event.event_date) || 'JUMAMOSI' }}</span>
                        <span class="date-number">{{ formatDate(event.event_date) || '14' }}</span>
                        <span class="date-month">{{ formatMonth(event.event_date) || 'FEBRUARY' }}</span>
                    </div>
                    <div class="date-year">{{ formatYear(event.event_date) || '2026' }}</div>
                </div>
            </div>

            <!-- Event Details Section -->
            <div class="card-body">
                <!-- Church Service -->
                <div class="event-detail">
                    <p class="detail-label">IBADA YA NDOA:</p>
                    <p class="detail-value">{{ form.churchName || 'Kanisa la SDA Sewgi Garden' }}</p>
                    <p class="detail-sub">{{ form.churchLocation || '(Mt Wa Kweli)' }}</p>
                    <p class="detail-time">{{ form.churchTime || 'MKUTANO SAA 3 JIONI, KAZALI SAA 4 JIONI' }}</p>
                </div>

                <!-- Reception -->
                <div class="event-detail">
                    <p class="detail-label">TAFRIJA:</p>
                    <p class="detail-value">{{ form.receptionVenue || 'Uwanja wa Sewgi Garden - Moshi Kibo' }}</p>
                    <p class="detail-time">{{ form.receptionTime || 'Mtalch Kuanzia Saa 12 Jioni.' }}</p>
                </div>

                <!-- Dress Code -->
                <div class="event-detail dress-code">
                    <p class="detail-label">RANGI ZA SHEREHE</p>
                    <div class="color-circles">
                        <span class="color-circle red" title="Red"></span>
                        <span class="color-circle white" title="White"></span>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="contact-section">
                    <p class="detail-label">DOUBLE</p>
                    <p class="contact-number">{{ form.contactNumber || '+255 765 979 797' }}</p>
                    <p class="contact-note">{{ form.contactNote || 'NB: Warahisi tunavyowajnbia kaum hivyo!watutumia!' }}</p>
                </div>

                <!-- QR Code Section -->
                <div class="qr-code-section">
                    <div class="qr-code-wrapper">
                        <div v-if="qrCodeUrl" class="qr-code">
                            <img :src="qrCodeUrl" alt="RSVP QR Code" class="qr-image">
                        </div>
                        <div v-else class="qr-placeholder">
                            <div class="qr-grid">
                                <div class="qr-square"></div>
                                <div class="qr-square"></div>
                                <div class="qr-square"></div>
                                <div class="qr-square"></div>
                                <div class="qr-square"></div>
                                <div class="qr-square"></div>
                                <div class="qr-square"></div>
                                <div class="qr-square"></div>
                                <div class="qr-square"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">QR Code</p>
                        </div>
                    </div>
                    <!-- Floral Decoration (Bottom Right) -->
                    <div class="floral-decoration bottom-right">
                        <svg class="w-20 h-20 text-pink-300 opacity-40" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    event: Object,
    form: Object,
    imagePreview: String,
    qrCodeUrl: String
})

const headerStyle = computed(() => {
    if (props.imagePreview) {
        return {
            backgroundImage: `linear-gradient(to bottom, rgba(255, 240, 245, 0.95), rgba(255, 240, 245, 0.98)), url(${props.imagePreview})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center'
        }
    }
    return {
        background: 'linear-gradient(135deg, #FFF5F7 0%, #FFE4E9 100%)'
    }
})

const formatDay = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('sw-TZ', { weekday: 'long' }).toUpperCase()
}

const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).getDate()
}

const formatMonth = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('en-US', { month: 'long' }).toUpperCase()
}

const formatYear = (date) => {
    if (!date) return ''
    return new Date(date).getFullYear()
}
</script>

<style scoped>
.invitation-card-wrapper {
    @apply w-full h-full flex items-center justify-center p-4;
}

.invitation-card {
    @apply w-full max-w-md bg-white rounded-2xl overflow-hidden shadow-2xl border-4 border-pink-100;
}

/* Header Section */
.card-header {
    @apply relative px-6 py-8 text-center;
    min-height: 400px;
}

.floral-decoration {
    @apply absolute;
}

.floral-decoration.top-left {
    @apply -top-6 -left-6;
    transform: rotate(-45deg);
}

.floral-decoration.top-right {
    @apply -top-6 -right-6;
    transform: rotate(45deg);
}

.floral-decoration.bottom-right {
    @apply absolute -bottom-4 -right-4;
    transform: rotate(25deg);
}

.parents-names {
    @apply text-center mb-4;
}

.hosts-section {
    @apply mb-6;
}

.couple-names {
    @apply mb-6;
}

.couple-script {
    font-family: 'Great Vibes', 'Pacifico', cursive;
    @apply text-5xl font-bold text-pink-600;
    line-height: 1.2;
}

.ampersand {
    @apply text-6xl mx-2;
}

.couple-photo {
    @apply relative w-48 h-48 mx-auto mb-6 rounded-lg overflow-hidden shadow-xl;
}

.couple-image {
    @apply w-full h-full object-cover;
}

.couple-photo-placeholder {
    @apply relative w-48 h-48 mx-auto mb-6 rounded-lg overflow-hidden bg-gray-100 flex flex-col items-center justify-center border-2 border-dashed border-gray-300;
}

.date-banner {
    @apply relative bg-white bg-opacity-90 backdrop-blur-sm rounded-xl shadow-lg px-6 py-3 mx-auto;
    max-width: 280px;
}

.date-content {
    @apply flex items-center justify-center gap-3 text-gray-800;
}

.date-day {
    @apply text-xs font-bold uppercase;
}

.date-number {
    @apply text-4xl font-black;
}

.date-month {
    @apply text-xs font-bold uppercase;
}

.date-year {
    @apply text-center text-2xl font-black text-gray-800 mt-1;
}

/* Body Section */
.card-body {
    @apply bg-gradient-to-b from-gray-800 to-gray-900 text-white px-6 py-8;
}

.event-detail {
    @apply mb-6;
}

.detail-label {
    @apply text-sm font-bold mb-2 text-pink-300;
}

.detail-value {
    @apply text-base font-semibold mb-1;
}

.detail-sub {
    @apply text-sm text-gray-300 mb-1;
}

.detail-time {
    @apply text-sm text-gray-400;
}

/* Dress Code */
.dress-code {
    @apply flex items-center gap-4;
}

.color-circles {
    @apply flex gap-2;
}

.color-circle {
    @apply w-6 h-6 rounded-full border-2 border-white shadow-lg;
}

.color-circle.red {
    @apply bg-red-600;
}

.color-circle.white {
    @apply bg-white;
}

/* Contact Section */
.contact-section {
    @apply mb-6;
}

.contact-number {
    @apply text-lg font-bold text-pink-300 mb-2;
}

.contact-note {
    @apply text-xs text-gray-400 italic;
}

/* QR Code Section */
.qr-code-section {
    @apply relative flex justify-end items-end pt-4;
}

.qr-code-wrapper {
    @apply relative z-10;
}

.qr-code {
    @apply bg-white p-2 rounded-lg shadow-xl;
}

.qr-image {
    @apply w-24 h-24;
}

.qr-placeholder {
    @apply bg-white p-3 rounded-lg shadow-xl text-center;
    width: 96px;
}

.qr-grid {
    @apply grid grid-cols-3 gap-1 mb-2;
}

.qr-square {
    @apply w-6 h-6 bg-gray-800 rounded-sm;
}

/* Responsive */
@media (max-width: 640px) {
    .couple-script {
        @apply text-4xl;
    }
    
    .ampersand {
        @apply text-5xl;
    }
    
    .couple-photo,
    .couple-photo-placeholder {
        @apply w-40 h-40;
    }
    
    .date-banner {
        @apply px-4 py-2;
    }
    
    .date-number {
        @apply text-3xl;
    }
}

/* Add Google Fonts for script font */
@import url('https://fonts.googleapis.com/css2?family=Great+Vibes&family=Pacifico&display=swap');
</style>