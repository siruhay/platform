<template>
    <v-sheet class="position-relative" color="transparent">
        <v-sheet
            class="position-absolute text-center w-100"
            color="transparent"
            style="z-index: 1; top: -28px"
        >
            <div class="d-flex justify-center position-relative">
                <v-sheet :color="`${theme}`" elevation="4" rounded="pill">
                    <v-card-text class="pa-1">
                        <v-avatar
                            :color="highlight"
                            size="48"
                            style="font-size: 22px"
                        >
                            <v-icon :color="iconColor ?? 'white'">{{
                                icon
                            }}</v-icon>
                        </v-avatar>
                    </v-card-text>
                </v-sheet>

                <div
                    :class="`text-${theme}-lighten-4`"
                    class="text-caption text-white position-absolute py-1 font-weight-bold text-uppercase"
                    style="font-size: 0.7rem !important; top: 0; right: 0"
                >
                    {{ title }}
                </div>
            </div>
        </v-sheet>

        <v-sheet class="mt-7 pt-7" elevation="1" rounded="lg">
            <slot></slot>
        </v-sheet>
    </v-sheet>
</template>

<script>
export default {
    name: "form-base",

    props: {
        highlight: String,
        icon: String,
        iconColor: String,
        theme: String,
        title: String,
    },
};
</script>
