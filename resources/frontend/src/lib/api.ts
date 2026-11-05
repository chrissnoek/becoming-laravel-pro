const baseUrl = `${import.meta.env.VITE_API_URL}/api`
const tokenKey = 'access_token'

export class ApiError extends Error {
    constructor(
        public status: number,
        public body: { message?: string; errors?: Record<string, string[]> } = {},
    ) {
        super(body.message ?? `Request failed with status ${status}`)
    }
}

export function getToken(): string | null {
    return localStorage.getItem(tokenKey)
}

export function setToken(token: string | null): void {
    if (token) {
        localStorage.setItem(tokenKey, token)
    } else {
        localStorage.removeItem(tokenKey)
    }
}

export async function api<T>(path: string, init: RequestInit = {}): Promise<T> {
    const headers = new Headers(init.headers)
    headers.set('Accept', 'application/json')

    if (init.body && !headers.has('Content-Type')) {
        headers.set('Content-Type', 'application/json')
    }

    const token = getToken()
    if (token && !headers.has('Authorization')) {
        headers.set('Authorization', `Bearer ${token}`)
    }

    const response = await fetch(`${baseUrl}${path}`, { ...init, headers })

    if (!response.ok) {
        const body = await response.json().catch(() => ({}))
        throw new ApiError(response.status, body)
    }

    return response.json() as Promise<T>
}
