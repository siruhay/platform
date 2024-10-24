import Vue from "@vitejs/plugin-vue";
import Vuetify from "vite-plugin-vuetify";
import basicSsl from "@vitejs/plugin-basic-ssl";
import { defineConfig } from "vite";
import { fileURLToPath, URL } from "node:url";
import { transformAssetUrls } from "vite-plugin-vuetify";
// import { VitePWA } from "vite-plugin-pwa";

export default defineConfig({
    root: "resources",

    build: {
        rollupOptions: {
            input: {
                app: fileURLToPath(
                    new URL("./resources/index.html", import.meta.url)
                ),

                mobile: fileURLToPath(
                    new URL("./resources/mobile.html", import.meta.url)
                ),
            },
        },
    },

    plugins: [
        basicSsl(),
        Vue({
            template: {
                transformAssetUrls,
            },
        }),
        Vuetify({
            autoImport: true,
        }),
        // VitePWA({
        //     includeAssets: [
        //         "assets/favicon.ico",
        //         "assets/apple-touch-icon.png",
        //     ],

        //     devOptions: {
        //         enabled: true,
        //         type: "module",
        //     },

        //     strategies: "injectManifest",
        //     srcDir: "./src/workers",
        //     filename: "service-worker.js",

        //     registerType: "autoUpdate",

        //     manifest: {
        //         name: "Siruhay",
        //         short_name: "SiRUHAY",
        //         description: "Sistem Terintegrasi Rumah Pemberdayaan",
        //         icons: [
        //             {
        //                 src: "assets/pwa-512x512.png",
        //                 sizes: "512x512",
        //                 type: "image/png",
        //                 purpose: "any",
        //             },

        //             {
        //                 src: "assets/pwa-192x192.png",
        //                 sizes: "192x192",
        //                 type: "image/png",
        //                 purpose: "any",
        //             },
        //         ],
        //         start_url: "/",
        //         display: "fullscreen",
        //         display_override: ["fullscreen", "minimal-ui"],
        //         background_color: "#5A6062",
        //         theme_color: "#5A6062",
        //     },

        //     workbox: {
        //         exclude: [/\.(?:png|php|jpg|jpeg|svg|txt|ico|html|htaccess)$/],
        //         maximumFileSizeToCacheInBytes: 10485760,
        //     },
        // }),
    ],
    define: { "process.env": {} },
    resolve: {
        alias: {
            "@components": fileURLToPath(
                new URL("./resources/src/components", import.meta.url)
            ),
            "@modules": fileURLToPath(new URL("./modules", import.meta.url)),
            "@plugins": fileURLToPath(
                new URL("./resources/src/plugins", import.meta.url)
            ),
            "@pinia": fileURLToPath(
                new URL("./resources/src/plugins/pinia", import.meta.url)
            ),
            "@styles": fileURLToPath(
                new URL("./resources/src/styles", import.meta.url)
            ),
        },
        extensions: [".js", ".json", ".jsx", ".mjs", ".ts", ".tsx", ".vue"],
    },
    server: {
        host: "hmr.devsiruhay.test",
        https: true,
        port: 3000,
    },
});
