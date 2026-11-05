import { computed, ref } from 'vue'
import { api, ApiError, getToken, setToken } from '@/lib/api'

export type User = { id: number; name: string; email: string }

type TokenResponse = {
    token_type: 'Bearer'
    expires_in: number
    access_token: string
    refresh_token?: string
}

export type RegisterPayload = {
    name: string
    email: string
    password: string
    password_confirmation: string
}

const user = ref<User | null>(null)
const token = ref<string | null>(getToken())

export function useAuth() {
    const isAuthenticated = computed(() => token.value !== null)

    async function authenticate(response: TokenResponse): Promise<void> {
        setToken(response.access_token)
        token.value = response.access_token
        await fetchUser()
    }

    async function login(email: string, password: string): Promise<void> {
        const response = await api<TokenResponse>('/login', {
            method: 'POST',
            body: JSON.stringify({ email, password }),
        })

        await authenticate(response)
    }

    async function register(payload: RegisterPayload): Promise<void> {
        const response = await api<TokenResponse>('/register', {
            method: 'POST',
            body: JSON.stringify(payload),
        })

        await authenticate(response)
    }

    async function fetchUser(): Promise<void> {
        try {
            user.value = await api<User>('/user')
        } catch (e) {
            if (e instanceof ApiError && e.status === 401) {
                logout()
                return
            }
            throw e
        }
    }

    function logout(): void {
        setToken(null)
        token.value = null
        user.value = null
    }

    return { user, isAuthenticated, login, register, logout, fetchUser }
}
