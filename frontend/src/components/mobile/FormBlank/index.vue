<template>
    <v-app-bar
        :color="`${theme}`"
        scroll-behavior="hide elevate"
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

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn
            v-if="withHelpdesk"
            :color="helpState ? 'white' : `${theme}-lighten-3`"
            icon
            @click="helpState = !helpState"
        >
            <v-icon
                class="with-shadow"
                :style="
                    helpState
                        ? 'transform: rotate(180deg)'
                        : 'transform: rotate(0deg)'
                "
                >menu_open</v-icon
            >

            <v-tooltip activator="parent" location="bottom"
                >Informasi</v-tooltip
            >
        </v-btn>
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
                min-height="calc(100dvh - 116px)"
                rounded="lg"
                flat
            >
                <slot
                    :combos="combos"
                    :mapResponseData="mapResponseData"
                    :record="record"
                    :theme="theme"
                    :store="store"
                ></slot>
            </v-sheet>
        </v-sheet>
    </v-main>

    <form-help mode="show" :withActivityLogs="withActivityLogs">
        <template v-slot:info>
            <slot name="info" :theme="theme"></slot>
        </template>

        <template v-slot:default>
            <slot
                name="helpdesk"
                :mapResponseData="mapResponseData"
                :record="record"
                :theme="theme"
                :store="store"
            ></slot>
        </template>
    </form-help>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-blank",

    props: {
        beforePost: Function,
        blankForm: Boolean,
        contentClass: String,
        dataFromStore: Boolean,
        hideEdit: Boolean,
        hideDelete: Boolean,
        navbackTo: String,
        title: String,
        width: {
            type: String,
            default: "500px",
        },
        withHelpdesk: Boolean,
        withActivityLogs: Boolean,
    },

    setup(props) {
        const store = usePageStore();

        store.beforePost = props.beforePost;
        store.activityLog = props.withActivityLogs;
        store.navigationState = false;

        const {
            combos,
            helpState,
            highlight,
            navigationState,
            page,
            parentName,
            record,
            theme,
        } = storeToRefs(store);

        const { getPageData, mapResponseData, openFormData } = store;

        return {
            combos,
            helpState,
            highlight,
            navigationState,
            page,
            parentName,
            record,
            theme,

            getPageData,
            mapResponseData,
            openFormData,

            store,
        };
    },

    mounted() {
        this.getPageData();
    },

    beforeUnmount() {
        this.navigationState = true;
    },
};
</script>
