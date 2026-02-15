<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: false
    },
    status: {
        type: String,
        default: null
    },
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    errors: Object,
    username: String
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
                    text: errors.username || errors.password || errors.auth || 'Invalid credentials. Please check your username and password.',
                    icon: 'error',
                    confirmButtonColor: '#3B82F6',
                    confirmButtonText: 'Try Again'
                });
            }
        }
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="flex min-h-screen bg-white">
        <!-- Left side - Illustration -->
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
                    
                    <!-- Calendar/Event icon large -->
                    <g transform="translate(200, 150)">
                        <rect x="0" y="20" width="200" height="180" rx="12" fill="#3B82F6" />
                        <rect x="0" y="0" width="200" height="40" rx="12" fill="#2563EB" />
                        <!-- Calendar holes -->
                        <circle cx="40" cy="10" r="5" fill="#DBEAFE" />
                        <circle cx="160" cy="10" r="5" fill="#DBEAFE" />
                        
                        <!-- Calendar grid -->
                        <rect x="20" y="60" width="35" height="30" rx="4" fill="#DBEAFE" />
                        <rect x="65" y="60" width="35" height="30" rx="4" fill="#DBEAFE" />
                        <rect x="110" y="60" width="35" height="30" rx="4" fill="#DBEAFE" />
                        <rect x="155" y="60" width="35" height="30" rx="4" fill="#DBEAFE" />
                        
                        <rect x="20" y="100" width="35" height="30" rx="4" fill="#EFF6FF" />
                        <rect x="65" y="100" width="35" height="30" rx="4" fill="#EFF6FF" />
                        <rect x="110" y="100" width="35" height="30" rx="4" fill="#60A5FA" />
                        <rect x="155" y="100" width="35" height="30" rx="4" fill="#EFF6FF" />
                        
                        <rect x="20" y="140" width="35" height="30" rx="4" fill="#EFF6FF" />
                        <rect x="65" y="140" width="35" height="30" rx="4" fill="#EFF6FF" />
                        <rect x="110" y="140" width="35" height="30" rx="4" fill="#EFF6FF" />
                        <rect x="155" y="140" width="35" height="30" rx="4" fill="#EFF6FF" />
                    </g>
                    
                    <!-- Floating elements -->
                    <!-- People icons -->
                    <g transform="translate(100, 100)">
                        <circle cx="0" cy="0" r="20" fill="#A78BFA" />
                        <circle cx="0" cy="-8" r="8" fill="#8B5CF6" />
                    </g>
                    
                    <g transform="translate(480, 180)">
                        <circle cx="0" cy="0" r="18" fill="#F472B6" />
                        <circle cx="0" cy="-7" r="7" fill="#EC4899" />
                    </g>
                    
                    <g transform="translate(120, 380)">
                        <circle cx="0" cy="0" r="16" fill="#34D399" />
                        <circle cx="0" cy="-6" r="6" fill="#10B981" />
                    </g>
                    
                    <!-- Decorative shapes -->
                    <circle cx="480" cy="350" r="25" fill="#FCD34D" opacity="0.6" />
                    <rect x="90" y="280" width="30" height="30" rx="6" fill="#F472B6" opacity="0.4" transform="rotate(15 105 295)" />
                    <circle cx="450" cy="100" r="20" fill="#60A5FA" opacity="0.5" />
                    
                    <!-- Connection lines -->
                    <line x1="100" y1="100" x2="250" y2="180" stroke="#93C5FD" stroke-width="2" stroke-dasharray="5,5" opacity="0.5" />
                    <line x1="480" y1="180" x2="380" y2="200" stroke="#F9A8D4" stroke-width="2" stroke-dasharray="5,5" opacity="0.5" />
                </svg>
            </div>

            <!-- Bottom decoration -->
            <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-blue-100 to-transparent"></div>
        </div>

        <!-- Right side - Login form -->
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

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                    <p class="text-gray-500">
                        Sign in to your account to continue
                    </p>
                </div>

                <div v-if="status" class="mb-6 text-sm text-blue-600 bg-blue-50 p-4 rounded-xl border border-blue-100">
                    {{ status }}
                </div>
                
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="username" class="block text-xs font-medium text-gray-700 mb-1">
                            Username *
                        </label>
                        <input 
                            id="username" 
                            name="username"
                            type="text" 
                            v-model="form.username"
                            required
                            class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.username }"
                            placeholder="Enter your username" 
                        />
                        <InputError :message="form.errors.username" class="mt-1 text-xs" />
                    </div>
                    
                    <div>
                        <label for="password" class="block text-xs font-medium text-gray-700 mb-1">
                            Password *
                        </label>
                        <input 
                            id="password" 
                            name="password"
                            type="password" 
                            v-model="form.password"
                            required
                            class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.password }"
                            placeholder="Enter your password" 
                        />
                        <InputError :message="form.errors.password" class="mt-1 text-xs" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input 
                                id="remember-me" 
                                name="remember-me" 
                                type="checkbox"
                                v-model="form.remember"
                                class="h-4 w-4 text-blue-500 focus:ring-blue-500 border-gray-300 rounded" 
                            />
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm text-blue-500 hover:text-blue-600 font-medium">
                            Forgot password?
                        </Link>
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
                            Signing in...
                        </span>
                        <span v-else>Sign In</span>
                    </button>

                    <!-- <p class="text-center text-sm text-gray-600 mt-6">
                        Don't have an account? 
                        <Link :href="route('register')" v-if="canRegister" class="text-blue-500 hover:text-blue-600 font-semibold">
                            Create account
                        </Link>
                    </p> -->
                </form>

             
            </div>
        </div>
    </div>
</template>

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