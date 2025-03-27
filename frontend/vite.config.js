import {fileURLToPath, URL} from "node:url";
import {defineConfig, loadEnv} from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig(({mode}) => {
    process.env = {...process.env, ...loadEnv(mode, process.cwd())};

    return {
        server: {
            host: true,
            port: 3000,
            strictPort: true,
            allowedHosts: ["expelliarmus.com"],
            proxy: {
                '/api': {
                    target: process.env.VITE_BACKEND_URL,
                    secure: false,
                    ws: true,
                    configure: (proxy, _options) => {
                        proxy.on('error', (err, _req, _res) => {
                            console.log('proxy error', err);
                        });
                        proxy.on('proxyReq', (proxyReq, req, _res) => {
                            console.log('Sending Request to the Target:', req.method, req.url);
                        });
                        proxy.on('proxyRes', (proxyRes, req, _res) => {
                            console.log('Received Response from the Target:', proxyRes.statusCode, req.url);
                        });
                    },
                }
            },
        },
        plugins: [vue()],
        resolve: {
            alias: {
                "@": fileURLToPath(new URL("./src", import.meta.url)),
            },
        },
    };
});
