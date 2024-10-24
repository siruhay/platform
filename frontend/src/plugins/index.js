// Plugins
import vuetify from "./vuetify";
import pinia from "./pinia";
import router from "./router";
import { Storage } from "./storage";
import { RequestInstance } from "./request";
import { useRouter, useRoute } from "vue-router";
import VueApexCharts from "vue3-apexcharts";

/**
 * registerDesktopPlugins
 * @param {*} app
 */
export function registerDesktopPlugins(app) {
    /** platform:desktop-components */
    const components = import.meta.glob("@components/desktop/**/index.vue", {
        eager: true,
    });

    Object.entries(components).forEach((file) => {
        app.component(file[1].default.name, file[1].default);
    });

    /** platform:mobile-plugins */
    const modulePlugins = import.meta.glob("@modules/**/plugins/index.js", {
        eager: true,
    });

    Object.entries(modulePlugins).forEach((plugin) => {
        if (
            Object.prototype.hasOwnProperty.call(
                plugin[1],
                "moduleDesktopPlugins"
            )
        ) {
            plugin[1].moduleDesktopPlugins(app);
        }
    });

    /** register storage and http */
    app.config.globalProperties.$storage = new Storage();
    app.config.globalProperties.$http = RequestInstance;

    pinia.use(({ store }) => {
        store.$http = app.config.globalProperties.$http;
        store.$storage = app.config.globalProperties.$storage;
        store.$route = useRoute();
        store.$router = useRouter();
    });

    app.use(vuetify).use(pinia).use(router).use(VueApexCharts);
}

/**
 * registerMobilePlugins
 * @param {*} app
 */
export function registerMobilePlugins(app) {
    /** platform:mobile-components */
    const components = import.meta.glob("@components/mobile/**/index.vue", {
        eager: true,
    });

    Object.entries(components).forEach((file) => {
        app.component(file[1].default.name, file[1].default);
    });

    /** platform:mobile-plugins */
    const modulePlugins = import.meta.glob("@modules/**/plugins/index.js", {
        eager: true,
    });

    Object.entries(modulePlugins).forEach((plugin) => {
        if (
            Object.prototype.hasOwnProperty.call(
                plugin[1],
                "moduleMobilePlugins"
            )
        ) {
            plugin[1].moduleMobilePlugins(app);
        }
    });

    /** register storage and http */
    app.config.globalProperties.$storage = new Storage();
    app.config.globalProperties.$http = RequestInstance;

    pinia.use(({ store }) => {
        store.$http = app.config.globalProperties.$http;
        store.$storage = app.config.globalProperties.$storage;
        store.$route = useRoute();
        store.$router = useRouter();
    });

    app.use(vuetify).use(pinia).use(router).use(VueApexCharts);
}
