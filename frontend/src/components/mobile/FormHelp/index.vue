<template>
    <v-navigation-drawer
        :color="`${theme}-lighten-4`"
        location="right"
        width="360"
        v-model="helpState"
        disable-resize-watcher
        style="height: 100%; top: 0; z-index: 1009"
    >
        <v-sheet class="position-relative" color="transparent" height="100dvh">
            <v-toolbar :color="theme">
                <v-btn icon @click="helpState = false">
                    <v-icon>close</v-icon>
                </v-btn>

                <v-toolbar-title class="text-white text-overline">
                    informasi
                </v-toolbar-title>
            </v-toolbar>

            <v-sheet
                :color="`${theme}`"
                class="mx-auto position-absolute rounded-b-xl"
                height="192"
                width="360"
            ></v-sheet>

            <v-responsive
                height="calc(100dvh - 64px)"
                class="bg-transparent overflow-x-hidden overflow-y-auto px-4 scrollbar-none"
                content-class="position-relative"
            >
                <v-sheet
                    class="position-absolute text-center w-100"
                    color="transparent"
                    style="z-index: 1"
                >
                    <div class="d-flex justify-center position-relative">
                        <div
                            class="position-relative text-blue-grey mx-auto"
                            style="width: 64px"
                        >
                            <div class="circle">
                                <div
                                    class="position-relative h-100 w-100 text-white"
                                >
                                    <v-avatar
                                        :color="`${theme}-lighten-4`"
                                        elevation="6"
                                        size="52"
                                    >
                                        <v-icon color="grey-darken-2"
                                            >menu_open</v-icon
                                        >
                                    </v-avatar>
                                </div>
                            </div>
                        </div>

                        <div
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
                                {{ page.title }}
                            </div>
                        </div>
                    </div>
                </v-sheet>

                <v-sheet
                    class="mt-9 pt-7"
                    elevation="1"
                    min-height="calc(100dvh - 116px)"
                    rounded="lg"
                >
                    <v-card-text>
                        <div class="text-overline">Form</div>
                        <v-divider></v-divider>

                        <div class="text-caption text-grey-darken-1 pt-2">
                            <slot name="info"
                                >Form ini berfungsi untuk menampilkan form
                                data.</slot
                            >
                        </div>

                        <slot name="help" :theme="theme"></slot>

                        <widget-icon :mode="mode"></widget-icon>

                        <widget-activity
                            v-if="withActivityLogs"
                        ></widget-activity>
                    </v-card-text>
                </v-sheet>

                <div class="py-2"></div>
            </v-responsive>
        </v-sheet>
    </v-navigation-drawer>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-help",

    props: {
        mode: {
            type: String,
            default: "show",
        },

        withActivityLogs: Boolean,
    },

    setup() {
        const store = usePageStore();

        const { helpState, logs, page, theme } = storeToRefs(store);

        return {
            helpState,
            logs,
            page,
            theme,
        };
    },
};
</script>

<style scoped>
.v-navigation-drawer--right {
    border-left-width: 0;
}
</style>
