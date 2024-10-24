<template>
    <router-view />
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "monoland-app",

    setup() {
        const store = usePageStore();

        const { auth, geoInitialized } = storeToRefs(store);

        const { initApplication, initGeoLocation } = store;

        return {
            auth,
            geoInitialized,

            initApplication,
            initGeoLocation,
        };
    },

    created() {
        if (this.auth && !this.geoInitialized) {
            this.initGeoLocation();
        }
    },

    mounted() {
        this.initApplication();
    },
};
</script>
