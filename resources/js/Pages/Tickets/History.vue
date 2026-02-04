<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const tickets = ref([]);
const loading = ref(true);

const statusColors = {
    open: 'bg-blue-100 text-blue-700',
    in_progress: 'bg-amber-100 text-amber-700',
    resolved: 'bg-emerald-100 text-emerald-700'
};

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/tickets');
        tickets.value = data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>

<template>
    <Head title="Ticket History" />

    <AppLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Ticket History</h1>
            <p class="mt-1 text-slate-500">View all your past and current tickets</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6">
                <div v-if="loading" class="space-y-4">
                    <div v-for="i in 4" :key="i" class="h-16 bg-slate-100 rounded-xl animate-pulse"></div>
                </div>

                <div v-else-if="tickets.length === 0" class="text-center py-16">
                    <p class="text-slate-500">No ticket history available</p>
                </div>

                <div v-else class="space-y-3">
                    <Link v-for="ticket in tickets" :key="ticket.id" :href="route('tickets.show', ticket.id)" class="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-400">#{{ ticket.id }}</span>
                                <h3 class="text-sm font-semibold text-slate-900 truncate">{{ ticket.title }}</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">{{ formatDate(ticket.created_at) }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span :class="[statusColors[ticket.status], 'px-2.5 py-1 text-xs font-semibold rounded-lg capitalize']">
                                {{ ticket.status?.replace('_', ' ') }}
                            </span>
                            <span v-if="ticket.category" class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-lg">
                                {{ ticket.category }}
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

