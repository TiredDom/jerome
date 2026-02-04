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

const statusColors = {
    open: 'bg-blue-100 text-blue-700 border-blue-300',
    in_progress: 'bg-amber-100 text-amber-700 border-amber-300',
    resolved: 'bg-emerald-100 text-emerald-700 border-emerald-300'
};

const priorityColors = {
    high: 'bg-red-100 text-red-700',
    medium: 'bg-orange-100 text-orange-700',
    low: 'bg-slate-100 text-slate-600'
};

const statusSteps = [
    { key: 'open', label: 'Open', icon: '📋' },
    { key: 'in_progress', label: 'In Progress', icon: '⚡' },
    { key: 'resolved', label: 'Resolved', icon: '✅' }
];

function getStepStatus(stepKey) {
    const currentIndex = statusSteps.findIndex(s => s.key === ticket.value.status);
    const stepIndex = statusSteps.findIndex(s => s.key === stepKey);
    if (stepIndex < currentIndex) return 'complete';
    if (stepIndex === currentIndex) return 'current';
    return 'upcoming';
}

async function load() {
    try {
        const { data } = await axios.get(`/api/tickets/${props.ticketId}`);
        ticket.value = data;
    } catch (e) {
        toast.error('Failed to load ticket');
    } finally {
        loading.value = false;
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

onMounted(() => {
    load();
    if (window.Echo) {
        window.Echo.private('tickets').listen('TicketStatusChanged', (e) => {
            if ((e.ticket_id || e.ticket?.id) == props.ticketId) {
                load();
                toast.info('Your ticket status has been updated');
            }
        });
    }
});
</script>

<template>
    <Head :title="`Ticket #${ticketId}`" />

    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <Link :href="route('tickets.index')" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to tickets
                </Link>
            </div>

            <div v-if="loading" class="space-y-6">
                <div class="h-48 bg-slate-100 rounded-2xl animate-pulse"></div>
                <div class="h-32 bg-slate-100 rounded-2xl animate-pulse"></div>
            </div>

            <div v-else class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-sm font-bold text-slate-400">#{{ ticket.id }}</span>
                                    <span :class="[statusColors[ticket.status], 'px-3 py-1 text-xs font-semibold rounded-full border capitalize']">
                                        {{ ticket.status?.replace('_', ' ') }}
                                    </span>
                                    <span :class="[priorityColors[ticket.priority], 'px-3 py-1 text-xs font-semibold rounded-full capitalize']">
                                        {{ ticket.priority }} priority
                                    </span>
                                </div>
                                <h1 class="text-2xl font-bold text-slate-900">{{ ticket.title }}</h1>
                            </div>
                            <div class="text-sm text-slate-500">
                                <p>Created</p>
                                <p class="font-medium text-slate-700">{{ formatDate(ticket.created_at) }}</p>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-slate-50 rounded-xl">
                            <h3 class="text-sm font-semibold text-slate-700 mb-2">Description</h3>
                            <p class="text-slate-600 whitespace-pre-wrap">{{ ticket.description }}</p>
                        </div>

                        <div v-if="ticket.category" class="mt-4">
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">
                                {{ ticket.category }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-6">Ticket Progress</h2>
                    <div class="relative">
                        <div class="absolute top-5 left-0 right-0 h-0.5 bg-slate-200"></div>
                        <div class="relative flex justify-between">
                            <div v-for="(step, index) in statusSteps" :key="step.key" class="flex flex-col items-center">
                                <div :class="[
                                    getStepStatus(step.key) === 'complete' ? 'bg-emerald-500 text-white' :
                                    getStepStatus(step.key) === 'current' ? 'bg-indigo-500 text-white ring-4 ring-indigo-100' :
                                    'bg-slate-200 text-slate-400',
                                    'w-10 h-10 rounded-full flex items-center justify-center text-lg relative z-10'
                                ]">
                                    <span v-if="getStepStatus(step.key) === 'complete'">✓</span>
                                    <span v-else>{{ step.icon }}</span>
                                </div>
                                <p :class="[
                                    getStepStatus(step.key) === 'current' ? 'text-indigo-600 font-semibold' : 'text-slate-500',
                                    'mt-2 text-sm'
                                ]">{{ step.label }}</p>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div v-if="index !== ticket.logs.length - 1" class="absolute top-8 left-4 w-0.5 h-full bg-slate-200"></div>
                            </div>
                            <div class="flex-1 pb-4">
                                <p class="text-sm text-slate-600">
                                    Status changed from <span class="font-semibold">{{ log.from_status || 'none' }}</span>
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
