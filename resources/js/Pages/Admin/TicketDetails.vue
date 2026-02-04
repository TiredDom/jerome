<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const props = defineProps({ ticketId: [String, Number] });
const toast = useToast();
const ticket = ref({});
const loading = ref(true);
const updating = ref(false);

const statusColors = {
    open: 'bg-blue-100 text-blue-700 border-blue-300',
    in_progress: 'bg-amber-100 text-amber-700 border-amber-300',
    resolved: 'bg-emerald-100 text-emerald-700 border-emerald-300'
};

const priorityConfig = {
    high: { bg: 'bg-red-100', text: 'text-red-700' },
    medium: { bg: 'bg-orange-100', text: 'text-orange-700' },
    low: { bg: 'bg-slate-100', text: 'text-slate-600' }
};

const canStartProgress = computed(() => ticket.value.status === 'open');
const canResolve = computed(() => ticket.value.status === 'in_progress');

async function load() {
    try {
        const { data } = await axios.get(`/admin/api/tickets/${props.ticketId}`);
        ticket.value = data;
    } catch (e) {
        toast.error('Failed to load ticket');
    } finally {
        loading.value = false;
    }
}

async function changeStatus(to) {
    updating.value = true;
    try {
        await axios.post(`/admin/api/tickets/${ticket.value.id}/status`, { to_status: to });
        await load();
        toast.success(`Status changed to ${to.replace('_', ' ')}`);
    } catch (e) {
        toast.error(e.response?.data?.message || 'Invalid status transition');
    } finally {
        updating.value = false;
    }
}

async function changePriority(priority) {
    try {
        await axios.post(`/admin/api/tickets/${ticket.value.id}/priority`, { priority });
        ticket.value.priority = priority;
        toast.success('Priority updated');
    } catch (e) {
        toast.error('Failed to update priority');
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

onMounted(() => {
    load();
    if (window.Echo) {
        window.Echo.private('tickets').listen('TicketStatusChanged', (e) => {
            if ((e.ticket_id || e.ticket?.id) === ticket.value.id) {
                load();
                toast.success('Ticket updated');
            }
        });
    }
});
</script>

<template>
    <Head :title="`Process Ticket #${ticketId}`" />

    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <Link :href="route('admin.tickets.queue')" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to queue
                </Link>
            </div>

            <div v-if="loading" class="space-y-6">
                <div class="h-64 bg-slate-100 rounded-2xl animate-pulse"></div>
                <div class="h-48 bg-slate-100 rounded-2xl animate-pulse"></div>
            </div>

            <div v-else class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-sm font-bold text-slate-400">#{{ ticket.id }}</span>
                                    <span :class="[statusColors[ticket.status], 'px-3 py-1 text-xs font-semibold rounded-full border capitalize']">
                                        {{ ticket.status?.replace('_', ' ') }}
                                    </span>
                                </div>
                                <h1 class="text-2xl font-bold text-slate-900">{{ ticket.title }}</h1>
                                <p class="text-sm text-slate-500 mt-2">Created {{ formatDate(ticket.created_at) }}</p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3">
                                <select
                                    :value="ticket.priority"
                                    @change="changePriority($event.target.value)"
                                    :class="[priorityConfig[ticket.priority]?.bg, priorityConfig[ticket.priority]?.text, 'px-4 py-2 text-sm font-semibold rounded-xl border-0 cursor-pointer focus:ring-2 focus:ring-indigo-500']"
                                >
                                    <option value="high">🔴 High Priority</option>
                                    <option value="medium">🟠 Medium Priority</option>
                                    <option value="low">⚪ Low Priority</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-slate-50 rounded-xl">
                            <h3 class="text-sm font-semibold text-slate-700 mb-2">Description</h3>
                            <p class="text-slate-600 whitespace-pre-wrap">{{ ticket.description }}</p>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3 pt-6 border-t border-slate-100">
                            <button
                                v-if="canStartProgress"
                                @click="changeStatus('in_progress')"
                                :disabled="updating"
                                class="inline-flex items-center px-5 py-2.5 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 disabled:opacity-50 transition-all shadow-md"
                            >
                                <svg v-if="updating" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Start Progress
                            </button>

                            <button
                                v-if="canResolve"
                                @click="changeStatus('resolved')"
                                :disabled="updating"
                                class="inline-flex items-center px-5 py-2.5 bg-emerald-500 text-white text-sm font-semibold rounded-xl hover:bg-emerald-600 disabled:opacity-50 transition-all shadow-md"
                            >
                                <svg v-if="updating" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mark Resolved
                            </button>

                            <div v-if="ticket.status === 'resolved'" class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-xl">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ticket Resolved
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Activity History</h2>
                    <div v-if="ticket.logs && ticket.logs.length > 0" class="space-y-4">
                        <div v-for="(log, index) in ticket.logs" :key="log.id" class="flex gap-4">
                            <div class="relative">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div v-if="index !== ticket.logs.length - 1" class="absolute top-8 left-4 w-0.5 h-full bg-slate-200"></div>
                            </div>
                            <div class="flex-1 pb-4">
                                <p class="text-sm text-slate-600">
                                    <span class="font-semibold text-slate-900">{{ log.user?.name || 'System' }}</span>
                                    changed status from <span class="font-semibold">{{ log.from_status || 'none' }}</span>
                                    to <span class="font-semibold">{{ log.to_status }}</span>
                                </p>
                                <p class="text-xs text-slate-400 mt-1">{{ formatDate(log.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-slate-500">No activity recorded yet.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
