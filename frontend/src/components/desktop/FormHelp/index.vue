<template>
    <v-navigation-drawer
        v-model="helpState"
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
            <v-card-text>
                <v-alert border="start" color="green" variant="tonal">
                    <slot name="feed">
                        <div class="text-caption text-justify">
                            Form ini berfungsi untuk menampilkan form data.
                        </div>
                    </slot>
                </v-alert>

                <slot name="info"></slot>

                <slot name="icon">
                    <widget-icon :mode="mode"></widget-icon>
                </slot>

                <widget-activity v-if="withActivityLogs"></widget-activity>
            </v-card-text>
        </template>
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

        title: {
            type: String,
            default: "information",
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
