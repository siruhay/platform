<template>
    <tr>
        <td style="padding-top: 16px; vertical-align: baseline">
            <slot name="prepend">
                <v-avatar size="32" :color="`${theme}-lighten-1`">
                    <v-icon size="small">{{ icon }}</v-icon>
                </v-avatar>
            </slot>
        </td>

        <td style="padding-top: 16px">
            <span class="text-caption font-weight-medium">{{ title }}</span>

            <span class="d-block text-caption text-grey-darken-1">
                <slot>
                    {{ subtitle }}
                </slot>
            </span>
        </td>
    </tr>
</template>

<script>
export default {
    name: "help-list-item",

    props: {
        icon: String,
        theme: String,
        title: String,
        subtitle: String,
    },
};
</script>
