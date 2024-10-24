<template>
    <widget-card>
        <v-avatar :color="`${color}-lighten-5`" size="40">
            <v-icon :color="color">{{ icon }}</v-icon>
        </v-avatar>

        <div
            :class="vertical ? 'pt-3' : 'pl-3'"
            class="d-flex flex-column flex-grow-1"
        >
            <div class="text-h6 font-weight-medium" style="line-height: 1">
                {{ value }}
            </div>
            <div
                class="text-caption text-grey-darken-1 mt-1"
                style="line-height: 1; font-size: 10px !important"
            >
                {{ label }}
            </div>
        </div>
    </widget-card>
</template>

<script>
export default {
    name: "widget-number",

    props: {
        color: {
            type: String,
            default: "green",
        },

        icon: String,
        label: String,
        value: String,
        vertical: Boolean,
    },
};
</script>
