<template>
    <Head title="Users" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users Management</h2>
            </div>
        </template>

        <div class="py-6">
            <!-- Centered Container with max width -->
            <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                    <!-- Total Users Card -->
                    <div
                        class="bg-gradient-to-br from-indigo-50 to-indigo-100 overflow-hidden shadow-sm rounded-lg border border-indigo-200 hover:shadow transition-all duration-200">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-xs font-medium text-indigo-700 truncate">Total Users</p>
                                    <p class="text-xl font-bold text-indigo-900">{{ summary?.totalUsers || 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admins Card -->
                    <div
                        class="bg-gradient-to-br from-purple-50 to-purple-100 overflow-hidden shadow-sm rounded-lg border border-purple-200 hover:shadow transition-all duration-200">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-xs font-medium text-purple-700 truncate">Administrators</p>
                                    <p class="text-xl font-bold text-purple-900">{{ summary?.totalAdmins || 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Staff Card -->
                    <div
                        class="bg-gradient-to-br from-amber-50 to-amber-100 overflow-hidden shadow-sm rounded-lg border border-amber-200 hover:shadow transition-all duration-200">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-amber-500 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-xs font-medium text-amber-700 truncate">Staff Members</p>
                                    <p class="text-xl font-bold text-amber-900">{{ summary?.totalStaff || 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Regular Users Card -->
                    <div
                        class="bg-gradient-to-br from-sky-50 to-sky-100 overflow-hidden shadow-sm rounded-lg border border-sky-200 hover:shadow transition-all duration-200">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-sky-500 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-xs font-medium text-sky-700 truncate">Regular Users</p>
                                    <p class="text-xl font-bold text-sky-900">{{ summary?.totalRegularUsers || 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header and Actions -->
                <div class="mb-6">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">Users</h1>
                            <p class="mt-1 text-sm text-gray-600">A list of all registered users</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <Link :href="route('users.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add User
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="search" v-model="searchTerm"
                                    placeholder="Search by name or email..."
                                    class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm" />
                            </div>
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
                            <select id="role" v-model="roleFilter" @change="handleRoleChange"
                                class="w-full py-2 px-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                                <option value="">All Roles</option>
                                <option value="ADMIN">Admin</option>
                                <option value="STAFF">Staff</option>
                                <option value="USER">User</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Users Data Table -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <!-- Loading State -->
                    <div v-if="loading">
                        <div class="flex justify-center items-center p-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-500"></div>
                            <span class="ml-3 text-sm font-medium text-gray-500">Loading users...</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Phone
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Joined
                                        </th>
                                        <th v-if="canManageUsers" scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="i in 5" :key="i" class="animate-pulse">
                                        <td class="px-6 py-4">
                                            <div class="h-3 bg-gray-200 rounded w-6"></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-gray-200"></div>
                                                <div class="ml-3">
                                                    <div class="h-3 bg-gray-200 rounded w-24 mb-1"></div>
                                                    <div class="h-2 bg-gray-200 rounded w-16"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="h-3 bg-gray-200 rounded w-32"></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="h-3 bg-gray-200 rounded w-20"></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="h-5 bg-gray-200 rounded-full w-16"></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="h-3 bg-gray-200 rounded w-20"></div>
                                        </td>
                                        <td v-if="canManageUsers" class="px-6 py-4">
                                            <div class="flex justify-center space-x-2">
                                                <div class="h-6 w-6 bg-gray-200 rounded"></div>
                                                <div class="h-6 w-6 bg-gray-200 rounded"></div>
                                                <div class="h-6 w-6 bg-gray-200 rounded"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div v-else>
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Phone
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Joined
                                        </th>
                                        <th v-if="canManageUsers" scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(user, index) in users.data" :key="user.id"
                                        class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="h-8 w-8 flex-shrink-0 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
                                                    <span class="text-xs font-bold text-white">
                                                        {{ getInitials(user.first_name) }}
                                                    </span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ user.first_name }} {{ user.last_name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        ID: {{ user.id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ user.phone || '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="getRoleClass(user.role)">
                                                <span class="inline-block h-1.5 w-1.5 rounded-full mr-1.5" :class="{
                                                    'bg-indigo-500': user.role === 'ADMIN',
                                                    'bg-amber-500': user.role === 'STAFF',
                                                    'bg-sky-500': user.role === 'USER'
                                                }"></span>
                                                {{ user?.role }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ formattedDate(user.created_at) }}
                                        </td>
                                        <td v-if="canManageUsers" class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex justify-center space-x-2">
                                                <Link :href="route('users.show', { id: user.id })"
                                                    class="inline-flex items-center p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded transition-colors"
                                                    title="View User">
                                                    <EyeIcon class="h-4 w-4" />
                                                </Link>

                                                <Link :href="route('users.edit', { id: user.id })"
                                                    class="inline-flex items-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                                    title="Edit User">
                                                    <PencilIcon class="h-4 w-4" />
                                                </Link>

                                                <button @click="confirmDelete(user.id)"
                                                    class="inline-flex items-center p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors"
                                                    title="Delete User">
                                                    <TrashIcon class="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Empty State -->
                                    <tr v-if="users.data.length === 0">
                                        <td :colspan="canManageUsers ? 7 : 6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div
                                                    class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                                    <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-medium text-gray-900 mb-1">No users found</p>
                                                <p class="text-xs text-gray-500 mb-4">
                                                    {{ searchTerm || roleFilter ? 'Try adjusting your search criteria' :
                                                        'There are no users in the system yet' }}
                                                </p>
                                                <Link :href="route('users.create')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 w-4 h-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add User
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6" v-if="users.data && users.data.length > 0">
                    <Pagination v-if="!loading" :data="users" :pageNumberUpdated="pageNumberUpdated" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { ref, watch, computed, onMounted } from "vue";
import { EyeIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

const page = usePage();

const props = defineProps({
    users: Object,
    search: String,
    role: String,
    summary: Object
});

let pageNumber = ref(1),
    searchTerm = ref(usePage().props.search ?? "");

let loading = ref(true);
let roleFilter = ref(props.role || "");

// Safe access to auth user
const authUser = computed(() => page.props.auth?.user);
const userRole = computed(() => authUser.value?.role || null);
const canManageUsers = computed(() => {
    const role = userRole.value;
    return role === 'ADMIN' || role === 'STAFF';
});

const getInitials = (name) => {
    if (!name) return '';
    return name.split(' ')
        .map(part => part.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

onMounted(() => {
    setTimeout(() => {
        loading.value = false;
    }, 1000);
});

const pageNumberUpdated = (link) => {
    pageNumber.value = link.url.split("=")[1];
    loading.value = true;
    setTimeout(() => {
        loading.value = false;
    }, 500);
};

const usersUrl = computed(() => {
    const url = new URL(route("users.index"));
    url.searchParams.set("page", pageNumber.value);

    if (searchTerm.value.trim()) {
        url.searchParams.set("search", searchTerm.value);
    }

    if (roleFilter.value) {
        url.searchParams.set("role", roleFilter.value);
    }
    return url;
});

watch(
    () => usersUrl.value,
    (newValue) => {
        router.visit(newValue, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onStart: () => {
                loading.value = true;
            },
            onFinish: () => {
                setTimeout(() => {
                    loading.value = false;
                }, 500);
            }
        });
    }
);

watch(searchTerm, () => {
    pageNumber.value = 1;
    const url = new URL(route("users.index"));

    if (searchTerm.value.trim()) {
        url.searchParams.set("search", searchTerm.value);
    }

    if (roleFilter.value) {
        url.searchParams.set("role", roleFilter.value);
    }

    url.searchParams.set("page", pageNumber.value);

    router.visit(url, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onStart: () => {
            loading.value = true;
        },
        onFinish: () => {
            setTimeout(() => {
                loading.value = false;
            }, 500);
        }
    });
});

// Role filter change
const handleRoleChange = () => {
    pageNumber.value = 1;
    const url = new URL(route("users.index"));

    if (searchTerm.value.trim()) {
        url.searchParams.set("search", searchTerm.value);
    }

    if (roleFilter.value) {
        url.searchParams.set("role", roleFilter.value);
    }

    url.searchParams.set("page", pageNumber.value);

    router.visit(url, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onStart: () => {
            loading.value = true;
        },
        onFinish: () => {
            setTimeout(() => {
                loading.value = false;
            }, 500);
        }
    });
};

const formattedDate = (dateString) => {
    if (!dateString) return null;
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return null;

    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(date);
};

// Get role badge class
const getRoleClass = (role) => {
    switch (role) {
        case 'ADMIN':
            return 'bg-indigo-100 text-indigo-800';
        case 'STAFF':
            return 'bg-amber-100 text-amber-800';
        case 'USER':
            return 'bg-sky-100 text-sky-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const confirmDelete = async (userId) => {
    const result = await Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
    });

    if (result.isConfirmed) {
        loading.value = true;
        router.delete(route('users.delete', { id: userId }), {
            onSuccess: () => {
                Swal.fire({
                    title: "Deleted!",
                    text: "User has been deleted.",
                    icon: "success",
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                setTimeout(() => {
                    loading.value = false;
                }, 500);
            },
            onError: () => {
                Swal.fire({
                    title: "Error!",
                    text: "Something went wrong.",
                    icon: "error",
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                loading.value = false;
            }
        });
    }
};
</script>

<style scoped>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: .5;
    }
}
</style>