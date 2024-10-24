<template>
    <v-app-bar
        scroll-behavior="hide elevate"
        scroll-threshold="87"
        :color="`${theme}`"
    >
        <v-toolbar-title class="text-body-2 font-weight-bold"
            >SiMASTEN</v-toolbar-title
        >

        <v-spacer></v-spacer>

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

    <v-sheet
        :color="`${theme}`"
        class="mx-auto position-fixed w-100 rounded-b-xl"
        height="256"
    ></v-sheet>

    <v-main>
        <v-container class="pt-0">
            <slot :modules="modules" :store="store" :theme="theme"></slot>
        </v-container>
    </v-main>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "user-apps",

    props: {
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

        const {
            auth,
            highlight,
            impersonated,
            modules,
            navigationState,
            railMode,
            theme,
        } = storeToRefs(store);

        const { getUserModules, initPage, mapUserModule } = store;

        return {
            auth,
            highlight,
            impersonated,
            modules,
            navigationState,
            railMode,
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
