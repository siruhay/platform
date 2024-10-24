<template>
    <v-toolbar :color="theme">
        <v-btn
            icon
            v-if="parentName"
            @click="
                manualBacknav
                    ? $emit('click:backnav', $event)
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
    </v-toolbar>

    <v-sheet
        :color="`${theme}`"
        class="mx-auto position-absolute w-100 rounded-b-xl"
        height="192"
    ></v-sheet>

    <v-responsive
        :height="
            navigationState ? `calc(100dvh - 120px)` : `calc(100dvh - 64px)`
        "
        class="bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none px-4"
        content-class="position-relative"
    >
        <v-sheet
            class="position-absolute text-center w-100 pt-1"
            color="transparent"
            style="z-index: 1"
        >
            <div class="d-flex justify-center position-relative">
                <v-sheet :color="`${theme}`" elevation="4" rounded="pill">
                    <v-card-text class="pa-1">
                        <v-avatar
                            :color="`${highlight}-lighten-2`"
                            size="52"
                            style="font-size: 22px"
                        >
                            <v-icon :color="`${theme}-darken-1`">{{
                                page.icon
                            }}</v-icon>
                        </v-avatar>
                    </v-card-text>
                </v-sheet>
            </div>
        </v-sheet>

        <v-sheet
            class="mt-9 pt-7"
            min-height="200px"
            elevation="1"
            rounded="lg"
        >
            <slot
                :combos="combos"
                :mapResponseData="mapResponseData"
                :record="record"
                :theme="theme"
                :store="store"
            ></slot>
        </v-sheet>

        <div class="py-2"></div>
    </v-responsive>

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
        manualBacknav: Boolean,
        width: {
            type: String,
            default: "500px",
        },
        withHelpdesk: Boolean,
        withActivityLogs: Boolean,
    },

    emits: {
        "click:backnav": null,
    },

    setup(props) {
        const store = usePageStore();

        store.beforePost = props.beforePost;
        store.activityLog = props.withActivityLogs;

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
};
</script>
