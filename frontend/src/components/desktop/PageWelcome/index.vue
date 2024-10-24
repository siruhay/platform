<template>
    <v-layout :class="`bg-blue-grey-lighten-5`">
        <v-main class="position-relative welcome-page playing">
            <div class="wave"></div>
            <div class="wave"></div>
            <div class="wave"></div>

            <v-container
                class="d-flex align-center justify-center"
                style="min-height: 100dvh"
            >
                <v-sheet color="transparent" max-width="840" width="100%">
                    <v-sheet
                        color="rgba(255,255,255,0.95)"
                        class="position-relative pa-8"
                        rounded="xl"
                        width="100%"
                        style="padding-top: 112px !important"
                    >
                        <div
                            class="position-absolute d-flex align-center"
                            style="top: 24px"
                        >
                            <img
                                src="/assets/pwa-76x76.png"
                                style="margin-left: -2px"
                            />

                            <div class="ml-2 text-h4 font-brand">
                                SiMAS<span class="sub-color">TEN</span>
                            </div>
                        </div>

                        <slot
                            :authenticate="authenticate"
                            :challenge="challenge"
                            :finduser="finduser"
                            :resetpass="resetpass"
                        ></slot>
                    </v-sheet>
                </v-sheet>

                <v-sheet
                    color="transparent"
                    class="position-absolute w-100"
                    style="bottom: 0px"
                >
                    <v-row no-gutters>
                        <v-col cols="12">
                            <div
                                class="d-flex justify-center text-grey-darken-1 pb-2"
                            >
                                <div class="text-caption text-uppercase">
                                    version 3.0.0
                                </div>
                                <div class="ml-4 text-caption text-uppercase">
                                    Datin BKD &copy; 2024
                                </div>
                            </div>
                        </v-col>
                    </v-row>
                </v-sheet>
            </v-container>
        </v-main>
    </v-layout>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";

export default {
    name: "PageWelcome",

    setup() {
        const store = usePageStore();

        const {
            authenticate,
            challenge,
            finduser,
            getSanctumCSRF,
            resetpass,
            theme,
        } = store;

        return {
            authenticate,
            challenge,
            finduser,
            getSanctumCSRF,
            resetpass,
            theme,
        };
    },

    created() {
        this.getSanctumCSRF({ mobile: false });
    },
};
</script>
