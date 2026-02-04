<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: String,
    subtitle: String,
});
</script>

<template>
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%239C92AC%22%20fill-opacity%3D%220.05%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-40"></div>

            <div class="absolute top-8 left-8">
                <Link href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-2xl shadow-indigo-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">ServiQ</span>
                </Link>
            </div>

            <div class="relative z-10 flex flex-col justify-center px-12 lg:px-16">
                <div class="max-w-md">
                    <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight mb-6">
                        Streamline Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Support Queue</span>
                    </h1>
                    <p class="text-lg text-slate-300 mb-10">
                        A modern ticketing system with priority-based queue management. Track, manage, and resolve issues efficiently.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Secure Access</h3>
                                <p class="text-slate-400 text-sm">Enterprise-grade security with session management</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Real-time Updates</h3>
                                <p class="text-slate-400 text-sm">Instant notifications on ticket status changes</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Priority Queue</h3>
                                <p class="text-slate-400 text-sm">Smart prioritization for faster resolution</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-8 left-8 right-8">
                <p class="text-slate-500 text-sm">© 2026 ServiQ. All rights reserved.</p>
            </div>
        </div>

        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-slate-50">
            <div class="lg:hidden mb-10">
                <Link href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-slate-900">ServiQ</span>
                </Link>
            </div>

            <div class="mx-auto w-full max-w-sm lg:max-w-md">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-slate-900">{{ title }}</h2>
                    <p class="mt-2 text-slate-600">{{ subtitle }}</p>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
