<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        height="72"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-app-bar-nav-icon @click="railMode = !railMode"></v-app-bar-nav-icon>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            module.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <slot
            name="toolbar"
            :combos="combos"
            :record="record"
            :statuses="statuses"
            :store="store"
            :theme="theme"
        ></slot>

        <v-btn icon @click="gotoAccountDashboard">
            <v-icon>exit_to_app</v-icon>
        </v-btn>
    </v-app-bar>

    <v-main style="min-height: 100dvh">
        <v-container>
            <page-paper max-width="100%">
                <div
                    class="d-flex justify-center mt-3"
                    v-if="dockMenus.length > 0"
                >
                    <v-chip
                        :color="theme"
                        class="text-white"
                        density="comfortable"
                        size="small"
                        variant="flat"
                    >
                        Module Pages
                    </v-chip>
                </div>

                <v-sheet
                    v-if="dockMenus.length > 0"
                    class="mx-auto bg-transparent pa-4"
                    :style="
                        $vuetify.display.width >= 1280
                            ? { maxWidth: '1164px' }
                            : { maxWidth: '640px' }
                    "
                >
                    <v-row justify="center" dense>
                        <v-col
                            v-for="(page, index) in dockMenus"
                            class="d-flex justify-center"
                            cols="1"
                            sm="2"
                            :key="index"
                        >
                            <widget-apps
                                :color="`${highlight}-darken-1`"
                                :highlight="`white`"
                                :icon="page.icon"
                                :label="page.name"
                                rounded="xl"
                                @click="openPage(page)"
                            ></widget-apps>
                        </v-col>
                    </v-row>
                </v-sheet>
            </page-paper>

            <slot
                :combos="combos"
                :record="record"
                :statuses="statuses"
                :store="store"
                :theme="theme"
            ></slot>
        </v-container>
    </v-main>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "page-home",

    emits: {
        initialized: null,
    },

    props: {
        maxWidth: {
            type: String,
            default: "900px",
        },

        pageName: {
            type: String,
            default: null,
        },

        pageKey: {
            type: String,
            default: null,
        },
    },

    setup(props) {
        const store = usePageStore();

        store.pageName = props.pageName;
        store.pageKey = props.pageKey;

        const {
            auth,
            combos,
            dockMenus,
            highlight,
            module,
            page,
            railMode,
            record,
            statuses,
            theme,
        } = storeToRefs(store);

        const { getDashboard } = store;

        return {
            auth,
            combos,
            dockMenus,
            highlight,
            module,
            page,
            railMode,
            record,
            statuses,
            theme,

            getDashboard,
            store,
        };
    },

    mounted() {
        this.getDashboard((response) => {
            this.$emit("initialized", { record: response });
        });
    },

    methods: {
        gotoAccountDashboard: function () {
            this.$router.push({ name: "account-dashboard" });
        },

        openPage: function (page) {
            this.$router.push({ name: page.slug });
        },
    },
};
</script>
