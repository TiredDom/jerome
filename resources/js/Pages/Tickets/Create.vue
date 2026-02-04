<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import axios from 'axios';

const loading = ref(false);
const errors = ref({});
const successMessage = ref('');
const errorMessage = ref('');

const form = reactive({
    title: '',
    description: '',
    category: ''
});

const categories = [
    { value: 'general', label: 'General Inquiry', icon: '💬' },
    { value: 'technical', label: 'Technical Support', icon: '🔧' },
    { value: 'billing', label: 'Billing', icon: '💳' },
    { value: 'feature', label: 'Feature Request', icon: '✨' },
    { value: 'bug', label: 'Bug Report', icon: '🐛' },
    { value: 'other', label: 'Other', icon: '📝' }
];

async function submit() {
    loading.value = true;
    errors.value = {};
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await axios.post('/api/tickets', form);
        successMessage.value = 'Ticket created successfully!';
        setTimeout(() => {
            router.visit(route('tickets.index'));
        }, 1000);
    } catch (e) {
        console.error('Error creating ticket:', e);
        if (e.response?.data?.errors) {
            errors.value = e.response.data.errors;
        } else if (e.response?.data?.error) {
            errorMessage.value = e.response.data.error;
        } else {
            errorMessage.value = 'Failed to create ticket. Please try again.';
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Create Ticket" />

    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <Link :href="route('tickets.index')" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to tickets
                </Link>
                <h1 class="text-2xl font-bold text-slate-900">Create New Ticket</h1>
                <p class="mt-1 text-slate-500">Submit a support request and we'll get back to you as soon as possible.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div v-if="successMessage" class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-emerald-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-medium text-emerald-800">{{ successMessage }}</p>
                        </div>
                    </div>

                    <div v-if="errorMessage" class="p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                        </div>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all"
                            :class="{ 'border-red-300 focus:ring-red-500': errors.title }"
                            placeholder="Brief summary of your issue"
                        />
                        <p v-if="errors.title" class="mt-2 text-sm text-red-600">{{ errors.title[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Category</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <button
                                v-for="cat in categories"
                                :key="cat.value"
                                type="button"
                                @click="form.category = cat.value"
                                :class="[
                                    form.category === cat.value
                                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-500'
                                        : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 hover:bg-slate-100',
                                    'flex items-center p-3 rounded-xl border transition-all'
                                ]"
                            >
                                <span class="text-lg mr-2">{{ cat.icon }}</span>
                                <span class="text-sm font-medium">{{ cat.label }}</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="6"
                            required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all resize-none"
                            :class="{ 'border-red-300 focus:ring-red-500': errors.description }"
                            placeholder="Please provide detailed information about your issue..."
                        ></textarea>
                        <p v-if="errors.description" class="mt-2 text-sm text-red-600">{{ errors.description[0] }}</p>
                        <p class="mt-2 text-xs text-slate-400">Be as detailed as possible to help us resolve your issue faster.</p>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                        <Link :href="route('tickets.index')" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-indigo-500/30"
                        >
                            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ loading ? 'Submitting...' : 'Submit Ticket' }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-6 p-4 rounded-xl bg-blue-50 border border-blue-100">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                        <p class="mt-1 text-sm text-blue-600">After submission, your ticket will be added to our queue. Our team processes tickets based on priority. You'll receive updates as your ticket progresses.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

