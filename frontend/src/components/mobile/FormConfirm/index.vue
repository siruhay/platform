<template>
    <v-dialog activator="parent" max-width="400" persistent>
        <template v-slot:default="{ isActive }">
            <v-sheet elevation="9" rounded="lg">
                <v-card-text class="text-center">
                    <v-icon class="v-icon--size-dialog text-blue-grey">{{
                        icon
                    }}</v-icon>

                    <div
                        class="text-caption text-subtitle-1 font-weight-medium my-1"
                    >
                        {{ title }}
                    </div>

                    <div
                        class="text-grey-darken-1"
                        style="line-height: 1.15rem"
                    >
                        <slot></slot>
                    </div>
                </v-card-text>

                <v-card-text class="pt-0">
                    <slot name="actions" :isActive="isActive"></slot>
                </v-card-text>
            </v-sheet>
        </template>
    </v-dialog>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-confirm",

    props: {
        icon: {
            type: String,
            default: "power_settings_new",
        },

        title: {
            type: String,
            default: "keluar aplikasi",
        },
    },

    setup() {
        const store = usePageStore();

        const { highlight, theme } = storeToRefs(store);

        return {
            highlight,
            theme,
        };
    },
};
</script>
