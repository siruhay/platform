<template>
    <template v-if="logs && Array.isArray(logs) && logs.length > 0">
        <div class="text-overline mt-8">activity</div>
        <v-divider></v-divider>
    </template>

    <v-timeline align="start" density="compact" side="end">
        <v-timeline-item
            v-for="(item, itemIndex) in logs"
            :dot-color="item.color"
            :key="itemIndex"
            size="32"
            fill-dot
        >
            <template v-slot:icon>
                <v-icon color="white" size="small">{{ item.icon }}</v-icon>
            </template>

            <div class="d-flex">
                <div
                    class="text-caption font-weight-medium me-2"
                    style="width: 60px"
                >
                    {{ item.event }}
                </div>

                <div class="flex-grow-1">
                    <div
                        class="text-caption font-weight-medium text-grey-darken-1"
                    >
                        {{ item.updated_at }}
                    </div>
                    <div class="text-caption text-grey-darken-1">
                        {{ item.username }}
                    </div>
                </div>

                <v-icon
                    v-if="item.dirties || item.origins"
                    color="grey"
                    @click="item.state = !item.state"
                    >{{
                        item.state ? "highlight_off" : "expand_circle_down"
                    }}</v-icon
                >
            </div>

            <v-expand-transition>
                <v-sheet style="max-width: 216px" v-show="item.state">
                    <div
                        class="text-caption text-grey-darken-1 font-weight-medium me-0 mt-3"
                    >
                        <small>ID - {{ item.id }}</small>
                    </div>

                    <v-divider></v-divider>

                    <template v-if="item.dirties && item.state">
                        <div
                            class="text-caption text-grey-darken-1 font-weight-medium me-2 mt-3"
                            style="width: 60px"
                        >
                            <small>CHANGES</small>
                        </div>

                        <v-divider></v-divider>

                        <div
                            v-for="(value, name, index) in item.dirties"
                            :key="index"
                            class="d-flex w-100 text-grey-darken-1 pt-1"
                            style="font-size: 9px"
                        >
                            <div style="min-width: 68px">
                                {{ name }}
                            </div>

                            <div
                                class="d-block field-value overflow-hidden w-100"
                                role="textbox"
                                style="max-height: 48px; outline: none"
                            >
                                {{ value }}
                            </div>
                        </div>
                    </template>

                    <template v-if="item.origins && item.state">
                        <div
                            class="text-caption text-grey-darken-1 font-weight-medium me-2 mt-3"
                            style="width: 60px"
                        >
                            <small>ORIGINS</small>
                        </div>

                        <v-divider></v-divider>

                        <div
                            v-for="(value, name, index) in item.origins"
                            :key="index"
                            class="d-flex w-100 text-grey-darken-1 pt-1"
                            style="font-size: 9px"
                        >
                            <div style="min-width: 68px">
                                {{ name }}
                            </div>

                            <div
                                class="d-block field-value overflow-hidden w-100"
                                role="textbox"
                                style="max-height: 48px; outline: none"
                            >
                                {{ value }}
                            </div>
                        </div>
                    </template>
                </v-sheet>
            </v-expand-transition>
        </v-timeline-item>
    </v-timeline>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "widget-activity",

    setup() {
        const store = usePageStore();

        const { logs } = storeToRefs(store);

        return {
            logs,
        };
    },
};
</script>
