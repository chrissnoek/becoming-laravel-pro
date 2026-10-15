import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

const frontendDomain = process.env.FRONTEND_DOMAIN
const proxyPort = process.env.PROXY_PORT

export default defineConfig({
    plugins: [vue(), tailwindcss()],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
        },
    },
    server: {
        host: true,
        port: 5173,
        strictPort: true,
        allowedHosts: frontendDomain ? [frontendDomain] : [],
        // The browser reaches Vite through the Caddy proxy, so HMR must connect on the proxy port.
        hmr: proxyPort ? { clientPort: Number(proxyPort) } : undefined,
    },
})
