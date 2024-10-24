<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        height="72"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-app-bar-nav-icon @click="railMode = !railMode"></v-app-bar-nav-icon>

        <v-toolbar-title class="text-body-2 font-weight-medium">
            LAYANAN
        </v-toolbar-title>

        <v-spacer></v-spacer>

        <slot
            name="toolbar"
            :combos="combos"
            :modules="modules"
            :statuses="statuses"
            :store="store"
            :theme="theme"
        ></slot>

        <v-btn
            icon
            v-if="impersonated"
            @click="
                $emit('click:impersonateLeave', {
                    mapUserModule: mapUserModule,
                })
            "
        >
            <v-icon>no_accounts</v-icon>
        </v-btn>
    </v-app-bar>

    <v-main style="min-height: 100dvh">
        <v-container>
            <slot
                :combos="combos"
                :modules="modules"
                :statuses="statuses"
                :store="store"
                :theme="theme"
            ></slot>
        </v-container>
    </v-main>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "user-apps",

    props: {
        clearFilters: Boolean,

        maxWidth: {
            type: [String, Number],
            default: "900",
        },

        pageName: {
            type: String,
            default: null,
        },

        pageKey: {
            type: String,
            default: null,
        },
    },

    emits: {
        "click:impersonateLeave": null,
    },

    setup(props) {
        const store = usePageStore();

        store.pageName = props.pageName;
        store.pageKey = props.pageKey;

        if (props.clearFilters) {
            store.clearFilters();
        }

        const {
            auth,
            combos,
            highlight,
            impersonated,
            modules,
            navigationState,
            railMode,
            statuses,
            theme,
        } = storeToRefs(store);

        const { getUserModules, initPage, mapUserModule } = store;

        return {
            auth,
            combos,
            highlight,
            impersonated,
            modules,
            navigationState,
            railMode,
            statuses,
            theme,

            getUserModules,
            initPage,
            mapUserModule,
            store,
        };
    },

    created() {
        this.initPage();
    },

    beforeMount() {
        this.getUserModules();
    },
};
</script>
