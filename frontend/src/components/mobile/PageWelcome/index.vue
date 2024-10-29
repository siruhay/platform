<template>
    <v-layout :class="`bg-blue-grey-lighten-5`">
        <v-main class="position-relative welcome-page playing">
            <div class="wave"></div>
            <div class="wave"></div>
            <div class="wave"></div>

            <v-sheet
                color="transparent"
                class="mx-auto text-center position-relative px-8 pb-9"
                height="100dvh"
                width="100dvw"
                style="padding-top: 216px !important"
            >
                <div
                    class="position-absolute d-flex flex-column align-center w-100"
                    style="left: 0; top: 48px"
                >
                    <img
                        src="/assets/pwa-192x192.png"
                        style="height: auto; width: 72px"
                    />

                    <div class="mt-2 mb-2 text-h3 font-brand">
                        Si<span class="sub-color">RUHAY</span>
                    </div>
                </div>

                <v-sheet
                    color="rgba(255, 255, 255, 0.95)"
                    rounded="lg"
                    class="mx-auto overflow-hidden pa-4"
                    width="100%"
                    height="auto"
                >
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
                                version 1.0.0
                            </div>
                            <div class="ml-4 text-caption text-uppercase">
                                DPMPD &copy; 2024
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-sheet>
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
        this.getSanctumCSRF({ mobile: true });
    },
};
</script>
