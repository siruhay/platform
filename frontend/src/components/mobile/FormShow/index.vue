<template>
    <v-app-bar
        :color="`${theme}`"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-btn
            icon
            @click="
                navbackTo ? $router.push({ name: navbackTo }) : openFormData()
            "
        >
            <v-icon class="with-shadow">arrow_back</v-icon>
        </v-btn>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <template v-if="softdelete">
            <v-btn icon>
                <v-icon class="with-shadow">restore</v-icon>

                <form-confirm icon="restore" title="pulihkan">
                    Proses ini akan memulihkan data ini dari status trashed.

                    <template v-slot:actions="{ isActive }">
                        <v-spacer></v-spacer>

                        <v-btn
                            :color="`${theme}-lighten-2`"
                            class="text-white mr-2"
                            variant="flat"
                            @click="isActive.value = false"
                            >batal</v-btn
                        >

                        <v-btn
                            color="deep-orange"
                            text="Pulihkan"
                            @click="
                                postFormRestore(() => (isActive.value = false))
                            "
                        ></v-btn>
                    </template>
                </form-confirm>
            </v-btn>

            <v-btn color="orange" icon>
                <v-icon class="with-shadow">delete_forever</v-icon>

                <form-confirm icon="delete_forever" title="Hapus Permanen?">
                    Proses ini akan menghapus data secara permanen, proses ini
                    tidak dapat di pulihkan setelah di lakukan.

                    <template v-slot:actions="{ isActive }">
                        <v-spacer></v-spacer>

                        <v-btn
                            :color="`${theme}-lighten-2`"
                            class="text-white mr-2"
                            variant="flat"
                            @click="isActive.value = false"
                            >batal</v-btn
                        >

                        <v-btn
                            color="deep-orange"
                            text="Hapus"
                            @click="
                                postFormForceDelete(
                                    () => (isActive.value = false)
                                )
                            "
                        ></v-btn>
                    </template>
                </form-confirm>
            </v-btn>
        </template>

        <template v-else>
            <v-btn v-if="!hideEdit" icon @click="openFormEdit">
                <v-icon class="with-shadow">edit</v-icon>

                <v-tooltip activator="parent" location="bottom">Ubah</v-tooltip>
            </v-btn>

            <v-btn v-if="!hideDelete" color="orange" icon>
                <v-icon class="with-shadow">delete</v-icon>

                <form-confirm icon="delete" title="Hapus data ini?">
                    Proses ini akan juga menghapus semua data yang terkait pada
                    data ini.

                    <template v-slot:actions="{ isActive }">
                        <v-row dense>
                            <v-col cols="6">
                                <v-btn
                                    :color="theme"
                                    rounded="pill"
                                    variant="outlined"
                                    block
                                    @click="isActive.value = false"
                                    >BATAL</v-btn
                                >
                            </v-col>

                            <v-col cols="6">
                                <v-btn
                                    :color="theme"
                                    rounded="pill"
                                    variant="flat"
                                    block
                                    @click="
                                        postFormDelete(
                                            () => (isActive.value = false)
                                        )
                                    "
                                    >HAPUS</v-btn
                                >
                            </v-col>
                        </v-row>
                    </template>
                </form-confirm>

                <v-tooltip activator="parent" location="bottom"
                    >Hapus</v-tooltip
                >
            </v-btn>
        </template>

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
                        class="text-caption text-white position-absolute font-weight-bold text-uppercase text-left"
                        style="
                            top: 8px;
                            left: 0;
                            font-size: 0.63rem !important;
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

                    <div
                        v-if="!hideDataTag"
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
                            show: {{ record[key] }}
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
        <template v-slot:feed>
            <slot name="feed" :theme="theme"></slot>
        </template>

        <template v-slot:info>
            <slot
                name="info"
                :mapResponseData="mapResponseData"
                :record="record"
                :theme="theme"
                :store="store"
            ></slot>
        </template>

        <template v-slot:icon>
            <slot name="icon" :theme="theme"></slot>
        </template>
    </form-help>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-show",

    props: {
        beforePost: Function,
        blankForm: Boolean,
        contentClass: String,
        dataFromStore: Boolean,
        hideEdit: Boolean,
        hideDataTag: Boolean,
        hideDelete: Boolean,
        navbackTo: String,
        routePrefix: String,
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
        store.routePrefix = props.routePrefix;
        store.navigationState = false;

        const {
            combos,
            helpState,
            highlight,
            key,
            navigationState,
            page,
            pageKey,
            softdelete,
            record,
            theme,
            title,
        } = storeToRefs(store);

        const {
            getPageData,
            mapResponseData,
            openFormData,
            openFormEdit,
            postFormDelete,
            postFormForceDelete,
            postFormRestore,
        } = store;

        return {
            combos,
            helpState,
            highlight,
            key,
            navigationState,
            page,
            pageKey,
            record,
            softdelete,
            theme,
            title,

            getPageData,
            mapResponseData,
            openFormData,
            openFormEdit,
            postFormDelete,
            postFormForceDelete,
            postFormRestore,

            store,
        };
    },

    mounted() {
        this.getPageData(this.dataFromStore);
    },

    beforeUnmount() {
        this.navigationState = true;
    },
};
</script>
