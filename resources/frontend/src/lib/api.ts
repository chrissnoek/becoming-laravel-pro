const baseUrl = '/api'
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
    const text = await response.text()

    let body: unknown = null
    try {
        body = text ? JSON.parse(text) : null
    } catch {
        if (import.meta.env.DEV) {
            console.groupCollapsed(`[api] non-JSON response from ${init.method ?? 'GET'} ${path} (${response.status})`)
            console.log(text)
            console.groupEnd()
        }
    }

    if (!response.ok) {
        throw new ApiError(response.status, (body as ApiError['body']) ?? {})
    }

    return body as T
}
