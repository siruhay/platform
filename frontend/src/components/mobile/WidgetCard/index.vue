<template>
    <v-sheet
        :class="vertical ? 'flex-column' : null"
        color="grey-lighten-5"
        class="d-flex align-center justify-center pa-3 border-thin"
        rounded
    >
        <slot></slot>
    </v-sheet>
</template>

<script>
export default {
    name: "widget-card",

    props: {
        vertical: Boolean,
    },
};
</script>
