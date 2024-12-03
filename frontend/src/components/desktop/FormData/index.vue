<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        :order="1"
        height="72"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-btn
            icon
            v-if="parentName || navbackTo"
            @click="
                navbackTo
                    ? $router.push({ name: navbackTo })
                    : $router.push({ name: parentName })
            "
        >
            <v-icon>arrow_back</v-icon>
        </v-btn>

        <v-app-bar-nav-icon
            v-else
            @click="railMode = !railMode"
        ></v-app-bar-nav-icon>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name ?? module.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn
            icon="folder_open"
            v-if="hasSelected"
            @click="openFormShow"
        ></v-btn>

        <v-btn
            :disabled="hasSelected"
            icon
            @click="sidenavState = !sidenavState"
        >
            <v-icon>{{ sidenavState ? "close" : "tune" }}</v-icon>
        </v-btn>
    </v-app-bar>

    <page-filter :withSync="withSync">
        <template v-slot:feed>
            <slot name="feed" :theme="theme"></slot>
        </template>

        <template v-slot:sync="{ mapResponseData, parent }">
            <slot
                name="sync"
                :mapResponseData="mapResponseData"
                :params="params"
                :parent="parent"
                :store="store"
                :theme="theme"
            ></slot>
        </template>

        <template v-slot:info>
            <slot name="info" :store="store" :theme="theme"></slot>
        </template>

        <template v-slot:filter>
            <slot name="filter" :store="store" :theme="theme"></slot>
        </template>

        <template v-slot:icon>
            <slot name="icon" :store="store" :theme="theme"></slot>
        </template>
    </page-filter>

    <v-main style="min-height: 100dvh">
        <v-container class="pt-0 h-100">
            <page-paper :text-left="title" max-width="100%">
                <v-data-table-server
                    v-model="selected"
                    :headers="headers"
                    :items="records"
                    :items-length="totalRecords"
                    :items-per-page="itemsPerPage"
                    :loading="loading"
                    density="comfortable"
                    height="calc(100vh - 222px)"
                    item-value="name"
                    select-strategy="single"
                    fixed-footer
                    fixed-header
                    return-object
                    show-select
                    @update:options="loadItems"
                >
                    <template
                        v-slot:item="{
                            internalItem,
                            isSelected,
                            toggleSelect,
                            item,
                            index,
                        }"
                    >
                        <slot
                            name="tableRow"
                            :headers="headers"
                            :item="item"
                            :index="index"
                            :internalItem="internalItem"
                            :isSelected="isSelected"
                            :toggleSelect="toggleSelect"
                        >
                            <item-data
                                :headers="headers"
                                :item="item"
                                :index="index"
                                :internalItem="internalItem"
                                :isSelected="isSelected"
                                :toggleSelect="toggleSelect"
                            ></item-data>
                        </slot>
                    </template>
                </v-data-table-server>
            </page-paper>
        </v-container>

        <v-fab
            v-if="!hideAddnew && !hasSelected"
            class="me-9"
            color="deep-orange"
            icon="add"
            location="end"
            size="64"
            absolute
            appear
            style="bottom: 95px"
            @click="openFormCreate"
        ></v-fab>
    </v-main>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-data",

    props: {
        chip: {
            type: String,
            default: "chip",
        },

        hideAddnew: Boolean,

        maxWidth: {
            type: [String, Number],
            default: "900",
        },

        navbackTo: String,

        subtitle: {
            type: String,
            default: "subtitle",
        },

        showDelete: {
            type: Boolean,
            default: false,
        },

        withSync: Boolean,
    },

    setup() {
        const store = usePageStore();

        store.helpState = false;

        const {
            formStateLast,
            hasSelected,
            headers,
            highlight,
            itemsPerPage,
            loading,
            meta,
            module,
            navigationState,
            page,
            pageKey,
            params,
            parentName,
            record,
            records,
            recordBase,
            railMode,
            sidenavState,
            selected,
            title,
            theme,
            totalRecords,
        } = storeToRefs(store);

        const { getPageDatas, openFormCreate, openFormShow, setSelected } =
            store;

        return {
            formStateLast,
            hasSelected,
            headers,
            highlight,
            itemsPerPage,
            loading,
            meta,
            module,
            navigationState,
            page,
            pageKey,
            params,
            parentName,
            record,
            records,
            recordBase,
            railMode,
            sidenavState,
            selected,
            title,
            theme,
            totalRecords,

            getPageDatas,
            openFormCreate,
            openFormShow,
            setSelected,

            store,
        };
    },

    beforeUnmount() {
        if (this.parentName || this.navbackTo) {
            this.navigationState = true;
        }
    },

    methods: {
        // clickOnRow: function (event, { item }) {
        //     this.setSelected(item);
        // },

        loadItems: function (tableOptions) {
            this.getPageDatas(tableOptions);
        },
    },

    watch: {
        params: {
            handler: function (newOptions) {
                this.getPageDatas(newOptions);
            },

            deep: true,
            immediate: true,
        },

        selected: {
            handler: function (selected) {
                this.record =
                    selected.length > 0
                        ? JSON.parse(JSON.stringify(selected[0]))
                        : JSON.parse(JSON.stringify(this.recordBase));
            },

            deep: true,
            immediate: true,
        },
    },
};
</script>
