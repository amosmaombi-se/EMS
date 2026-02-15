<template>
    <Head title="Forgot Password" />

    <div class="flex min-h-screen bg-white">
        <!-- Left side - Illustration (same as login page) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50">
            <!-- EVENTS Logo -->
            <div class="absolute top-8 left-8 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">EVENTS</h1>
                </div>
            </div>

            <!-- Main Illustration -->
            <div class="flex items-center justify-center w-full h-full p-16">
                <svg viewBox="0 0 600 500" class="w-full max-w-lg" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background circles -->
                    <circle cx="300" cy="250" r="200" fill="#EFF6FF" opacity="0.5" />
                    <circle cx="300" cy="250" r="150" fill="#DBEAFE" opacity="0.5" />
                    
                    <!-- Key/Lock Illustration -->
                    <g transform="translate(200, 150)">
                        <!-- Lock body -->
                        <rect x="70" y="100" width="60" height="80" rx="8" fill="#3B82F6" />
                        <rect x="60" y="100" width="80" height="15" rx="4" fill="#2563EB" />
                        <circle cx="100" cy="80" r="12" fill="#2563EB" />
                        
                        <!-- Key -->
                        <g transform="translate(250, 130)">
                            <rect x="0" y="20" width="40" height="8" rx="4" fill="#F59E0B" />
                            <circle cx="20" cy="24" r="12" fill="#F59E0B" />
                            <rect x="20" y="0" width="3" height="20" fill="#D97706" />
                            <rect x="15" y="5" width="10" height="3" rx="1.5" fill="#D97706" />
                            <rect x="10" y="10" width="5" height="3" rx="1.5" fill="#D97706" />
                            <rect x="25" y="15" width="7" height="3" rx="1.5" fill="#D97706" />
                        </g>
                    </g>
                    
                    <!-- Floating elements -->
                    <g transform="translate(100, 100)">
                        <circle cx="0" cy="0" r="20" fill="#A78BFA" />
                        <circle cx="0" cy="-8" r="8" fill="#8B5CF6" />
                    </g>
                    
                    <g transform="translate(480, 180)">
                        <circle cx="0" cy="0" r="18" fill="#F472B6" />
                        <circle cx="0" cy="-7" r="7" fill="#EC4899" />
                    </g>
                    
                    <!-- Decorative shapes -->
                    <circle cx="480" cy="350" r="25" fill="#FCD34D" opacity="0.6" />
                    <circle cx="450" cy="100" r="20" fill="#60A5FA" opacity="0.5" />
                    
                    <!-- Connection lines -->
                    <line x1="100" y1="100" x2="270" y2="150" stroke="#93C5FD" stroke-width="2" stroke-dasharray="5,5" opacity="0.5" />
                    <line x1="320" y1="180" x2="380" y2="150" stroke="#FCD34D" stroke-width="2" stroke-dasharray="5,5" opacity="0.5" />
                </svg>
            </div>

            <!-- Bottom decoration -->
            <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-blue-100 to-transparent"></div>
        </div>

        <!-- Right side - Forgot Password form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16">
            <div class="max-w-md w-full">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">EVENTS</h1>
                </div>

                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">Reset Your Password</h2>
                    <p class="text-gray-500 text-sm">
                        Forgot your password? No problem. Enter your email address and we'll send you a password reset link.
                    </p>
                </div>

                <!-- Back to login link -->
                <div class="mb-6">
                    <Link :href="route('login')" class="inline-flex items-center text-sm text-blue-500 hover:text-blue-600 font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to login
                    </Link>
                </div>

                <div v-if="status" class="mb-6 p-4 rounded-lg bg-green-50 border border-green-100">
                    <p class="text-sm text-green-600 font-medium">{{ status }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-700 mb-1">
                            Email Address *
                        </label>
                        <input 
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="email"
                            class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.email }"
                            placeholder="Enter your email address"
                        />
                        <p v-if="form.errors.email" class="text-red-600 text-xs mt-1">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex justify-center py-2.5 px-6 rounded-lg text-white font-medium bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-md shadow-blue-500/20"
                    >
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                        <span v-else>Send Reset Link</span>
                    </button>

                    <!-- Additional help text -->
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-xs text-blue-600">
                            <strong>Note:</strong> The password reset link will be sent to your email and will expire in 24 hours.
                        </p>
                    </div>

                   
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<style scoped>
/* Smooth animations */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Additional custom styles if needed */
input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>