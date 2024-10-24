<template>
    <v-hover>
        <template v-slot:default="{ isHovering, props }">
            <v-sheet
                v-bind="props"
                class="d-flex flex-column align-center py-2"
                @click="$emit('click')"
                flat
            >
                <v-avatar
                    :color="`${color}`"
                    :class="[
                        `text-${highlight}`,
                        isHovering ? 'elevation-8' : 'elevation-2',
                    ]"
                    :rounded="rounded"
                    class="position-relative d-flex align-center justify-center outer cursor-pointer"
                    size="72"
                >
                    <v-avatar
                        class="position-relative"
                        :color="`${color}`"
                        :rounded="rounded"
                        size="68"
                        style="z-index: 1"
                    >
                        <v-icon
                            class="position-relative v-icon--wght-250"
                            :color="highlight"
                            :icon="icon"
                            :size="32"
                            style="z-index: 1"
                        ></v-icon>
                    </v-avatar>
                </v-avatar>

                <div
                    class="text-caption font-weight-default d-flex justify-center pt-3 w-100"
                    style="line-height: 1.1em"
                >
                    {{ label }}
                </div>
            </v-sheet>
        </template>
    </v-hover>
</template>

<script>
export default {
    name: "widget-apps",

    props: {
        color: String,
        highlight: String,
        icon: String,
        label: String,
        maxWidth: {
            type: String,
            default: "96",
        },
        rounded: {
            type: String,
            default: "pill",
        },
    },

    emits: {
        click: null,
    },
};
</script>

<style>
.v-avatar.outer::after {
    opacity: 0;
    content: "";
    position: absolute;
    display: block;
    width: 56px;
    height: 72px;
    transform: rotate(0deg) translateY(-50%);
    background: linear-gradient(90deg, transparent, currentColor);
    transition: opacity 300ms;
    animation: rotation_9019 3000ms infinite linear;
    animation-play-state: paused;
    z-index: 0;
}

.v-avatar.outer::before {
    opacity: 0;
    content: "";
    position: absolute;
    display: block;
    width: 56px;
    height: 72px;
    transform: rotate(0deg) translateY(50%);
    background: linear-gradient(90deg, currentColor, transparent);
    transition: opacity 300ms;
    animation: rotation_9018 3000ms infinite linear;
    animation-play-state: paused;
    z-index: 0;
}

.v-avatar.outer:hover::after,
.v-avatar.outer:hover::before {
    opacity: 0.6;
    animation-play-state: running;
}

@keyframes rotation_9018 {
    0% {
        transform: rotate(0deg) translateY(50%);
    }

    100% {
        transform: rotate(360deg) translateY(50%);
    }
}
@keyframes rotation_9019 {
    0% {
        transform: rotate(0deg) translateY(-50%);
    }

    100% {
        transform: rotate(360deg) translateY(-50%);
    }
}
</style>
