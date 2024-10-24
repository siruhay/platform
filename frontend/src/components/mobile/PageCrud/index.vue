<template>
    <router-view key="pagecrud" />
</template>

<script>
import { usePageStore } from "@pinia/pageStore";

export default {
    name: "page-crud",

    props: {
        pagePath: {
            type: String,
            default: null,
        },

        pageName: {
            type: String,
            default: null,
        },

        pageKey: {
            type: String,
            default: null,
        },

        parentName: {
            type: String,
            default: null,
        },

        parentKey: {
            type: String,
            default: null,
        },
    },

    setup(props) {
        const store = usePageStore();

        store.pageKey = props.pageKey;
        store.pageName = props.pageName;
        store.pagePath = props.pagePath;
        store.parentKey = props.parentKey;
        store.parentName = props.parentName;

        const { initPage } = store;

        return {
            initPage,
        };
    },

    created() {
        this.initPage();
    },
};
</script>
