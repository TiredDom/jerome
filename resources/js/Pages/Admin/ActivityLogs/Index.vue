<script setup>
import { ref } from 'vue';
import { router, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    logs: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters.search || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const applyFilters = () => {
    router.get(route('admin.tickets.logs'), {
        search: search.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    router.get(route('admin.tickets.logs'));
};

const exportCSV = () => {
    const params = new URLSearchParams({
        search: search.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    });
    window.location.href = route('admin.activity-logs.export') + '?' + params.toString();
};

const getActionBadge = (fromStatus, toStatus) => {
    if (!fromStatus || fromStatus === 'none') {
        return { text: 'Created', class: 'bg-blue-100 text-blue-700' };
    }
    if (toStatus === 'resolved') {
        return { text: 'Resolved', class: 'bg-green-100 text-green-700' };
    }
    if (toStatus === 'in_progress') {
        return { text: 'In Progress', class: 'bg-amber-100 text-amber-700' };
    }
    if (fromStatus.startsWith('priority:')) {
        return { text: 'Priority Changed', class: 'bg-purple-100 text-purple-700' };
    }
    return { text: 'Status Changed', class: 'bg-indigo-100 text-indigo-700' };
};

const formatStatusChange = (fromStatus, toStatus) => {
    const from = fromStatus || 'none';
    const to = toStatus;

    if (from.startsWith('priority:') && to.startsWith('priority:')) {
        return `${from.replace('priority:', '')} → ${to.replace('priority:', '')}`;
    }

    return `${from} → ${to}`;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getPriorityBadge = (priority) => {
    const badges = {
        low: 'bg-gray-100 text-gray-700',
        medium: 'bg-blue-100 text-blue-700',
        high: 'bg-red-100 text-red-700',
    };
    return badges[priority] || badges.low;
};

const getStatusBadge = (status) => {
    const badges = {
        open: 'bg-blue-100 text-blue-700',
        in_progress: 'bg-amber-100 text-amber-700',
        resolved: 'bg-green-100 text-green-700',
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head title="Activity Logs" />

    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Activity Logs</h1>
                    <p class="text-gray-600 mt-2">Track all ticket status changes and actions</p>
                </div>
                <button
                    @click="exportCSV"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Activities</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.today }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-xl">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">This Week</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.this_week }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-xl">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">This Month</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.this_month }}</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-xl">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search activities..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            @keyup.enter="applyFilters"
                        />
                    </div>

                    <div>
                        <input
                            v-model="dateFrom"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    <div>
                        <input
                            v-model="dateTo"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    <div class="flex space-x-2">
                        <button
                            @click="applyFilters"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                        >
                            Apply
                        </button>
                        <button
                            @click="clearFilters"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div v-if="logs.data.length > 0" class="divide-y divide-gray-200">
                    <div
                        v-for="log in logs.data"
                        :key="log.id"
                        class="p-6 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-3 bg-indigo-100 rounded-xl">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <span :class="['px-2.5 py-0.5 rounded-full text-xs font-medium', getActionBadge(log.from_status, log.to_status).class]">
                                            {{ getActionBadge(log.from_status, log.to_status).text }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ log.created_at_human }}</span>
                                    </div>
                                </div>

                                <div v-if="log.ticket" class="mb-3">
                                    <Link :href="`/admin/tickets/${log.ticket.id}/show`" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                        Ticket #{{ log.ticket.id }}: {{ log.ticket.title }}
                                    </Link>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span :class="['px-2 py-0.5 rounded text-xs font-medium', getStatusBadge(log.ticket.status)]">
                                            {{ log.ticket.status?.replace('_', ' ') }}
                                        </span>
                                        <span :class="['px-2 py-0.5 rounded text-xs font-medium', getPriorityBadge(log.ticket.priority)]">
                                            {{ log.ticket.priority }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-sm text-gray-900 mb-3">
                                    <span class="font-medium">Status change:</span>
                                    <span class="ml-2">{{ formatStatusChange(log.from_status, log.to_status) }}</span>
                                </div>

                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    <div v-if="log.user" class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ log.user.name }}</span>
                                    </div>
                                    <div v-else class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                        </svg>
                                        <span>System</span>
                                    </div>

                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ formatDate(log.created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No activity logs</h3>
                    <p class="mt-2 text-sm text-gray-500">Activity will appear here when tickets are updated.</p>
                </div>

                <div v-if="logs.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ logs.from }}</span> to
                            <span class="font-medium">{{ logs.to }}</span> of
                            <span class="font-medium">{{ logs.total }}</span> results
                        </p>

                        <div class="flex space-x-2">
                            <template v-for="link in logs.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 text-sm rounded-lg',
                                        link.active
                                            ? 'bg-indigo-600 text-white font-medium'
                                            : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                    ]"
                                    v-html="link.label.replace('&laquo;', '←').replace('&raquo;', '→')"
                                />
                                <span
                                    v-else
                                    :class="[
                                        'px-3 py-2 text-sm rounded-lg',
                                        'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                                    ]"
                                    v-html="link.label.replace('&laquo;', '←').replace('&raquo;', '→')"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
