<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        :order="1"
        height="72"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-btn icon="arrow_back" @click="openFormData"></v-btn>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn v-if="!hideUpdate" icon @click="postFormEdit">
            <v-icon>save</v-icon>

            <v-tooltip activator="parent" location="bottom">Simpan</v-tooltip>
        </v-btn>

        <v-btn v-if="withHelpdesk" icon @click="helpState = !helpState">
            <v-icon
                :style="
                    helpState
                        ? 'transform: rotate(180deg)'
                        : 'transform: rotate(0deg)'
                "
                >{{ helpState ? "close" : "menu_open" }}</v-icon
            >

            <v-tooltip activator="parent" location="bottom"
                >Informasi</v-tooltip
            >
        </v-btn>
    </v-app-bar>

    <v-main style="min-height: 100dvh">
        <v-container>
            <page-paper :max-width="maxWidth">
                <slot
                    :combos="combos"
                    :record="record"
                    :theme="theme"
                    :store="store"
                ></slot>
            </page-paper>
        </v-container>
    </v-main>

    <form-help mode="edit" :withActivityLogs="withActivityLogs">
        <template v-slot:info>
            <slot name="info" :theme="theme"></slot>
        </template>

        <template v-slot:default>
            <slot name="help" :theme="theme"></slot>
        </template>
    </form-help>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-edit",

    props: {
        beforePost: Function,

        dataFromStore: Boolean,

        hideUpdate: Boolean,

        maxWidth: {
            type: String,
            default: "560px",
        },

        withHelpdesk: Boolean,

        withActivityLogs: Boolean,
    },

    setup(props) {
        const store = usePageStore();

        store.beforePost = props.beforePost;
        store.activityLog = props.withActivityLogs;

        const {
            combos,
            helpState,
            highlight,
            key,
            page,
            pageKey,
            record,
            theme,
        } = storeToRefs(store);

        const { getPageData, openFormData, postFormEdit } = store;

        return {
            combos,
            helpState,
            highlight,
            key,
            page,
            pageKey,
            record,
            theme,

            getPageData,
            openFormData,
            postFormEdit,

            store,
        };
    },

    created() {
        this.getPageData(this.dataFromStore);
    },
};
</script>
