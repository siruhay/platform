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
                <v-card-text>
                    <slot
                        :combos="combos"
                        :record="record"
                        :theme="theme"
                        :store="store"
                    ></slot>
                </v-card-text>
            </page-paper>
        </v-container>
    </v-main>
    <!-- <v-sheet color="transparent" class="position-relative">
        <v-toolbar :color="theme">
            <v-btn
                color="white"
                icon="arrow_back"
                @click="openFormData"
            ></v-btn>

            <v-toolbar-title
                class="text-body-2 font-weight-bold text-uppercase"
                >{{ page.name }}</v-toolbar-title
            >

            <v-spacer></v-spacer>

            <v-btn v-if="!hideSave" color="white" icon @click="postFormCreate">
                <v-icon>save</v-icon>

                <v-tooltip activator="parent" location="bottom"
                    >Simpan</v-tooltip
                >
            </v-btn>

            <v-btn
                v-if="withHelpdesk"
                :color="helpState ? 'white' : `${theme}-lighten-3`"
                icon
                @click="helpState = !helpState"
            >
                <v-icon
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
            :color="`${theme}-lighten-4`"
            class="mx-auto position-absolute w-100"
            height="100%"
        ></v-sheet>

        <v-sheet
            :color="`${theme}`"
            class="mx-auto position-absolute w-100 rounded-b-xl"
            height="192"
        ></v-sheet>

        <v-sheet
            class="position-relative bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none px-4"
            height="calc(100dvh - 72px)"
            width="100%"
        >
            <div
                class="position-absolute text-center"
                style="width: calc(100% - 32px); z-index: 1"
            >
                <div
                    :style="`max-width: ${maxWidth}`"
                    class="d-flex flex-column align-center justify-center position-relative mx-auto"
                >
                    <v-card :color="`${theme}`" rounded="pill">
                        <v-card-text class="pa-1">
                            <v-avatar
                                :color="`${highlight}-lighten-2`"
                                size="64"
                                style="font-size: 22px"
                            >
                                <v-icon :color="`${theme}-darken-1`">{{
                                    page.icon
                                }}</v-icon>
                            </v-avatar>
                        </v-card-text>
                    </v-card>

                    <div
                        :class="`text-${theme}-lighten-4`"
                        class="text-caption text-white position-absolute pt-3 font-weight-bold text-uppercase"
                        style="top: 0; right: 0"
                    >
                        create
                    </div>
                </div>
            </div>

            <v-sheet
                :style="`max-width: ${maxWidth}`"
                class="mt-9 pt-9 mx-auto"
                min-height="calc(100dvh - 175px)"
                rounded="lg"
            >
                <slot
                    :combos="combos"
                    :record="record"
                    :theme="theme"
                    :store="store"
                ></slot>
            </v-sheet>
        </v-sheet>
    </v-sheet> -->

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
