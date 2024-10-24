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

        <v-btn icon @click="$emit('click:tasklist')">
            <v-icon>shopping_cart</v-icon>
        </v-btn>

        <v-btn icon @click="$emit('click:notification')">
            <v-icon>notifications</v-icon>
        </v-btn>

        <v-btn icon>
            <v-icon class="with-shadow">power_settings_new</v-icon>

            <form-confirm icon="door_back" title="Keluar dari SiMASTEN?">
                <div class="text-caption text-grey-darken-1">
                    Saat Anda keluar dari aplikasi ini, semua data temporer yang
                    tersimpan pada perangkat ini juga akan di hapus.
                </div>

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
                                @click="signOut(() => (isActive.value = false))"
                                >KELUAR</v-btn
                            >
                        </v-col>
                    </v-row>
                </template>
            </form-confirm>
        </v-btn>
    </v-app-bar>

    <v-sheet
        :color="`${theme}`"
        class="mx-auto position-fixed w-100 rounded-b-xl"
        height="256"
    ></v-sheet>

    <v-main>
        <v-container class="pt-0">
            <slot :record="record" :store="store" :theme="theme"></slot>
        </v-container>
    </v-main>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "user-home",

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
        "click:notification": null,
        "click:tasklist": null,
    },

    setup(props) {
        const store = usePageStore();

        store.pageName = props.pageName;
        store.pageKey = props.pageKey;

        const {
            auth,
            highlight,
            modules,
            navigationState,
            record,
            railMode,
            theme,
        } = storeToRefs(store);

        const { getUserDashboard, initPage, signOut } = store;

        return {
            auth,
            highlight,
            modules,
            navigationState,
            railMode,
            record,
            theme,

            getUserDashboard,
            initPage,
            signOut,
            store,
        };
    },

    created() {
        this.initPage();
    },

    beforeMount() {
        this.getUserDashboard();
    },
};
</script>
