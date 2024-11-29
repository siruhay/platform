<template>
    <v-app-bar
        :color="`${theme}`"
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

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name ?? module.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn
            v-if="!hideAddnew && !hasSelected"
            :color="highlight"
            icon
            @click="openFormCreate"
        >
            <v-icon class="with-shadow">add</v-icon>
        </v-btn>

        <v-btn
            :disabled="hasSelected"
            :color="sidenavState ? 'white' : `${theme}-lighten-3`"
            icon
            @click="sidenavState = !sidenavState"
        >
            <v-icon class="with-shadow">filter_list</v-icon>
        </v-btn>
    </v-app-bar>

    <v-sheet
        :color="`${theme}`"
        class="mx-auto position-fixed w-100 rounded-b-xl"
        height="256"
    ></v-sheet>

    <v-main>
        <v-sheet class="bg-transparent position-relative px-4 pt-9 pb-4">
            <v-sheet
                class="position-absolute"
                color="transparent"
                width="calc(100% - 32px)"
                style="top: 0; z-index: 1"
            >
                <div class="d-flex justify-center">
                    <form-icon></form-icon>

                    <div
                        :class="`text-${theme}-lighten-4`"
                        class="text-caption text-white position-absolute font-weight-bold text-uppercase text-left"
                        style="
                            top: 8px;
                            left: 0;
                            font-size: 0.63rem !important;
                            width: calc(50% - 30px);
                        "
                    >
                        <div
                            class="d-inline-block text-truncate"
                            style="max-width: 100%"
                        >
                            {{ title }}
                        </div>
                    </div>

                    <div
                        :class="`text-${theme}-lighten-4`"
                        class="text-caption text-white position-absolute font-weight-bold text-uppercase text-right"
                        style="
                            font-size: 0.63rem !important;
                            top: 8px;
                            right: 0;
                            width: calc(50% - 30px);
                        "
                    >
                        <div
                            class="d-inline-block text-truncate"
                            style="max-width: 100%"
                        >
                            SEMUA DATA
                        </div>
                    </div>
                </div>
            </v-sheet>

            <v-sheet
                :min-height="
                    parentName || navbackTo
                        ? `calc(100dvh - 116px)`
                        : `calc(100dvh - 172px)`
                "
                class="position-relative pt-7"
                elevation="1"
                rounded="lg"
                flat
            >
                <v-list
                    class="bg-transparent overflow-y-auto overflow-x-hidden scrollbar-none"
                    :active-class="
                        showDelete
                            ? `bg-white elevation-6 text-grey with-delete`
                            : `bg-white elevation-6 text-grey`
                    "
                    lines="two"
                    selectable
                    @update:selected="setSelected"
                >
                    <template v-for="(record, index) in records">
                        <slot
                            name="listItem"
                            :index="index"
                            :record="record"
                            :showDelete="showDelete"
                            :theme="theme"
                        >
                            <item-data
                                :chip="chip"
                                :subtitle="subtitle"
                                :show-delete="showDelete"
                                :value="record"
                            ></item-data>
                        </slot>
                    </template>

                    <template v-if="records.length <= 0">
                        <slot>
                            <div
                                class="d-flex align-center justify-center text-body-2 text-center text-grey"
                                style="height: calc(100dvh - 216px)"
                            >
                                Data tidak ditemukan
                            </div>
                        </slot>
                    </template>

                    <template
                        v-if="
                            meta.current_page &&
                            meta.current_page < meta.last_page
                        "
                    >
                        <v-list-item v-intersect="onIntersect"></v-list-item>
                    </template>
                </v-list>
            </v-sheet>
        </v-sheet>
    </v-main>

    <page-filter :withSync="withSync">
        <template v-slot:syncdata="{ mapResponseData, parent }">
            <slot
                name="syncdata"
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

        <template v-slot:default>
            <slot name="help" :store="store" :theme="theme"></slot>
        </template>

        <template v-slot:utility>
            <slot name="utility" :store="store" :theme="theme"></slot>
        </template>
    </page-filter>
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

    setup(props) {
        const store = usePageStore();

        store.helpState = false;

        if (store.parentName || props.navbackTo) {
            store.navigationState = false;
        }

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
            records,
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
            records,
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
        onIntersect: function (isIntersecting) {
            if (isIntersecting) {
                this.loading = true;
                this.params.page = this.params.page + 1;
            }
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
    },
};
</script>
