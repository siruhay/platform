<template>
    <v-layout :class="`bg-${theme}-lighten-4`">
        <router-view key="userbase" />

        <v-overlay
            :model-value="overlay"
            class="align-center justify-center"
            scroll-strategy="block"
            persistent
        >
            <v-progress-circular
                :color="theme"
                :width="3"
                size="64"
                indeterminate
            >
                <template v-slot:default><small>loading</small></template>
            </v-progress-circular>
        </v-overlay>

        <v-snackbar
            :color="snackbar.color"
            :timeout="1500"
            v-model="snackbar.state"
            multi-line
        >
            {{ snackbar.text }}

            <template v-slot:actions>
                <v-btn
                    color="white"
                    variant="text"
                    @click="snackbar.state = false"
                >
                    Close
                </v-btn>
            </template>
        </v-snackbar>

        <v-bottom-navigation
            :active="navigationState"
            :base-color="`${theme}-lighten-2`"
            :color="`${theme}-darken-1`"
        >
            <v-btn
                v-for="(page, index) in appsMenus"
                :key="index"
                style="width: 25%"
                :to="{ name: page.slug }"
            >
                <v-icon>{{ page.icon }}</v-icon>

                <div style="margin-top: 2px; font-size: 88%">
                    {{ page.name }}
                </div>
            </v-btn>
        </v-bottom-navigation>
    </v-layout>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "user-base",

    props: {
        moduleName: {
            type: String,
            default: null,
        },
    },

    setup(props) {
        const store = usePageStore();

        store.moduleName = props.moduleName;

        const {
            auth,
            appsMenus,
            geoInitialized,
            navigationState,
            overlay,
            railMode,
            snackbar,
            theme,
        } = storeToRefs(store);
        const { initModule } = store;

        return {
            auth,
            appsMenus,
            geoInitialized,
            navigationState,
            overlay,
            railMode,
            snackbar,
            theme,

            initModule,
        };
    },

    created() {
        if (!this.auth) {
            this.$router.push({ name: "welcome-page" });
            return;
        }

        this.initModule({ mobile: true });
    },
};
</script>
