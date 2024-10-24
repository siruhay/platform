<template>
    <v-navigation-drawer
        v-model="sidenavState"
        color="white"
        location="right"
        width="360"
        disable-resize-watcher
        style="height: calc(100dvh)"
    >
        <template v-slot:prepend>
            <v-toolbar color="white" height="72">
                <v-toolbar-title class="text-overline">
                    {{ title }}
                </v-toolbar-title>

                <v-spacer></v-spacer>
            </v-toolbar>

            <v-divider></v-divider>
        </template>

        <template v-slot:default>
            <v-sheet
                class="position-relative bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none"
                height="calc(100dvh - 75px)"
                width="100%"
            >
            </v-sheet>
        </template>
    </v-navigation-drawer>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "page-sidenav",

    props: {
        title: String,
    },

    setup() {
        const store = usePageStore();

        const { sidenavState } = storeToRefs(store);

        return {
            sidenavState,
        };
    },
};
</script>
