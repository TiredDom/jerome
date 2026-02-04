<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const tickets = ref([]);
const loading = ref(true);
const stats = ref({ total: 0, open: 0, in_progress: 0, resolved: 0 });

const statusColors = {
    open: 'bg-blue-100 text-blue-700',
    in_progress: 'bg-amber-100 text-amber-700',
    resolved: 'bg-emerald-100 text-emerald-700'
};

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/tickets');
        tickets.value = data.slice(0, 5);
        stats.value.total = data.length;
        stats.value.open = data.filter(t => t.status === 'open').length;
        stats.value.in_progress = data.filter(t => t.status === 'in_progress').length;
        stats.value.resolved = data.filter(t => t.status === 'resolved').length;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Welcome back, {{ user?.name?.split(' ')[0] }}!</h1>
            <p class="mt-1 text-slate-500">Here's what's happening with your tickets today.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Tickets</p>
                        <p class="text-3xl font-bold text-slate-900 mt-1">{{ stats.total }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Open</p>
                        <p class="text-3xl font-bold text-blue-600 mt-1">{{ stats.open }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">In Progress</p>
                        <p class="text-3xl font-bold text-amber-600 mt-1">{{ stats.in_progress }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Resolved</p>
                        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ stats.resolved }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/60">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Recent Tickets</h2>
                    <Link :href="route('tickets.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View all</Link>
                </div>
                <div class="p-6">
                    <div v-if="loading" class="space-y-4">
                        <div v-for="i in 3" :key="i" class="h-16 bg-slate-100 rounded-xl animate-pulse"></div>
                    </div>
                    <div v-else-if="tickets.length === 0" class="text-center py-8">
                        <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-slate-500 mb-4">No tickets yet</p>
                        <Link :href="route('tickets.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                            Create your first ticket
                        </Link>
                    </div>
                    <div v-else class="space-y-3">
                        <Link v-for="ticket in tickets" :key="ticket.id" :href="route('tickets.show', ticket.id)" class="block p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-medium text-slate-400">#{{ ticket.id }}</span>
                                        <h3 class="text-sm font-semibold text-slate-900 truncate">{{ ticket.title }}</h3>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">{{ formatDate(ticket.created_at) }}</p>
                                </div>
                                <span :class="[statusColors[ticket.status], 'px-2.5 py-1 text-xs font-medium rounded-lg capitalize']">
                                    {{ ticket.status?.replace('_', ' ') }}
                                </span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-lg font-semibold text-slate-900">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <Link :href="route('tickets.create')" class="flex items-center p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-4 group-hover:bg-indigo-200 transition-colors">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">New Ticket</p>
                            <p class="text-xs text-slate-500">Create a support request</p>
                        </div>
                    </Link>

                    <Link :href="route('tickets.index')" class="flex items-center p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-4 group-hover:bg-purple-200 transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">My Tickets</p>
                            <p class="text-xs text-slate-500">View all your tickets</p>
                        </div>
                    </Link>

                    <Link :href="route('profile.edit')" class="flex items-center p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center mr-4 group-hover:bg-slate-200 transition-colors">
                            <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Settings</p>
                            <p class="text-xs text-slate-500">Manage your profile</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
