<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto';
import { useToast } from 'vue-toastification';

const toast = useToast();
const loading = ref(true);
const stats = ref({ total: 0, open: 0, in_progress: 0, resolved_today: 0 });
const recentTickets = ref([]);

const priorityColors = {
    high: 'bg-red-100 text-red-700',
    medium: 'bg-orange-100 text-orange-700',
    low: 'bg-slate-100 text-slate-600'
};

onMounted(async () => {
    try {
        const { data } = await axios.get('/admin/api/dashboard/stats');
        stats.value.total = data.total;
        stats.value.open = data.open;
        stats.value.in_progress = data.in_progress;
        stats.value.resolved_today = data.resolved_today;
        recentTickets.value = data.recent_tickets;

        const ctx = document.getElementById('statusChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Open', 'In Progress'],
                    datasets: [{
                        data: [stats.value.open, stats.value.in_progress],
                        backgroundColor: ['#3B82F6', '#F59E0B'],
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '70%',
                    plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } } }
                }
            });
        }
    } catch (e) {
        toast.error('Failed to load dashboard data');
    } finally {
        loading.value = false;
    }
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Admin Dashboard</h1>
            <p class="mt-1 text-slate-500">Overview of your ticketing system</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total in Queue</p>
                        <p class="text-3xl font-bold text-slate-900 mt-1">{{ stats.total }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-emerald-600 font-medium">Active</span>
                    <span class="text-slate-400 ml-2">tickets awaiting action</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60">
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
                <div class="mt-4 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" :style="{ width: stats.total ? (stats.open / stats.total * 100) + '%' : '0%' }"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60">
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
                <div class="mt-4 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full" :style="{ width: stats.total ? (stats.in_progress / stats.total * 100) + '%' : '0%' }"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Resolved Today</p>
                        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ stats.resolved_today }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-emerald-600 font-medium">Completed</span>
                    <span class="text-slate-400 ml-2">tickets resolved today</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/60">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Recent Tickets</h2>
                    <Link :href="route('admin.tickets.queue')" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View Queue</Link>
                </div>
                <div class="p-6">
                    <div v-if="loading" class="space-y-4">
                        <div v-for="i in 3" :key="i" class="h-16 bg-slate-100 rounded-xl animate-pulse"></div>
                    </div>
                    <div v-else-if="recentTickets.length === 0" class="text-center py-8">
                        <p class="text-slate-500">No tickets in queue</p>
                    </div>
                    <div v-else class="space-y-3">
                        <Link v-for="ticket in recentTickets" :key="ticket.id" :href="route('admin.tickets.show', ticket.id)" class="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-400">#{{ ticket.id }}</span>
                                    <h3 class="text-sm font-semibold text-slate-900 truncate">{{ ticket.title }}</h3>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ formatDate(ticket.created_at) }}</p>
                            </div>
                            <span :class="[priorityColors[ticket.priority], 'px-2.5 py-1 text-xs font-semibold rounded-lg capitalize']">
                                {{ ticket.priority }}
                            </span>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-lg font-semibold text-slate-900">Status Distribution</h2>
                </div>
                <div class="p-6">
                    <canvas id="statusChart" class="max-h-64"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <Link :href="route('admin.tickets.queue')" class="flex items-center p-5 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold">Ticket Queue</p>
                    <p class="text-sm text-indigo-200">Process pending tickets</p>
                </div>
            </Link>

            <Link :href="route('admin.tickets.logs')" class="flex items-center p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mr-4 group-hover:bg-indigo-100 transition-colors">
                    <svg class="w-6 h-6 text-slate-600 group-hover:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Activity Logs</p>
                    <p class="text-sm text-slate-500">View system history</p>
                </div>
            </Link>

            <a :href="route('api.admin.tickets.export')" class="flex items-center p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mr-4 group-hover:bg-emerald-100 transition-colors">
                    <svg class="w-6 h-6 text-slate-600 group-hover:text-emerald-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Export Data</p>
                    <p class="text-sm text-slate-500">Download CSV report</p>
                </div>
            </a>
        </div>
    </AppLayout>
</template>
