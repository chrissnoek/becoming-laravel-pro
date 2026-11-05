<script setup lang="ts">
import { ref } from 'vue'
import { ApiError } from '@/lib/api'
import { useAuth } from '@/composables/useAuth'

const emit = defineEmits<{ switch: [] }>()

const { login } = useAuth()

const email = ref('')
const password = ref('')
const submitting = ref(false)
const message = ref<string | null>(null)
const fieldErrors = ref<Record<string, string[]>>({})

async function submit() {
    submitting.value = true
    message.value = null
    fieldErrors.value = {}

    try {
        await login(email.value, password.value)
    } catch (e) {
        if (e instanceof ApiError && e.status === 422) {
            fieldErrors.value = e.body.errors ?? {}
        } else if (e instanceof ApiError && e.status === 401) {
            message.value = 'These credentials do not match our records.'
        } else {
            message.value = e instanceof Error ? e.message : 'Something went wrong.'
        }
    } finally {
        submitting.value = false
    }
}
</script>

<template>
    <form class="flex flex-col gap-4" novalidate @submit.prevent="submit">
        <h1 class="text-2xl font-semibold text-zinc-900">Log in</h1>

        <p v-if="message" class="rounded bg-red-50 px-3 py-2 text-sm text-red-700">{{ message }}</p>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium text-zinc-700">Email</span>
            <input
                v-model="email"
                type="email"
                autocomplete="email"
                required
                class="rounded border border-zinc-300 px-3 py-2 focus:border-zinc-900 focus:outline-none"
            />
            <span v-if="fieldErrors.email" class="text-sm text-red-700">{{ fieldErrors.email[0] }}</span>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium text-zinc-700">Password</span>
            <input
                v-model="password"
                type="password"
                autocomplete="current-password"
                required
                class="rounded border border-zinc-300 px-3 py-2 focus:border-zinc-900 focus:outline-none"
            />
            <span v-if="fieldErrors.password" class="text-sm text-red-700">{{ fieldErrors.password[0] }}</span>
        </label>

        <button
            type="submit"
            :disabled="submitting"
            class="rounded bg-zinc-900 px-4 py-2 font-medium text-white disabled:opacity-50"
        >
            {{ submitting ? 'Logging in…' : 'Log in' }}
        </button>

        <p class="text-sm text-zinc-600">
            No account yet?
            <button type="button" class="font-medium text-zinc-900 underline" @click="emit('switch')">Create one</button>
        </p>
    </form>
</template>
