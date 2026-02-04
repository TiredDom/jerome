<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const tickets = ref([]);
const loading = ref(true);
const filter = ref('all');

const statusColors = {
    open: 'bg-blue-100 text-blue-700 border-blue-200',
    in_progress: 'bg-amber-100 text-amber-700 border-amber-200',
    resolved: 'bg-emerald-100 text-emerald-700 border-emerald-200'
};

const priorityColors = {
    high: 'bg-red-100 text-red-700',
    medium: 'bg-orange-100 text-orange-700',
    low: 'bg-slate-100 text-slate-700'
};

const filteredTickets = () => {
    if (filter.value === 'all') return tickets.value;
    return tickets.value.filter(t => t.status === filter.value);
};

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/tickets');
        tickets.value = data;
    } catch (e) {
        toast.error('Failed to load tickets');
    } finally {
        loading.value = false;
    }

    if (window.Echo) {
        window.Echo.private('tickets').listen('TicketStatusChanged', (e) => {
            const id = e.ticket_id || (e.ticket && e.ticket.id);
            const index = tickets.value.findIndex(t => t.id === id);
            if (index !== -1) {
                tickets.value[index].status = e.to;
            }
            toast.info(`Ticket #${id} status updated`);
        });
    }
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>

<template>
    <Head title="My Tickets" />

    <AppLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">My Tickets</h1>
                <p class="mt-1 text-slate-500">Track and manage your support requests</p>
            </div>
            <Link :href="route('tickets.create')" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Ticket
            </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center gap-2">
                <button @click="filter = 'all'" :class="[filter === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200', 'px-4 py-2 text-sm font-medium rounded-lg transition-colors']">
                    All
                </button>
                <button @click="filter = 'open'" :class="[filter === 'open' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-600 hover:bg-blue-100', 'px-4 py-2 text-sm font-medium rounded-lg transition-colors']">
                    Open
                </button>
                <button @click="filter = 'in_progress'" :class="[filter === 'in_progress' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-600 hover:bg-amber-100', 'px-4 py-2 text-sm font-medium rounded-lg transition-colors']">
                    In Progress
                </button>
                <button @click="filter = 'resolved'" :class="[filter === 'resolved' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100', 'px-4 py-2 text-sm font-medium rounded-lg transition-colors']">
                    Resolved
                </button>
            </div>

            <div class="p-6">
                <div v-if="loading" class="space-y-4">
                    <div v-for="i in 4" :key="i" class="h-24 bg-slate-100 rounded-xl animate-pulse"></div>
                </div>

                <div v-else-if="filteredTickets().length === 0" class="text-center py-16">
                    <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">No tickets found</h3>
                    <p class="text-slate-500 mb-6">Get started by creating your first support ticket</p>
                    <Link :href="route('tickets.create')" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                        Create Ticket
                    </Link>
                </div>

                <div v-else class="space-y-4">
                    <Link v-for="ticket in filteredTickets()" :key="ticket.id" :href="route('tickets.show', ticket.id)" class="block p-5 rounded-xl border border-slate-200 hover:border-indigo-300 hover:shadow-md transition-all group">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-xs font-bold text-slate-400">#{{ ticket.id }}</span>
                                    <span :class="[statusColors[ticket.status], 'px-2.5 py-1 text-xs font-semibold rounded-lg border capitalize']">
                                        {{ ticket.status?.replace('_', ' ') }}
                                    </span>
                                    <span :class="[priorityColors[ticket.priority], 'px-2.5 py-1 text-xs font-medium rounded-lg capitalize']">
                                        {{ ticket.priority }}
                                    </span>
                                </div>
                                <h3 class="text-base font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ ticket.title }}</h3>
                                <p class="text-sm text-slate-500 mt-1 line-clamp-1">{{ ticket.description }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-xs text-slate-400">Created</p>
                                    <p class="text-sm font-medium text-slate-600">{{ formatDate(ticket.created_at) }}</p>
                                </div>
                                <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
