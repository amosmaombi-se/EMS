<script setup>
import OtpLogin from '@/Components/OtpLogin.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    userId:            { type: Number, required: true },
    email:             { type: String, required: true },
    phone:             { type: String, required: true },
    notificationStatus:{ type: Object, required: true },
    message:           { type: String, required: true },
    cooldownPeriod:    { type: Number, required: true },
    maxResendAttempts: { type: Number, required: true },
    remainingAttempts: { type: Number, default: 3 }
});

const loading          = ref(false);
const error            = ref(null);
const message          = ref(props.message);
const remainingAttempts = ref(props.remainingAttempts);
const cooldown         = ref(props.cooldownPeriod);

const handleResend = async () => {
    if (loading.value || remainingAttempts.value <= 0) return;

    loading.value = true;
    error.value   = null;

    try {
        const response = await axios.post('/resend-otp', {
            userId:    props.userId,
            loginType: props.email ? 'email' : 'phone'
        });
        message.value          = response.data.message;
        remainingAttempts.value = response.data.remainingAttempts;
        cooldown.value         = response.data.cooldownPeriod;
    } catch (err) {
        error.value = err.response?.data?.errors?.otp?.[0] || 'Failed to resend OTP. Try again later.';
    } finally {
        loading.value = false;
    }
};

const handleVerified = () => {
    window.location.href = '/dashboard';
};
</script>

<template>
    <Head title="Verify — Event Portal" />

    <OtpLogin
        :userId="userId"
        :masked-phone="phone"
        :masked-email="email"
        :notification-status="notificationStatus"
        :message="message"
        :cooldown-period="cooldownPeriod"
        :max-resend-attempts="maxResendAttempts"
        :remaining-attempts="remainingAttempts"
        @verified="handleVerified"
        @resend="handleResend"
    />
</template>