const baseUrl = `${import.meta.env.VITE_API_URL}/api`

export async function api<T>(path: string, init: RequestInit = {}): Promise<T> {
    const headers = new Headers(init.headers)
    headers.set('Accept', 'application/json')

    if (init.body && !headers.has('Content-Type')) {
        headers.set('Content-Type', 'application/json')
    }

    const response = await fetch(`${baseUrl}${path}`, { ...init, headers })

    if (!response.ok) {
        throw new Error(`${response.status} ${response.statusText}`)
    }

    return response.json() as Promise<T>
}
