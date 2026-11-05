<script setup lang="ts">
import { onMounted, ref } from 'vue'
import LoginForm from '@/components/LoginForm.vue'
import RegisterForm from '@/components/RegisterForm.vue'
import { useAuth } from '@/composables/useAuth'

const { user, isAuthenticated, fetchUser, logout } = useAuth()

const screen = ref<'login' | 'register'>('login')

onMounted(() => {
    if (isAuthenticated.value) {
        fetchUser()
    }
})
</script>

<template>
    <main class="mx-auto flex min-h-screen max-w-sm flex-col justify-center gap-4 px-4">
        <template v-if="!isAuthenticated">
            <LoginForm v-if="screen === 'login'" @switch="screen = 'register'" />
            <RegisterForm v-else @switch="screen = 'login'" />
        </template>

        <template v-else>
            <p v-if="user" class="text-zinc-900">Logged in as {{ user.name }} ({{ user.email }})</p>
            <p v-else class="text-zinc-500">Loading user…</p>
            <button type="button" class="self-start text-sm text-zinc-600 underline" @click="logout">Log out</button>
        </template>
    </main>
</template>
