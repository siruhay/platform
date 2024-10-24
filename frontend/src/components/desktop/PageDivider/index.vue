<template>
    <div class="d-block w-100">
        <v-divider>
            <v-chip
                :color="theme"
                :class="{ 'text-uppercase': uppercase }"
                class="text-white"
                density="comfortable"
                size="small"
                variant="flat"
            >
                {{ label }}
            </v-chip>
        </v-divider>
    </div>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "page-divider",

    props: {
        label: String,
        uppercase: Boolean,
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
