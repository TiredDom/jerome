<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="ServiQ - Ticketing System" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800">
        <nav class="relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-white">ServiQ</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <template v-if="canLogin">
                            <Link
                                v-if="$page.props.auth?.user"
                                :href="route('dashboard')"
                                class="text-white hover:text-indigo-200 px-3 py-2 rounded-md text-sm font-medium transition"
                            >
                                Dashboard
                            </Link>
                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="text-white hover:text-indigo-200 px-3 py-2 rounded-md text-sm font-medium transition"
                                >
                                    Log in
                                </Link>
                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="bg-white text-indigo-600 hover:bg-indigo-50 px-4 py-2 rounded-md text-sm font-medium transition"
                                >
                                    Register
                                </Link>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 text-center lg:pt-32">
                <h1 class="mx-auto max-w-4xl text-5xl font-bold tracking-tight text-white sm:text-7xl">
                    Streamline Your
                    <span class="text-indigo-300">Support Queue</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-indigo-200">
                    A modern ticketing system with priority-based queue management.
                    Submit tickets, track progress, and resolve issues efficiently.
                </p>
                <div class="mt-10 flex flex-col items-center gap-6">
                    <div class="flex justify-center gap-x-6">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-full py-3 px-6 text-sm font-semibold bg-white text-indigo-600 hover:bg-indigo-50 transition"
                        >
                            Get Started Free
                        </Link>
                        <Link
                            :href="route('login')"
                            class="ring-1 ring-white rounded-full py-3 px-6 text-sm font-semibold text-white hover:bg-white/10 transition"
                        >
                            User Sign In
                        </Link>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500 text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-white">Create Tickets</h3>
                        <p class="mt-2 text-indigo-200">Submit support tickets with detailed descriptions and categories.</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500 text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-white">Priority Queue</h3>
                        <p class="mt-2 text-indigo-200">Tickets processed by priority (High/Medium/Low) and submission time.</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500 text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-white">Track Progress</h3>
                        <p class="mt-2 text-indigo-200">Follow tickets through: Open → In Progress → Resolved.</p>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="rounded-2xl bg-white/10 p-8 md:p-12 text-center">
                    <h2 class="text-3xl font-bold text-white">Ready to get started?</h2>
                    <p class="mt-4 text-lg text-indigo-200">Create your account and submit your first ticket in minutes.</p>
                    <div class="mt-8">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-full py-3 px-8 text-sm font-semibold bg-white text-indigo-600 hover:bg-indigo-50 transition"
                        >
                            Create Free Account
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <footer class="border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <p class="text-center text-sm text-indigo-300">2026 ServiQ. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
