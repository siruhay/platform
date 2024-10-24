<template>
    <div class="v-list-wrap position-relative overflow-hidden">
        <v-list-item
            class="bg-white"
            :ripple="false"
            :value="value"
            style="z-index: 1"
        >
            <template v-slot:prepend>
                <v-avatar :color="`${theme}-lighten-4`">
                    <v-icon class="with-shadow" color="white">{{
                        Object.hasOwn(value, "icon")
                            ? value["icon"]
                            : icon ?? page.icon
                    }}</v-icon>
                </v-avatar>
            </template>

            <template v-slot:title>
                <slot name="title">
                    <div class="d-flex" v-if="value[chip]" style="height: 24px">
                        <div class="position-relative flex-grow-1">
                            <div
                                class="position-absolute text-truncate"
                                style="top: 0; width: 100%"
                            >
                                {{ value[title] }}
                            </div>
                        </div>

                        <v-chip
                            :color="`${theme}-lighten-5`"
                            :class="`text-${theme}`"
                            size="x-small"
                            variant="flat"
                            label
                            >{{ value[chip] }}</v-chip
                        >
                    </div>

                    <template v-else>
                        {{ value[title] }}
                    </template>
                </slot>
            </template>

            <template v-slot:subtitle>
                <slot name="subtitle">{{ value[subtitle] }}</slot>
            </template>
        </v-list-item>

        <div
            :class="`bg-${theme}-lighten-5`"
            class="d-flex position-absolute h-100 w-100 px-4 py-3"
            style="top: 0; z-index: 0"
        >
            <v-spacer></v-spacer>

            <v-btn
                :color="`${theme}`"
                size="small"
                variant="flat"
                rounded="lg"
                icon
                @click="openFormShow"
            >
                <v-icon color="white">folder</v-icon>
            </v-btn>

            <v-btn
                v-if="showDelete"
                :color="`${highlight}-darken-2`"
                class="ml-4"
                size="small"
                variant="flat"
                rounded="lg"
                icon
            >
                <v-icon color="white">delete</v-icon>
            </v-btn>
        </div>
    </div>
    <v-divider></v-divider>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "item-data",

    props: {
        chip: {
            type: String,
            default: "chip",
        },

        icon: {
            type: String,
            default: null,
        },

        showDelete: {
            type: Boolean,
            default: false,
        },

        subtitle: {
            type: String,
            default: "subtitle",
        },

        title: {
            type: String,
            default: "name",
        },

        value: {
            type: Object,
            default: () => ({}),
        },
    },

    setup() {
        const store = usePageStore();

        const { highlight, page, theme } = storeToRefs(store);

        const { openFormShow } = store;

        return {
            highlight,
            page,
            theme,

            openFormShow,
        };
    },
};
</script>
