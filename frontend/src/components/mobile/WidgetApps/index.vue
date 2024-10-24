<template>
    <v-card :max-width="maxWidth" class="text-center mx-auto" rounded="md" flat>
        <v-card-text class="pa-1 text-center">
            <v-avatar
                :color="color"
                style="font-size: 16px"
                size="56"
                rounded="pill"
            >
                <v-icon :color="highlight" :icon="icon" size="large"></v-icon>
            </v-avatar>
        </v-card-text>

        <v-card-text class="pa-1" style="max-height: 38px">
            <div
                class="text-caption font-weight-default d-flex align-center justify-center overflow-hidden w-100"
                style="
                    line-height: 1.1em;
                    height: 24px;
                    font-size: 11px !important;
                "
            >
                {{ label }}
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    name: "widget-apps",

    props: {
        color: String,
        highlight: String,
        icon: String,
        label: String,
        maxWidth: String,
    },
};
</script>
