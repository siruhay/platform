<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        :order="1"
        height="72"
        scroll-behavior="elevate"
        scroll-threshold="87"
    >
        <v-btn
            icon
            v-if="parentName || navbackTo"
            @click="
                navbackTo
                    ? $router.push({ name: navbackTo })
                    : $router.push({ name: parentName })
            "
        >
            <v-icon>arrow_back</v-icon>
        </v-btn>

        <v-app-bar-nav-icon
            v-else
            @click="railMode = !railMode"
        ></v-app-bar-nav-icon>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <slot name="toolbar"></slot>

        <v-btn v-if="showSidenav" icon @click="sidenavState = !sidenavState">
            <v-icon>{{ sidenavState ? "close" : "tune" }}</v-icon>
        </v-btn>
    </v-app-bar>

    <page-sidenav :title="sidenavTitle"></page-sidenav>

    <v-main style="min-height: 100dvh">
        <v-container>
            <slot
                :combos="combos"
                :highlight="highlight"
                :record="record"
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
    name: "page-blank",

    props: {
        maxWidth: {
            type: [String, Number],
            default: "900",
        },

        navbackTo: String,

        pagePath: {
            type: String,
            default: null,
        },

        pageName: {
            type: String,
            default: null,
        },

        pageKey: {
            type: String,
            default: null,
        },

        parentName: {
            type: String,
            default: null,
        },

        parentKey: {
            type: String,
            default: null,
        },

        sidenavTitle: {
            type: String,
            default: "Utility",
        },

        title: String,

        showSidenav: {
            type: Boolean,
            default: false,
        },
    },

    setup(props) {
        const store = usePageStore();

        store.pageKey = props.pageKey;
        store.pageName = props.pageName;
        store.pagePath = props.pagePath;
        store.sidenavState = props.showSidenav;

        const {
            combos,
            highlight,
            module,
            navigationState,
            page,
            sidenavState,
            railMode,
            record,
            theme,
        } = storeToRefs(store);

        const { getPageData } = store;

        return {
            combos,
            highlight,
            module,
            navigationState,
            page,
            sidenavState,
            railMode,
            record,
            theme,

            getPageData,
            store,
        };
    },

    created() {
        this.getPageData();
    },
};
</script>
