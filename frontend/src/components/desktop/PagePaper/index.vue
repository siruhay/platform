<template>
    <v-sheet
        :max-width="maxWidth"
        class="bg-transparent mx-auto position-relative px-0 pt-9 pb-4"
    >
        <v-sheet
            class="position-absolute"
            color="transparent"
            width="100%"
            style="top: 0px; z-index: 1"
        >
            <div class="d-flex justify-center">
                <form-icon>
                    <v-img
                        v-if="userAvatar"
                        :src="
                            auth.avatar ?? `/avatars/${auth.gender}-avatar.svg`
                        "
                    ></v-img>
                </form-icon>

                <div
                    :class="`text-${theme}-lighten-4`"
                    class="text-caption text-white position-absolute font-weight-medium text-uppercase text-left"
                    style="top: 8px; left: 0; width: calc(50% - 30px)"
                >
                    <div
                        class="d-inline-block text-truncate text-grey-darken-1"
                        style="max-width: 100%"
                    >
                        {{ textLeft }}
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
                        {{ textRight }}
                    </div>
                </div>
            </div>
        </v-sheet>

        <v-sheet class="mx-auto position-relative pt-9" rounded="lg" flat>
            <v-card-text class="text-center" v-if="userAvatar">
                <div class="text-body-2 font-weight-medium text-uppercase">
                    {{ auth.username }}
                </div>
                <div class="text-caption">{{ auth.usermail }}</div>
            </v-card-text>

            <slot></slot>
        </v-sheet>
    </v-sheet>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "page-paper",

    props: {
        textLeft: String,
        textRight: String,
        maxWidth: {
            type: [String, Number],
            default: "100%",
        },
        userAvatar: Boolean,
    },

    setup() {
        const store = usePageStore();

        const { auth, theme } = storeToRefs(store);

        return {
            auth,
            theme,
        };
    },
};
</script>
