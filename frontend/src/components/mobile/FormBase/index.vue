<template>
    <v-sheet class="position-relative" color="transparent">
        <v-sheet
            class="position-absolute text-center w-100"
            color="transparent"
            style="z-index: 1; top: -36px"
        >
            <div class="d-flex justify-center position-relative">
                <form-icon></form-icon>

                <div
                    :class="`text-${theme}-lighten-3`"
                    class="text-caption text-white position-absolute py-1 font-weight-bold text-uppercase"
                    style="font-size: 0.7rem !important; top: 0; right: 0"
                >
                    {{ title }}
                </div>
            </div>
        </v-sheet>

        <v-sheet class="mt-9 pt-9" elevation="1" rounded="lg">
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
