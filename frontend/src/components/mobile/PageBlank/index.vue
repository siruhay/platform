<template>
    <v-app-bar
        :color="theme"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-btn icon v-if="parentName">
            <v-icon>arrow_back</v-icon>
        </v-btn>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            module.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>
    </v-app-bar>

    <v-sheet
        :color="`${theme}`"
        class="mx-auto position-fixed w-100 rounded-b-xl"
        height="256"
    ></v-sheet>

    <v-main>
        <v-sheet class="bg-transparent position-relative px-4 pt-9 pb-4">
            <v-sheet
                class="position-absolute"
                color="transparent"
                width="calc(100% - 32px)"
                style="top: 0; z-index: 1"
            >
                <div class="d-flex justify-center">
                    <form-icon></form-icon>

                    <div
                        :class="`text-${theme}-lighten-4`"
                        class="text-caption text-white position-absolute font-weight-bold text-uppercase text-right"
                        style="
                            font-size: 0.63rem !important;
                            top: 8px;
                            right: 0;
                            width: calc(50% - 30px);
                        "
                    >
                        <div
                            class="d-inline-block text-truncate"
                            style="max-width: 100%"
                        >
                            {{ title }}
                        </div>
                    </div>
                </div>
            </v-sheet>

            <v-sheet
                class="position-relative pt-7"
                elevation="1"
                min-height="calc(100dvh - 172px)"
                rounded="lg"
                flat
            >
                <slot
                    :combos="combos"
                    :highlight="highlight"
                    :record="record"
                    :store="store"
                    :theme="theme"
                ></slot>
            </v-sheet>
        </v-sheet>
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
            default: "500px",
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

        title: String,

        withProperty: {
            type: Boolean,
            default: false,
        },
    },

    setup(props) {
        const store = usePageStore();

        store.pageKey = props.pageKey;
        store.pageName = props.pageName;
        store.pagePath = props.pagePath;

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
