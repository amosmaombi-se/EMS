<template>
    <Head title="Create User" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New User</h2>
                <Link :href="route('users.index')" 
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Users
                </Link>
            </div>
        </template>

        <div class="py-0">
            <div class="max-w-7xl mx-auto"> <!-- Reduced max width -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-white border-b border-gray-200"> <!-- Reduced padding -->
                        <!-- Success/Error Messages -->
                        <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm">
                            <div class="flex items-center text-red-800">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Please fix the following errors:</span>
                            </div>
                            <ul class="mt-1 ml-5 list-disc text-xs text-red-700">
                                <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                            </ul>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4"> <!-- Reduced spacing -->
                            <!-- Personal Information Section -->
                            <div class="space-y-3">
                                <h3 class="text-base font-medium text-gray-900 pb-1 border-b">Personal Information</h3>
                                
                                <!-- Row 1: Name -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- First Name -->
                                    <div>
                                        <InputLabel for="first_name" value="First Name *" class="text-sm" />
                                        <TextInput
                                            id="first_name"
                                            type="text"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                        
                                            v-model="form.first_name"
                                            required
                                            autofocus
                                            autocomplete="given-name"
                                            :class="{ 'border-red-300': form.errors.first_name }"
                                            placeholder="John"
                                        />
                                        <InputError class="mt-1 text-xs" :message="form.errors.first_name" />
                                    </div>
                                    
                                    <!-- Last Name -->
                                    <div>
                                        <InputLabel for="last_name" value="Last Name *" class="text-sm" />
                                        <TextInput
                                            id="last_name"
                                            type="text"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                        
                                            v-model="form.last_name"
                                            required
                                            autocomplete="family-name"
                                            :class="{ 'border-red-300': form.errors.last_name }"
                                            placeholder="Doe"
                                        />
                                        <InputError class="mt-1 text-xs" :message="form.errors.last_name" />
                                    </div>
                                </div>
                                
                                <!-- Row 2: Contact -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Email -->
                                    <div>
                                        <InputLabel for="email" value="Email *" class="text-sm" />
                                        <TextInput
                                            id="email"
                                            type="email"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                        
                                            v-model="form.email"
                                            required
                                            autocomplete="email"
                                            :class="{ 'border-red-300': form.errors.email }"
                                            placeholder="john@example.com"
                                        />
                                        <InputError class="mt-1 text-xs" :message="form.errors.email" />
                                    </div>
                                    
                                    <!-- Phone -->
                                    <div>
                                        <InputLabel for="phone" value="Phone" class="text-sm" />
                                        <TextInput
                                            id="phone"
                                            type="tel"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                        
                                            v-model="form.phone"
                                            autocomplete="tel"
                                            placeholder="+255 XXX XXX XXX"
                                            :class="{ 'border-red-300': form.errors.phone }"
                                        />
                                        <InputError class="mt-1 text-xs" :message="form.errors.phone" />
                                    </div>
                                </div>
                                
                                <!-- Row 3: Additional Info -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Date of Birth -->
                                    <div>
                                        <InputLabel for="date_of_birth" value="Date of Birth" class="text-sm" />
                                        <TextInput
                                            id="date_of_birth"
                                            type="date"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                        
                                            v-model="form.date_of_birth"
                                            :class="{ 'border-red-300': form.errors.date_of_birth }"
                                        />
                                        <InputError class="mt-1 text-xs" :message="form.errors.date_of_birth" />
                                    </div>
                                    
                                    <!-- Gender -->
                                    <div>
                                        <InputLabel for="gender" value="Gender" class="text-sm" />
                                        <SelectInput
                                            id="gender"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                        
                                            v-model="form.gender"
                                            :options="[
                                                { value: '', label: 'Select Gender' },
                                                { value: 'male', label: 'Male' },
                                                { value: 'female', label: 'Female' },
                                                { value: 'other', label: 'Other' },
                                            ]"
                                            :class="{ 'border-red-300': form.errors.gender }"
                                        />
                                        <InputError class="mt-1 text-xs" :message="form.errors.gender" />
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Account Setup Section -->
                            <div class="space-y-3">
                                <h3 class="text-base font-medium text-gray-900 pb-1 border-b">Account Setup</h3>
                                
                                <!-- Roles Selection -->
                                <div>
                                    <InputLabel value="Assign Roles *" class="text-sm mb-1" />
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <div v-for="role in roles" :key="role.id" 
                                             class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50 transition-colors"
                                             :class="{ 'border-indigo-300 bg-indigo-50': form.role_ids.includes(role.id) }">
                                            <input
                                                :id="`role-${role.id}`"
                                                type="checkbox"
                                                :value="role.id"
                                                v-model="form.role_ids"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring-indigo-200 h-3 w-3"
                                            />
                                            <label :for="`role-${role.id}`" class="ml-2 text-xs text-gray-700 cursor-pointer truncate">
                                                {{ role.name }}
                                            </label>
                                        </div>
                                    </div>
                                    <InputError class="mt-1 text-xs" :message="form.errors.role_ids" />
                                </div>
                                
                                <!-- Password Section -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Password -->
                                    <div>
                                        <InputLabel for="password" value="Password *" class="text-sm" />
                                        <TextInput
                                            id="password"
                                            type="password"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                        
                                            v-model="form.password"
                                            required
                                            autocomplete="new-password"
                                            :class="{ 'border-red-300': form.errors.password }"
                                            placeholder="••••••••"
                                        />
                                        <div v-if="form.password" class="mt-1 flex items-center">
                                            <span class="text-xs font-medium" :class="passwordStrength.color">
                                                {{ passwordStrength.text }}
                                            </span>
                                        </div>
                                        <InputError class="mt-1 text-xs" :message="form.errors.password" />
                                    </div>
                                    
                                    <!-- Password Confirmation -->
                                    <div>
                                        <InputLabel for="password_confirmation" value="Confirm Password *" class="text-sm" />
                                        <TextInput
                                            id="password_confirmation"
                                            type="password"
                                           class="w-full rounded-lg border border-gray-300 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm"
                                        
                                            v-model="form.password_confirmation"
                                            required
                                            autocomplete="new-password"
                                            :class="{ 'border-red-300': form.errors.password_confirmation }"
                                            placeholder="••••••••"
                                        />
                                        <div v-if="form.password && form.password_confirmation" class="mt-1">
                                            <span v-if="form.password === form.password_confirmation" class="text-xs text-green-600 font-medium">
                                                ✓ Passwords match
                                            </span>
                                            <span v-else class="text-xs text-red-600 font-medium">
                                                ✗ Passwords don't match
                                            </span>
                                        </div>
                                        <InputError class="mt-1 text-xs" :message="form.errors.password_confirmation" />
                                    </div>
                                </div>
                                
                                <!-- Account Status -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="flex items-center space-x-2 p-2 border border-gray-200 rounded">
                                        <input
                                            id="is_active"
                                            type="checkbox"
                                            v-model="form.is_active"
                                            :true-value="true"
                                            :false-value="false"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring-indigo-200 h-3 w-3"
                                        />
                                        <label for="is_active" class="text-xs text-gray-700">
                                            <span class="font-medium">Active Account</span>
                                            <div class="text-gray-500 text-xs">User can log in</div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 p-2 border border-gray-200 rounded">
                                        <input
                                            id="is_verified"
                                            type="checkbox"
                                            v-model="form.is_verified"
                                            :true-value="true"
                                            :false-value="false"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring-indigo-200 h-3 w-3"
                                        />
                                        <label for="is_verified" class="text-xs text-gray-700">
                                            <span class="font-medium">Email Verified</span>
                                            <div class="text-gray-500 text-xs">Skip verification</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="text-xs text-gray-500">
                                    <span class="font-medium">Note:</span> * Required fields
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button
                                        type="button"
                                        @click="form.reset(); form.is_active = true; form.is_verified = false;"
                                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded text-xs font-medium text-gray-700 uppercase tracking-wider hover:bg-gray-200 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:ring-offset-1 transition"
                                    >
                                        Reset
                                    </button>
                                    
                                    <Link
                                        :href="route('users.index')"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded text-xs font-medium text-gray-700 uppercase tracking-wider hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:ring-offset-1 transition"
                                    >
                                        Cancel
                                    </Link>
                                    
                                    <PrimaryButton 
                                        type="submit"
                                        class="text-sm py-1.5 px-4"
                                        :class="{ 'opacity-25 cursor-not-allowed': processing }" 
                                        :disabled="processing"
                                    >
                                        <svg v-if="processing" class="animate-spin -ml-1 mr-1.5 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-xs">{{ processing ? 'Creating...' : 'Create User' }}</span>
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    roles: Array,
});

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    role_ids: [],
    password: '',
    password_confirmation: '',
    date_of_birth: '',
    gender: '',
    is_active: true,
    is_verified: false,
});

const processing = ref(false);

// Computed property for role names
const selectedRoleNames = computed(() => {
    return props.roles
        .filter(role => form.role_ids.includes(role.id))
        .map(role => role.name)
        .join(', ');
});

const submit = () => {
    // Validate required fields before showing confirmation
    if (!form.first_name || !form.last_name || !form.email || !form.password || form.role_ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Required Fields',
            text: 'Please fill in all required fields (First Name, Last Name, Email, Password, and at least one Role).',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1.5 px-3'
            }
        });
        return;
    }

    // Password validation
    if (form.password !== form.password_confirmation) {
        Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'Password and confirmation do not match.',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-sm',
                confirmButton: 'text-xs py-1.5 px-3'
            }
        });
        return;
    }

    // Show confirmation with user details
    Swal.fire({
        title: 'Create New User?',
        html: `
            <div class="text-left space-y-1 text-sm">
                <p><strong>Name:</strong> ${form.first_name} ${form.last_name}</p>
                <p><strong>Email:</strong> ${form.email}</p>
                <p><strong>Phone:</strong> ${form.phone || 'Not provided'}</p>
                <p><strong>Roles:</strong> ${selectedRoleNames.value || 'No roles selected'}</p>
                <p><strong>Status:</strong> ${form.is_active ? 'Active' : 'Inactive'}</p>
                <p><strong>Email Verified:</strong> ${form.is_verified ? 'Yes' : 'No'}</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, create',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        width: '400px',
        customClass: {
            popup: 'text-sm',
            confirmButton: 'text-xs py-1.5 px-3',
            cancelButton: 'text-xs py-1.5 px-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            processing.value = true;
            
            // Show loading
            Swal.fire({
                title: 'Creating...',
                text: 'Please wait while we create the user.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: 'text-sm'
                }
            });

            form.post(route('users.store'), {
                preserveScroll: true,
                onSuccess: (response) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Created!',
                        html: `
                            <div class="text-center text-sm">
                                <p class="mb-1"><strong>${form.first_name} ${form.last_name}</strong> created!</p>
                                <p class="text-gray-600 text-xs">Email sent to user.</p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'View Users',
                        cancelButtonText: 'Create Another',
                        width: '350px',
                        customClass: {
                            popup: 'text-sm',
                            confirmButton: 'text-xs py-1.5 px-3',
                            cancelButton: 'text-xs py-1.5 px-3'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = route('users.index');
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // Reset form for new entry
                            form.reset();
                            form.is_active = true;
                            form.is_verified = false;
                            // Focus on first name field
                            document.getElementById('first_name')?.focus();
                        }
                    });
                },
                onError: (errors) => {
                    let errorMessage = '';
                    
                    if (errors.email && errors.email.includes('already been taken')) {
                        errorMessage = `Email <strong>${form.email}</strong> already exists.`;
                    } else if (errors.password) {
                        errorMessage = `Password: ${errors.password}`;
                    } else if (errors.role_ids) {
                        errorMessage = `Roles: ${errors.role_ids}`;
                    } else {
                        errorMessage = 'Please check form for errors.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Creation Failed',
                        html: errorMessage,
                        confirmButtonText: 'OK',
                        width: '350px',
                        customClass: {
                            popup: 'text-sm',
                            confirmButton: 'text-xs py-1.5 px-3'
                        }
                    });
                },
                onFinish: () => {
                    processing.value = false;
                    Swal.close();
                }
            });
        }
    });
};

// Password strength indicator
const passwordStrength = computed(() => {
    if (!form.password) return { text: '', color: '' };
    
    const score = calculatePasswordStrength(form.password);
    if (score >= 80) return { text: 'Strong', color: 'text-green-600' };
    if (score >= 60) return { text: 'Good', color: 'text-yellow-600' };
    if (score >= 40) return { text: 'Fair', color: 'text-orange-600' };
    return { text: 'Weak', color: 'text-red-600' };
});

function calculatePasswordStrength(password) {
    let score = 0;
    if (password.length >= 8) score += 25;
    if (/[A-Z]/.test(password)) score += 25;
    if (/[a-z]/.test(password)) score += 25;
    if (/[0-9]/.test(password)) score += 15;
    if (/[^A-Za-z0-9]/.test(password)) score += 10;
    return Math.min(score, 100);
}
</script>