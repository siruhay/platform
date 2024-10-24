<template>
    <table class="w-100" style="border-spacing: 0px">
        <colgroup>
            <col width="48px" />
            <col width="auto" />
        </colgroup>

        <tbody>
            <slot></slot>
        </tbody>
    </table>
</template>

<script>
export default {
    name: "help-list",
};
</script>
