<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '@/lib/api'

type Health = { status: string; app: string }

const apiUrl = import.meta.env.VITE_API_URL

const health = ref<Health | null>(null)
const error = ref<string | null>(null)

onMounted(async () => {
    try {
        health.value = await api<Health>('/health')
    } catch (e) {
        error.value = e instanceof Error ? e.message : String(e)
    }
})
</script>

<template>
    <main class="mx-auto flex min-h-screen max-w-xl flex-col justify-center gap-4 px-4">
        <h1 class="text-2xl font-semibold text-zinc-900">Laravel Pro</h1>
        <p class="text-zinc-600">
            API:
            <code class="rounded bg-zinc-100 px-1.5 py-0.5 text-sm">{{ apiUrl }}</code>
        </p>
        <p v-if="health" class="text-emerald-700">Connected to {{ health.app }} ({{ health.status }})</p>
        <p v-else-if="error" class="text-red-700">API unreachable: {{ error }}</p>
        <p v-else class="text-zinc-500">Checking API…</p>
    </main>
</template>
