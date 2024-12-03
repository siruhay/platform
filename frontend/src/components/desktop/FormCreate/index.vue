<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        :order="1"
        height="72"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-btn
            icon
            @click="
                navbackTo ? $router.push({ name: navbackTo }) : openFormData()
            "
        >
            <v-icon>arrow_back</v-icon>
        </v-btn>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn v-if="!hideSave" icon @click="postFormCreate">
            <v-icon>save</v-icon>

            <v-tooltip activator="parent" location="bottom">Simpan</v-tooltip>
        </v-btn>

        <slot
            name="toolbar"
            :record="record"
            :theme="theme"
            :store="store"
        ></slot>

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

    <form-help mode="create" :withActivityLogs="withActivityLogs">
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
    name: "form-create",

    props: {
        beforePost: Function,
        hideSave: Boolean,

        maxWidth: {
            type: String,
            default: "560px",
        },

        navbackTo: String,
        withActivityLogs: Boolean,
        withHelpdesk: Boolean,
    },

    setup(props) {
        const store = usePageStore();

        store.beforePost = props.beforePost;
        store.activityLog = false;

        const { combos, helpState, highlight, page, pageKey, record, theme } =
            storeToRefs(store);

        const { getCreateData, openFormData, postFormCreate } = store;

        return {
            combos,
            helpState,
            highlight,
            page,
            pageKey,
            record,
            theme,

            getCreateData,
            openFormData,
            postFormCreate,

            store,
        };
    },

    created() {
        this.getCreateData();
    },
};
</script>
