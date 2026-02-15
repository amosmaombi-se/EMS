<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header with EVENTS theme -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-8 text-center">
        <div class="flex justify-center mb-4">
          <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
        </div>
        <h2 class="text-2xl font-bold text-white mb-2">Verify Your Identity</h2>
        <p class="text-blue-100 text-sm">
          We sent a verification code to {{ maskedPhone }}
        </p>
      </div>

      <!-- OTP Form -->
      <form @submit.prevent="handleSubmit" class="mt-8 space-y-6 bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <div class="space-y-6">
          <!-- OTP Input Fields -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-3 text-center">
              Enter the 6-digit code *
            </label>
            <div class="flex gap-3 justify-center">
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
                  class="w-12 h-12 text-center text-lg font-semibold rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all"
                  :class="{
                    'border-red-300 focus:border-red-500 focus:ring-red-500': error,
                    'border-gray-300': !error
                  }" 
                />
              </template>
            </div>
            
            <p v-if="error" class="text-red-600 text-xs text-center mt-3">
              {{ error }}
            </p>
          </div>

          <!-- Resend Code Section -->
          <div class="text-center">
            <p v-if="timer > 0" class="text-sm text-gray-600">
              Resend code in <span class="font-semibold text-blue-600">{{ formatTime(timer) }}</span>
            </p>
            <button 
              v-else 
              @click="handleResend" 
              type="button"
              class="text-blue-500 hover:text-blue-600 text-sm font-medium transition-colors duration-200"
            >
              Resend Verification Code
            </button>
          </div>
        </div>

        <!-- Verify Button -->
        <button 
          type="submit" 
          :disabled="!isComplete || loading"
          class="w-full flex justify-center items-center py-2.5 px-6 rounded-lg text-white font-medium bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-md shadow-blue-500/20"
        >
          <template v-if="loading">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
            Verifying...
          </template>
          <template v-else>
            <span>Verify Code</span>
          </template>
        </button>

        <!-- Back to Login Link -->
        <div class="text-center pt-4 border-t border-gray-100">
          <Link :href="route('login')" class="inline-flex items-center text-sm text-blue-500 hover:text-blue-600 font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to login
          </Link>
        </div>
      </form>

      <!-- Additional Help Text -->
      <div class="bg-blue-50 rounded-xl border border-blue-100 p-4">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="text-xs text-blue-600">
              <strong>Didn't receive the code?</strong> Check your spam folder or wait {{ Math.floor(props.initialTimer / 60) }} minutes to request a new one.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

// Props
const props = defineProps({
  maskedPhone: {
    type: String,
    default: '***-***-1234'
  },
  initialTimer: {
    type: Number,
    default: 300
  },
  userId: {
    type: Number,
  }
});

const emit = defineEmits(['verified', 'resend']);

const otpDigits = ref(Array(6).fill(''));
const inputRefs = ref([]);
const error = ref('');
const loading = ref(false);
const timer = ref(props.initialTimer);
let timerInterval;

const isComplete = computed(() => {
  return otpDigits.value.every(digit => digit !== '') &&
    otpDigits.value.every(digit => /^[0-9]$/.test(digit));
});

const handleInput = (event, index) => {
  const value = event.target.value;
  if (!/^[0-9]$/.test(value)) {
    otpDigits.value[index] = '';
    return;
  }
  if (index < 5 && value) {
    inputRefs.value[index + 1]?.focus();
  }
  error.value = '';
};

const handleKeydown = (event, index) => {
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    inputRefs.value[index - 1]?.focus();
  }
};

const handlePaste = (event) => {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text');
  const numbers = pastedData.match(/\d/g);

  if (numbers && numbers.length > 0) {
    numbers.slice(0, 6).forEach((number, index) => {
      otpDigits.value[index] = number;
    });
    inputRefs.value[Math.min(numbers.length, 5)]?.focus();
  }
};

const handleSubmit = async () => {
  if (!isComplete.value) return;
  
  const otpValue = otpDigits.value.join('').trim();
  const userId = props?.userId;

  if (!userId) {
    error.value = 'User ID is missing.';
    return;
  }

  if (otpValue.length !== 6 || isNaN(otpValue)) {
    error.value = 'Invalid OTP. Please enter a 6-digit code.';
    return;
  }
  
  loading.value = true;
  error.value = '';
  
  try {
    const response = await axios.post('/verify-otp', { otp: otpValue, userId });
    console.log('OTP Verified:', response.data);
    
    if (response.data.redirect) {
      window.location.href = response.data.redirect;
    } else {
      window.location.href = '/dashboard'; 
    }
  } catch (err) {
    console.error('Verification failed:', err.response?.data || err.message);
    error.value = err.response?.data?.message || 'Invalid verification code. Please try again.';
    
    // Clear OTP fields on error
    otpDigits.value = Array(6).fill('');
    inputRefs.value[0]?.focus();
    
    loading.value = false;
  }
};

const handleResend = () => {
  timer.value = props.initialTimer;
  startTimer();
  emit('resend');
  error.value = '';
  
  // Clear OTP fields
  otpDigits.value = Array(6).fill('');
  inputRefs.value[0]?.focus();
};

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const startTimer = () => {
  timerInterval = setInterval(() => {
    if (timer.value > 0) {
      timer.value--;
    } else {
      clearInterval(timerInterval);
    }
  }, 1000);
};

onMounted(() => {
  inputRefs.value[0]?.focus();
  startTimer();
});

onUnmounted(() => {
  clearInterval(timerInterval);
});
</script>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] {
  -moz-appearance: textfield;
}

/* OTP input styling */
input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  transition: all 0.2s ease;
}

/* Smooth number animation for timer */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.timer-pulse {
  animation: pulse 2s infinite;
}
</style>