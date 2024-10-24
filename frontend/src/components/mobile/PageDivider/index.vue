<template>
    <v-divider>
        <v-chip
            :color="theme"
            class="text-white"
            density="comfortable"
            size="small"
            variant="flat"
        >
            {{ label }}
        </v-chip>
    </v-divider>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "page-divider",

    props: {
        label: String,
    },

    setup() {
        const store = usePageStore();

        const { theme } = storeToRefs(store);

        return {
            theme,
        };
    },
};
</script>
