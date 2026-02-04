<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const tickets = ref([]);
const loading = ref(true);

const statusColors = {
    open: 'bg-blue-100 text-blue-700 border-blue-200',
    in_progress: 'bg-amber-100 text-amber-700 border-amber-200',
    resolved: 'bg-emerald-100 text-emerald-700 border-emerald-200'
};

const priorityConfig = {
    high: { bg: 'bg-red-100', text: 'text-red-700', border: 'border-red-200', dot: 'bg-red-500' },
    medium: { bg: 'bg-orange-100', text: 'text-orange-700', border: 'border-orange-200', dot: 'bg-orange-500' },
    low: { bg: 'bg-slate-100', text: 'text-slate-600', border: 'border-slate-200', dot: 'bg-slate-400' }
};

async function load() {
    loading.value = true;
    try {
        const { data } = await axios.get('/admin/api/tickets/queue');
        tickets.value = data;
    } catch (e) {
        toast.error('Failed to load queue');
    } finally {
        loading.value = false;
    }
}

async function setPriority(ticket, priority) {
    try {
        await axios.post(`/admin/api/tickets/${ticket.id}/priority`, { priority });
        ticket.priority = priority;
        toast.success('Priority updated');
        load();
    } catch (e) {
        toast.error('Failed to update priority');
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function getTimeAgo(date) {
    const now = new Date();
    const diff = now - new Date(date);
    const hours = Math.floor(diff / 3600000);
    if (hours < 1) return 'Just now';
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
}

onMounted(() => {
    load();
    if (window.Echo) {
        window.Echo.private('tickets').listen('TicketStatusChanged', () => load());
    }
});
</script>

<template>
    <Head title="Ticket Queue" />

    <AppLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ticket Queue</h1>
                <p class="mt-1 text-slate-500">Process tickets by priority and submission time</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                <span class="px-3 py-1.5 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg">
                    {{ tickets.length }} tickets
                </span>
                <button @click="load" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-slate-600">High Priority</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        <span class="text-slate-600">Medium</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                        <span class="text-slate-600">Low</span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div v-if="loading" class="space-y-4">
                    <div v-for="i in 4" :key="i" class="h-28 bg-slate-100 rounded-xl animate-pulse"></div>
                </div>

                <div v-else-if="tickets.length === 0" class="text-center py-16">
                    <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Queue is empty!</h3>
                    <p class="text-slate-500">All tickets have been processed. Great work!</p>
                </div>

                <div v-else class="space-y-4">
                    <div v-for="(ticket, index) in tickets" :key="ticket.id" class="relative p-5 rounded-xl border-2 transition-all hover:shadow-md"
                         :class="[priorityConfig[ticket.priority].border, 'border-l-4']"
                         :style="{ borderLeftColor: priorityConfig[ticket.priority].dot.replace('bg-', '') }">
                        <div class="absolute -left-px top-0 bottom-0 w-1 rounded-l-xl" :class="priorityConfig[ticket.priority].dot"></div>

                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-lg font-bold text-slate-400">
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-bold text-slate-400">#{{ ticket.id }}</span>
                                        <span :class="[statusColors[ticket.status], 'px-2 py-0.5 text-xs font-semibold rounded-md border capitalize']">
                                            {{ ticket.status?.replace('_', ' ') }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-semibold text-slate-900 truncate">{{ ticket.title }}</h3>
                                    <p class="text-sm text-slate-500 mt-1 line-clamp-1">{{ ticket.description }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 lg:gap-6">
                                <div class="text-right hidden sm:block">
                                    <p class="text-xs text-slate-400">Submitted</p>
                                    <p class="text-sm font-medium text-slate-600">{{ getTimeAgo(ticket.created_at) }}</p>
                                </div>

                                <select
                                    :value="ticket.priority"
                                    @change="setPriority(ticket, $event.target.value)"
                                    :class="[priorityConfig[ticket.priority].bg, priorityConfig[ticket.priority].text, 'px-3 py-2 text-sm font-semibold rounded-lg border-0 cursor-pointer focus:ring-2 focus:ring-indigo-500']"
                                >
                                    <option value="high">🔴 High</option>
                                    <option value="medium">🟠 Medium</option>
                                    <option value="low">⚪ Low</option>
                                </select>

                                <Link :href="route('admin.tickets.show', ticket.id)" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md shadow-indigo-500/20">
                                    Process
                                    <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

