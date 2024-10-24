<template>
    <v-layout :class="`bg-${theme}-lighten-5`">
        <v-navigation-drawer
            :color="`${theme}-darken-1`"
            rail-width="65"
            disable-resize-watcher
            permanent
            :rail="railMode"
        >
            <template v-slot:prepend>
                <part-logo></part-logo>
            </template>

            <template v-slot:default>
                <div
                    :class="`bg-${theme}-darken-1`"
                    class="position-relative h-100 py-2 px-1 overflow-hidden"
                >
                    <v-list
                        :active-class="`bg-${theme}-darken-1`"
                        class="h-100"
                        nav
                    >
                        <v-list-item
                            v-for="(item, index) in sideMenus"
                            :key="index"
                            :to="{ name: item.slug }"
                        >
                            <template v-slot:prepend="{ isActive }">
                                <v-icon
                                    :color="
                                        isActive
                                            ? `white`
                                            : `${theme}-lighten-2`
                                    "
                                    :icon="item.icon"
                                ></v-icon>
                            </template>

                            <template v-slot:default="{ isActive }">
                                <v-list-item-title
                                    :class="
                                        isActive
                                            ? `white`
                                            : `text-${theme}-lighten-2`
                                    "
                                    >{{ item.name }}</v-list-item-title
                                >
                            </template>
                        </v-list-item>
                    </v-list>
                </div>
            </template>
        </v-navigation-drawer>

        <router-view key="pagebase" />

        <v-overlay
            :model-value="overlay"
            class="align-center justify-center"
            opacity="0.2"
            scroll-strategy="block"
            persistent
        >
            <v-progress-circular
                :color="theme"
                :width="3"
                size="64"
                indeterminate
            >
                <template v-slot:default><small>loading</small></template>
            </v-progress-circular>
        </v-overlay>

        <v-snackbar
            :color="snackbar.color"
            :timeout="1500"
            v-model="snackbar.state"
            multi-line
        >
            {{ snackbar.text }}

            <template v-slot:actions>
                <v-btn
                    color="white"
                    variant="text"
                    @click="snackbar.state = false"
                >
                    Close
                </v-btn>
            </template>
        </v-snackbar>
    </v-layout>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "page-base",

    props: {
        moduleName: {
            type: String,
            default: null,
        },

        moduleKey: {
            type: String,
            default: null,
        },

        moduleType: {
            type: String,
            default: null,
        },
    },

    setup(props) {
        const store = usePageStore();

        store.moduleName = props.moduleName;
        store.moduleKey = props.moduleKey;
        store.moduleType = props.moduleType;

        const {
            auth,
            dockMenus,
            overlay,
            railMode,
            sideMenus,
            snackbar,
            theme,
        } = storeToRefs(store);

        const { initModule } = store;

        return {
            auth,
            dockMenus,
            initModule,
            overlay,
            railMode,
            sideMenus,
            snackbar,
            theme,
        };
    },

    created() {
        this.initModule({ mobile: false });
    },
};
</script>
