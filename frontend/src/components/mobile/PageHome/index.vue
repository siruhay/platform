<template>
    <v-app-bar
        :color="theme"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            module.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <slot name="toolbar"></slot>

        <v-btn icon @click="gotoAccountService">
            <v-icon>exit_to_app</v-icon>
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
                    <form-icon>
                        <v-img
                            :src="
                                auth.avatar ??
                                `/avatars/${auth.gender}-avatar.svg`
                            "
                        ></v-img>
                    </form-icon>

                    <div
                        :class="`text-${theme}-lighten-4`"
                        class="text-caption text-white position-absolute font-weight-bold text-uppercase pt-1 text-right"
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
                            {{ page.name }}
                        </div>
                    </div>
                </div>
            </v-sheet>

            <v-sheet
                class="position-relative pt-7"
                elevation="1"
                min-height="calc(100dvh - 172px)"
                rounded="lg"
                flat
            >
                <v-card-text :class="dockMenus.length <= 0 ? 'py-2' : ''">
                    <v-row dense>
                        <v-col
                            cols="3"
                            v-for="(page, index) in dockMenus"
                            :key="index"
                        >
                            <v-card
                                :border="`border-${theme} thin`"
                                :color="`${theme}-lighten-5`"
                                class="text-center"
                                rounded="md"
                                width="100%"
                                flat
                                @click="openPage(page)"
                            >
                                <v-card-text class="pa-3 pb-1">
                                    <v-avatar
                                        :color="theme"
                                        size="36"
                                        style="font-size: 11px"
                                    >
                                        <v-icon
                                            :color="highlight"
                                            :icon="page.icon"
                                            size="large"
                                        ></v-icon>
                                    </v-avatar>
                                </v-card-text>

                                <v-card-text
                                    :class="`text-${theme}-darken-1`"
                                    class="pa-1"
                                    style="max-height: 38px"
                                >
                                    <div
                                        class="text-caption d-flex align-center justify-center overflow-hidden w-100"
                                        style="
                                            font-size: 9px !important;
                                            line-height: 1.1em;
                                            height: 24px;
                                        "
                                    >
                                        {{ page.name }}
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-divider>
                    <v-chip
                        :color="theme"
                        density="comfortable"
                        size="small"
                        variant="flat"
                    >
                        <v-icon>more_horiz</v-icon>
                    </v-chip>
                </v-divider>

                <slot :record="record" :store="store" :theme="theme"></slot>
            </v-sheet>
        </v-sheet>
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
            dockMenus,
            highlight,
            module,
            page,
            railMode,
            record,
            theme,
        } = storeToRefs(store);

        const { getDashboard } = store;

        return {
            auth,
            highlight,
            module,
            page,
            railMode,
            record,
            dockMenus,
            theme,

            getDashboard,
            store,
        };
    },

    created() {
        this.getDashboard((response) => {
            this.$emit("initialized", { record: response });
        });
    },

    methods: {
        gotoAccountService: function () {
            this.$router.push({ name: "account-service" });
        },

        openPage: function (page) {
            this.$router.push({ name: page.slug });
        },
    },
};
</script>
